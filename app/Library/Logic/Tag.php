<?php

namespace App\Library\Logic;

use App\Library\Wechat\Sender\Text;

class Tag extends Logic
{

    public function done(string $payload): string
    {
        $content = <<<CONTENT
您搜索的【{$payload}】未找到，请访问https://www.baidu.com
CONTENT;
        return (new Text())->handle($this->data['FromUserName'], $this->data['ToUserName'], $content);
    }
}
