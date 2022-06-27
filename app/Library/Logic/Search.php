<?php

namespace App\Library\Logic;


use App\Library\Spider\Spider;
use App\Model\Books;

class Search extends Logic
{

    public function done(string $payload): string
    {
        if (Books::isLikeExists($payload) > 0) {
            $content = "已为您找到以下书籍\r\n";
            $bookList = Books::query()->where('name', 'like', "%{$payload}%")->limit(3)->get();
            foreach ($bookList as $value) {
                $content .= "<a href='https://www.baidu.com?id={$value['id']}'>" . $value['name'] . "-作者：aa</a>\r\n";
            }
        } else {
            //TODO:去图书站点窃书 然后存储
            $books = (new Spider('https://zh.1lib.world/'))->done($payload);
            if ($books === false) {
                $content = "您搜索的【{$payload}】未找到，请访问";
            } else {
                $content = '您搜索的图书正在整理，请大约1分钟后向本公众号发送[命令:进度查询@图书名]即可查看图书整理进度，若已完成搜集则会返回相应图书列表';
            }
        }

        return $content;

    }
}
