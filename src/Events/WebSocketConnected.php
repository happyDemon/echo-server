<?php

namespace HappyDemon\EchoServer\Events;

use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Websocket;

class WebSocketConnected
{
    /**
     * @var Websocket
     */
    public $webSocket;
    /**
     * @var Request
     */
    public $request;

    /**
     * WebSocketConnected constructor.
     *
     * @param Websocket $webSocket
     */
    public function __construct(Websocket $webSocket, Request $request)
    {
        $this->webSocket = $webSocket;
        $this->request = $request;
    }
}