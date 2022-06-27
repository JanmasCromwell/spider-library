<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Action\Producter;

use App\Library\Client\Redis;

class BookProducter extends Producter
{
    const ACTION_NAME = 'BookLinkSourceMap';

    public static function done(?array $context)
    {
        $redisPoolInstance = Redis::getInstance();
        $redis = $redisPoolInstance->get(1);
        $domain = $context['domain'] ?? '';
        $data = $context['data'];
        $newData = array_map(function ($info) use ($domain) {
            $info['link'] = $domain . ltrim($info['link'], '/');
            $info['domain'] = $domain;
            return json_encode($info);
        }, $data);
        unset($data);
        $redis->sAddArray(self::ACTION_NAME, $newData);
        $redisPoolInstance->put($redis);
        //唤醒消费者进程
        /* $process = new BooksConsumber(ApplicationContext::getContainer());
         return $process->handle();*/
    }
}
