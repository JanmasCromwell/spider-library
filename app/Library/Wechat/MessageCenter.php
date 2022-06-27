<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Wechat;


use App\Library\Wechat\Sender\Text;

class MessageCenter
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        if (isset($this->data['echostr'])) {
            return $this->signature();
        }
        return $this->responseMessage();
    }

    protected function signature()
    {

        $signature = $this->data['signature'];
        $timestamp = $this->data['timestamp'];
        $nonce = $this->data['nonce'];
        $echoStr = $this->data['echostr'];

        $token = 'JanmasCromwell';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return $echoStr;
        }
        return false;
    }

    public function responseMessage()
    {
        $msgType = $this->data['MsgType'];
        if ($msgType === 'event') {
            $msgType = $this->data['Event'];
        }
        //检测msgId是否已经处理
        $namespace = '\App\Library\Wechat\Message\\';
        $class = $namespace . ucfirst($msgType);
        if (class_exists($class)) {
            $response = (new $class)->handle($this->data);
            return $response;
        } else {
            return (new Text())->handle($this->data['FromUserName'], $this->data['ToUserName'], '【收到不支持的消息类型，暂无法显示】');
        }

    }
}
