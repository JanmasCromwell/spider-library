<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Action;

use App\Library\Client\Logger;

abstract class Action
{
    abstract static function done(?array $context);




}
