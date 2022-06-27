<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Client;

use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

/**
 * @property RedisPool $instance
 */
class Redis
{
    protected static $instance;
    protected static $pool;

    public static function getInstance()
    {
        return self::initPool();

    }

    protected static function initPool()
    {
        if (!self::$instance instanceof RedisPool) {
            $config = new RedisConfig();
            $config->withHost(env('REDIS_HOST'));
            $config->withPort(env('REDIS_PORT'));
            $config->withDbIndex(env('REDIS_DB'));
            $env = env('REDIS_AUTH');
            if ($env) {
                $config->withAuth($env);
            }
            self::$instance = new RedisPool($config, env('REDIS_POOL_SIZE'));
        }
        return self::$instance;
    }

}
