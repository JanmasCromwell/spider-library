<?php

namespace App\Library\Wechat\Sender;

class Text extends Sender
{
    protected function format($to, $from, $content)
    {
        $data = [
            'ToUserName'   => $to,
            'FromUserName' => $from,
            'Content'      => (string)$content,
            'MsgType'      => $this->msgType,
            'CreateTime'   => time()
        ];

        return $data;
    }
}