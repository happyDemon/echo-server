<?php

namespace HappyDemon\EchoServer\Socket\Events;


use HappyDemon\EchoServer\Facades\EchoStorage;
use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Room;
use SwooleTW\Http\Websocket\Facades\Websocket;
use SwooleTW\Http\Websocket\Websocket as SocketInstance;

class Connection
{
    public function connect(SocketInstance $webSocket, Request $request)
    {
        if ($request->user() !== null) {
            $webSocket->loginUsing($request->user());
        }

        EchoStorage::connect($webSocket, $request);

        \Log::info('socket', [$webSocket, ' s <-> r ', $request->input(), $request->user()]);
        \Log::debug('connected clients', ['client' => Room::getClients('game')]);



        // called while socket on connect
        $webSocket->emit('message', ['data' => 'some']);
        Websocket::emit('message', 'this is a test');
    }

    public function disconnect(SocketInstance $webSocket)
    {
        EchoStorage::disconnect($webSocket);
    }
}