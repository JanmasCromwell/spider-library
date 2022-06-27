#!/usr/bin/env php
<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('memory_limit', '1G');

error_reporting(E_ALL);

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

require BASE_PATH . '/vendor/autoload.php';
//清空日志文件
$logDir = BASE_PATH . '/runtime/logs/';
$logFile = $logDir . 'hyperf.log';
if (is_file($logFile)) {
    copy($logFile, $logDir . date('Y-m-d H-i-s') . '.log');
    unlink($logFile);
}
// Self-called anonymous function that creates its own scope and keep the global namespace clean.
(function () {
    Hyperf\Di\ClassLoader::init();
    /** @var Psr\Container\ContainerInterface $container */
    $container = require BASE_PATH . '/config/container.php';

    $application = $container->get(Hyperf\Contract\ApplicationInterface::class);
    $application->run();
})();
\App\Library\Client\Logger::writeInfo('应用启动');
