<?php

namespace App\Library\Logic;

abstract class Logic
{

    abstract public function done(string $content): string;
}
