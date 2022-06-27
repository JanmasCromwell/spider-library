<?php
#+------------------------------------------------------------------
#| 普通的。
#+------------------------------------------------------------------
#| Author:Janmas Cromwell <janmas-cromwell@outlook.com>
#+------------------------------------------------------------------
namespace App\Library\Client;

use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\ApplicationContext;

class GuzzleClient
{
    static function getInstance(array $options = [])
    {
        return (new ClientFactory(ApplicationContext::getContainer()))->create($options);
    }
}
