<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Process;

use App\Library\Action\Consumer\BooksConsumer;
use App\Library\Action\Producter\BookProducter;
use App\Library\Client\Logger;
use App\Library\Client\Redis;
use App\Model\Books;

/**
 * 消费redis中的图书列表信息
 */
class BooksQueueConsumer extends \Hyperf\Process\AbstractProcess
{
    public $name = 'consumberBookQueue';

    public $nums = 11;


    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        $key = BookProducter::ACTION_NAME;
        $allowFilureNumber = BooksConsumer::FailureNumber;
        $redisPool = Redis::getInstance();
        while (true) {
            $redis = $redisPool->get();
            if ($redis->sCard($key) > 0) {
                $info = $redis->sPop($key);
                $info = json_decode($info, true);
                if (!is_null($info) && !Books::isExists($info['name'] ?? '')) {
                    if (isset($info['failure']) && $info['failure'] > $allowFilureNumber) {
                        Logger::writeError('图书详情抓取消费者队列，某数据的错误次数已达上线', $info);
                        continue;
                    }
                    if (!BooksConsumer::done($info)) {
                        $info['failure'] = !isset($info['failure']) ? 1 : $info['failure'] + 1;
                        $redis->sAdd($key, json_encode($info));
                    }
                }
            }
//            echo '图书资源队列-------现在蜘蛛队列里面的图书数量:' . $redis->sCard($key) . PHP_EOL;
            $redisPool->put($redis);
        }
    }
}
