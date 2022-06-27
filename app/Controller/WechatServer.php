<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Controller;

use App\Library\Wechat\MessageCenter;
use App\Library\Wechat\Verify;

class WechatServer extends AbstractController
{
    public function connect()
    {
//        $this->logger->debug(json_encode($this->request->all()));
        return (new MessageCenter($this->request->all()))->handle();
    }


}
