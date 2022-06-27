<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Action\Consumer;

use _PHPStan_76800bfb5\Nette\Neon\Exception;
use App\Library\Client\CosClient;
use App\Library\Client\Logger;
use App\Library\Spider\Proxy\Center;
use App\Model\Books;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class BooksConsumer extends Consumer
{

    static function done(?array $context)
    {
        $name = $context['name'];
        $domain = $context['domain'];
        $link = $context['link'];

        $headers = Center::generate();
        $headers['Accept-Encoding'] = 'gzip, deflate, br';

        $client = new Client([
            'base_uri' => $domain,
            'headers'  => $headers,
        ]);
        echo "开始访问图书[{$name}]" . PHP_EOL;
        try {
            //请求详情页
            $response = $client->get($link);
        } catch (\Exception $e) {
            Logger::writeError("获取图书详情页异常", [
                'name'    => $name,
                'message' => $e->getMessage(),
                'file'    => $e->getMessage(),
                'line'    => $e->getLine(),
            ], __FILE__, __LINE__);
            return false;
        }
        Logger::writeDebug('获取图书通过');
        if ($response->getStatusCode() !== 200) {
            Logger::writeError('获取图书失败', [
                'name'        => $name,
                'status_code' => $response->getStatusCode(),
                'headers'     => $response->getHeaders(),
                'body'        => $response->getBody()->getContents(),
                'size'        => $response->getBody()->getSize(),
                'context'     => $context
            ], __FILE__, __LINE__);
            return false;
        }

        //根据详情页匹配下载链接
        $html = $response->getBody()->getContents();
        ##匹配书的后缀
        preg_match_all('/<i class="zlibicon-download"><\/i>\n([\s]+)(.*)/', $html, $extensionMatches);
        $tmpExtesionArray = array_pop($extensionMatches);
        $tmp = array_pop($tmpExtesionArray);
        $begin = strpos($tmp, '(');
        $end = strpos($tmp, ',');
        $fileExtension = substr($tmp, $begin + 1, $end - $begin - 1);
        $fileName = $name . '.' . $fileExtension;

        #TODO:匹配作者、年、语言、种类、文件、页、简述


        $create = [
            'name'        => $name,
            'create_time' => time(),
            'ext'         => $fileExtension
        ];

        # 检查文件资源是否已经上传COS
        $result = CosClient::getInstance()->doesObjectExist(env('COS_BUCKET'), $fileName);
        if ($result) {
            try {
                $COSObjectFileInfo = CosClient::getInstance()->HeadObject([
                    'Bucket' => env('COS_BUCKET'),
                    'Key'    => $fileName
                ]);
            } catch (\Exception $e) {
                Logger::writeError('获取COS书本信息失败', [
                    'name'    => $name,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ], __FILE__, __LINE__);

                return false;
            }
            Logger::writeDebug('图书在COS中已存在');

            //检查数据库中是否存在
            $bookLink = Books::where('name', '=', $name)->where('link', '=', $result['Location'])->get();
            if ($bookLink) {
                return true;
            }
            try {
                $create['link'] = CosClient::getInstance()->getObjectUrlWithoutSign(env('COS_BUCKET'), $fileName);
            } catch (\Exception $e) {
                Logger::writeError('获取书本下载地址失败', [
                    'name'    => $name,
                    'message' => $e->getMessage(),
                    'trace'   => $e->getTraceAsString()
                ], __FILE__, __LINE__);
                return false;
            }
            Logger::writeDebug('图书COS下载地址获取成功');
            $create['etag'] = trim($COSObjectFileInfo['ETag']);
            $create['size'] = $COSObjectFileInfo['ContentLength'];
            $create['mime'] = $COSObjectFileInfo['ContentType'];

            return Books::create($create);
        }
        Logger::writeDebug('图书未上传COS');
        //去下载书
        $pattern = '/<a class="btn btn-primary dlButton addDownloadedBook" href="(.*)" target="[\w]*" data-book_id="[\d]*" data-isbn="[\d|\w]*"/';
        preg_match_all($pattern, $html, $matches);
        $downloadUrlMap = array_pop($matches);
        if (!$downloadUrlMap) {
            $htmlFile = BASE_PATH . '/runtime/error/正则匹配_' . date('Y/m/d/H/i/s') . '_' . uniqid() . '.html';
            file_put_contents($htmlFile, $html);
            Logger::writeError('正则匹配图书下载链接失败', [
                'name'   => $name,
                'notice' => '内容已保存到' . $htmlFile
            ], __FILE__, __LINE__);
            return false;
        }
        $downloadUrl = array_pop($downloadUrlMap);

        try {
            $tmpfile = BASE_PATH . '/runtime/download/tmp/' . uniqid();
            $dirname = dirname($tmpfile);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }
            touch($tmpfile);
            $resource = fopen($tmpfile, 'w+');
//            $stream = stream_for($resource);
            //请求图书下载数据
            $sourceResponse = $client->get($downloadUrl, [
                RequestOptions::SINK => $resource
            ]);
        } catch (\Exception $e) {
            Logger::writeError('下载图书失败', [
                'name'        => $name,
                'message'     => $e->getMessage(),
                'downloadurl' => $downloadUrl,
                'file'        => $e->getFile(),
                'line'        => $e->getLine()
            ], __FILE__, __LINE__);
            echo "图书下载失败[{$name}],原因:" . $e->getMessage() . PHP_EOL;
            fclose($resource);
            unlink($tmpfile);
            return false;
        }

        Logger::writeDebug('图书下载成功');

        $headers = $sourceResponse->getHeaders();

        //上传数据
//        Logger::writeDebug('获取图书header头信息失败', ['link' => $downloadUrl, 'header' => $sourceResponse->getHeaders()]);

        $originalFileNameWithExtension = array_pop($headers['Content-Disposition']);
        $fileName = trim(mb_substr($originalFileNameWithExtension, 22, mb_strripos($originalFileNameWithExtension, ';filename') - 23));
        $fileName = str_replace(' (z-lib.org)', '', $fileName);
        $tmpMap = explode('.', $fileName);
        $contentType = array_pop($headers['Content-Type']);
        $contentLength = $headers['Content-Length'][0];

        $create['ext'] = array_pop($tmpMap);
        $create['mime'] = $contentType;
        $create['size'] = $contentLength;
        echo "获取到的文件名为[{$fileName}]" . PHP_EOL;
        echo '获取文件大小为' . $contentLength . PHP_EOL;
//            file_put_contents($saveDir . $fileName, $source);
        //检查在存储桶中是否存在
        echo '开始检查COS中是否有图书信息' . PHP_EOL;

        echo "开始上传图书{$name}的资源" . PHP_EOL;
        try {

            $cosFileUploadInfo = CosClient::getInstance()->upload(env('COS_BUCKET'), $fileName, file_get_contents($tmpfile), [
                'ContentType'   => $contentType,
                'ContentLength' => $contentLength,
            ]);
//            Logger::getInstance()->debug('文件上传后的东西', (array)$cosFileUploadInfo);
            if (!$cosFileUploadInfo) {
                throw new Exception('文件上传失败了');
//                文件上传失败
            }
            echo "文件已上传，访问地址为：{$cosFileUploadInfo['Location']}" . PHP_EOL;
            $create['link'] = $cosFileUploadInfo['Location'];
            $create['etag'] = trim($cosFileUploadInfo['ETag'], '"');
            # 清空资源并删除临时文件
            @fclose($resource);
            unlink($tmpfile);
        } catch (\Exception $e) {
            Logger::getInstance()->error("图书[{$name}]上传失败", [
                'name'      => $name,
                'message'   => $e->getMessage(),
                'code'      => $e->getCode(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'exception' => $e->getTrace()
            ]);

            fclose($resource);
            unlink($tmpfile);
            return false;
        }

        #TODO:存储书籍并存储cos的访问链接
        Books::create($create);
        echo '图书[' . $name . ']执行完毕' . PHP_EOL;
        return true;
    }
}
