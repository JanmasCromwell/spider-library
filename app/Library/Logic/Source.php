<?php

namespace App\Library\Logic;

use App\Library\Wechat\Sender\Text;

class Source extends Logic
{

    public function done(string $payload):string
    {
        $content = <<<CONTENT
您提交的书源已收到，
CONTENT;
        return (new Text())->handle($this->data['FromUserName'], $this->data['ToUserName'], $content);
    }
}
