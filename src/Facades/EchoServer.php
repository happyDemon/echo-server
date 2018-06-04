<?php

namespace HappyDemon\EchoServer\Facades;

use Illuminate\Support\Facades\Facade;

class EchoServer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'echoserver';
    }
}
