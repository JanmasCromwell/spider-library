<?php

namespace App\Library\Client;


use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

class Logger
{
    public static function getInstance(string $name = 'app')
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
    }

    public static function __callStatic($name, $arguments)
    {
        self::getInstance()->$name(...$arguments);
    }

    static function writeInfo(string $message, array $context = [], string $file = '', ?int $line = null)
    {
        if (!empty($file)) {
            $context['file'] = $file . '@' . $line;
        }
        self::getInstance()->info($message, $context);
    }

    static function writeError(string $message, array $context = [], string $file = '', ?int $line = null)
    {
        if (!empty($file)) {
            $context['file'] = $file . '@' . $line;
        }
        self::getInstance()->error($message, $context);
    }

    static function writeDebug(string $message, array $context = [], string $file = '', ?int $line = null)
    {
        if (!empty($file)) {
            $context['file'] = $file . '@' . $line;
        }
        self::getInstance()->debug($message, $context);
    }

    static function writeWarning(string $message, array $context = [], string $file = '', ?int $line = null)
    {
        if (!empty($file)) {
            $context['file'] = $file . '@' . $line;
        }
        self::getInstance()->warning($message, $context);
    }

}
