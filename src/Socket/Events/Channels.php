<?php

namespace HappyDemon\EchoServer\Socket\Events;

use HappyDemon\EchoServer\Facades\EchoChannels;
use SwooleTW\Http\Websocket\Facades\Room;
use SwooleTW\Http\Websocket\Websocket as SocketInstance;

class Channels
{
    public function join(SocketInstance $webSocket, $data)
    {
        \Log::debug('join channel', ['data' => $data, 'user' => $webSocket->getUserId()]);

        EchoChannels::subscribe($webSocket, $data);

        \Log::info('channel clients', [
            'data' => $data,
            'sender' => $webSocket->getSender(),
            'clients' => Room::getClients($data['channel'])
        ]);
    }

    public function leave(SocketInstance $webSocket, $data)
    {
        \Log::debug('leave channel', ['data' => $data, 'user' => $webSocket->getUserId()]);

        EchoChannels::unsubscribe($webSocket, $data);
    }

    /**
     * Pass through whispers.
     *
     * @param SocketInstance $webSocket
     * @param                $data
     */
    public function clientEvent(SocketInstance $webSocket, $data)
    {
        $webSocket->in($data['channel'])->emit($data['event'], $data['data']);
    }
}