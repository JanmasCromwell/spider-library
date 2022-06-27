<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Spider\Proxy;

class Center
{
    static function generate()
    {
        $ip = Ip::generate();
        return [
            'User-Agent'      => UserAgent::generate(),
            'X-Forwarded-For' => $ip,
            'Client-IP'       => $ip
        ];
    }
}
