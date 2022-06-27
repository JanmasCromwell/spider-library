<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Client;

use Hyperf\Elasticsearch\ClientBuilderFactory;
use Hyperf\Utils\ApplicationContext;

class ElasticSearch
{

    public static function getInstance()
    {
        $builder = ApplicationContext::getContainer()->get(ClientBuilderFactory::class)->create();
        return $builder->setHosts(['http://' . env('ES_HOST') . ':' . env('ES_PORT')])->build();
    }

    public static function __callStatic($name, $arguments)
    {
        self::getInstance()->$name(...$arguments);
    }
}
