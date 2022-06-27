<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Spider\Analyse;

use App\Library\Client\Logger;
use Psr\Http\Message\ResponseInterface;

class Analyse
{

    //加载匹配规则
    static function list(ResponseInterface $response)
    {


        /*$es = ElasticSearch::getInstance()->create([

        ]);*/
        //匹配度处理
//        ElasticSearch::getInstance()->get();
//        ElasticSearch::getInstance()->create();
//        return self::nextPage($nameMaps, $linkMaps);
    }

    static function nextPage(array $map)
    {

    }
}
