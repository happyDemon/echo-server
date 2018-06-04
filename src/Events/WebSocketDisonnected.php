<?php

namespace HappyDemon\EchoServer\Events;

use SwooleTW\Http\Websocket\Websocket;

class WebSocketDisonnected
{
    /**
     * @var Websocket
     */
    public $webSocket;

    /**
     * WebSocketConnected constructor.
     *
     * @param Websocket $webSocket
     */
    public function __construct(Websocket $webSocket)
    {
        $this->webSocket = $webSocket;
    }
}