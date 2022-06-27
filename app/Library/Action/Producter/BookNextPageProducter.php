<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Action\Producter;

use App\Library\Client\Redis;

class BookNextPageProducter extends Producter
{
    const ACTION_NAME = 'BookNextPageSource';


    static function done(?array $context)
    {

        $redisPoolInstance = Redis::getInstance();
        $redis = $redisPoolInstance->get();
        $redis->lPush(self::ACTION_NAME, json_encode($context));
        $redisPoolInstance->put($redis);
    }
}
