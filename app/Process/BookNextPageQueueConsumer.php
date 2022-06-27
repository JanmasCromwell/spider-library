<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Process;

use App\Library\Action\Consumer\BookNextPageConsumer;
use App\Library\Action\Producter\BookNextPageProducter;
use App\Library\Client\Logger;
use App\Library\Client\Redis;

/**
 * 翻页队列消费
 */
class BookNextPageQueueConsumer extends \Hyperf\Process\AbstractProcess
{
    public $name = 'consumerBookNextPageQueue';

    public $nums = 1;

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        $key = BookNextPageProducter::ACTION_NAME;
        $allowFailureNumber = BookNextPageConsumer::FailureNumber;
        $redisPool = Redis::getInstance();
        while (true) {
            $redis = $redisPool->get();
            if ($redis->lLen($key) > 0) {
                $originalInfo = $redis->lPop($key);
                $info = json_decode($originalInfo, true);
                if (is_null($info)) {
                    Logger::writeError('翻页消费者json解析失败', ['error' => json_last_error_msg()]);
                    continue;
                }
                if (isset($info['fialure']) && $info['fialure'] > $allowFailureNumber) {
                    Logger::writeError('列表翻页消费者，某数据的错误次数已达上线', $info);
                    continue;
                }
                if (!BookNextPageConsumer::done($info)) {
                    if (!isset($info['failure'])) {
                        $info['failure'] = 0;
                    } else {
                        $info['failure'] += 1;
                    }
                    $redis->lPush($key,);
                }
            }
//            echo '图书翻页队列-------队列里面的图书资源数量:' . $redis->lLen($key) . PHP_EOL;
            $redisPool->put($redis);
        }
    }
}
