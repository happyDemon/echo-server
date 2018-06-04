<?php

namespace HappyDemon\EchoServer\Facades;

use Illuminate\Support\Facades\Facade;

class EchoStorage extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'echo.storage';
    }
}
