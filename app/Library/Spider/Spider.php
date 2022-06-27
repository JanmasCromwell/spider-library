<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Spider;

use App\Library\Action\Producter\BookNextPageProducter;
use App\Library\Action\Producter\BookProducter;
use App\Library\Client\Logger;
use App\Library\Spider\Proxy\Center;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Spider
{
    protected $baseHttpClient;
    protected $url;
    protected $header;
    protected $timeOut;

    public function __construct(string $url = '', int $timeOut = 15)
    {

        //根据不同的书源初始化不同的环境 ,在环境中获取baseHttpClient 并增加不同的headers 以及不同的解析类
        $this->url = $url;
        $this->timeOut = $timeOut;
        $this->header = Center::generate();
        $this->baseHttpClient = new Client([
            'base_uri' => $this->url,
            'timeout'  => $this->timeOut,
            'headers'  => $this->header
        ]);
    }

    public function done(string $bookName, $arg = [])
    {
        $uri = $this->buildUri($bookName, $arg);
        try {
            $response = $this->baseHttpClient->get($uri, [
                'timeout' => $this->timeOut,
                'headers' => $this->header
            ]);
        } catch (RequestException $e) {
            Logger::writeError('获取图书列表失败', [
                'keyword' => $bookName,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return false;
        }

        if ($response->getStatusCode() != 200) {
            Logger::writeError('抓取图书列表失败');
            return false;
        }

        $body = $response->getBody()->getContents();
        preg_match_all('/<a href="(.*)" style="text-decoration: underline;">(.*)<\/a>/', $body, $matches);
        $nameMaps = array_pop($matches);
        $linkMaps = array_pop($matches);

        $map = [];
        foreach ($nameMaps as $key => $value) {
            $map[] = [
                'link' => $linkMaps[$key],
                'name' => $value
            ];
        }
        if (empty($map)) {
            return false;
        }
        //时间锚点往蜘蛛队列放数据
        BookProducter::done(['domain' => $this->url, 'data' => $map]);

        unset($matches);
        //匹配下一页
        preg_match_all('/<noscript>\n([\s]+)<a href="(.*)">next page<\/a>/', $body, $matches);
        $tmpUrlMap = array_pop($matches);
        $url = urldecode(array_shift($tmpUrlMap));
        $arg = substr($url, strripos($url, '?') + 1);
        $arg = explode('=', $arg);
        if (!empty($arg[1])) {
            BookNextPageProducter::done(['domain' => $this->url, 'name' => $bookName, 'arg' => $arg]);
        }
        return true;
    }

    protected function buildUri($bookName, $arg = [])
    {
        $uri = '/s/' . $bookName;
        if (empty($arg)) {
            return $uri;
        }
        $uri .= '?' . implode('=', $arg);

        return $uri;
    }
}
