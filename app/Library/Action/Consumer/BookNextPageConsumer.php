<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Action\Consumer;

use App\Library\Spider\Spider;

class BookNextPageConsumer extends Consumer
{

    static function done(?array $context)
    {
        return (new Spider($context['domain']))->done($context['name'], $context['arg']);
    }
}
