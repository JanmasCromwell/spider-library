<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Client;

class CosClient
{
    /**
     * @return \Qcloud\Cos\Client
     */
    static function getInstance()
    {
        $secretId = env('COS_SECRETID');
        $secretKey = env('COS_SECRETKEY');
        $region = env('COS_REGION');
        return new \Qcloud\Cos\Client(array(
            'region'      => $region,
            'schema'      => 'https', //协议头部，默认为http
            'credentials' => array(
                'secretId'  => $secretId,
                'secretKey' => $secretKey
            )
        ));
    }
}
