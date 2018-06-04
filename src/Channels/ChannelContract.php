<?php

namespace HappyDemon\EchoServer\Channels;


use HappyDemon\EchoServer\Facades\EchoStorage;
use SwooleTW\Http\Websocket\Websocket;

abstract class ChannelContract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Channel constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function members()
    {
        return EchoStorage::getMembers($this->name);
    }

    public function isMember($socketId)
    {
        $members = collect(EchoStorage::getActiveMembers($this->name));

        if(is_a($socketId, Websocket::class))
        {
            $socketId = $socketId->getSender();
        }

        // Is the socket id in the channel
        return $members->filter(function($fds)use($socketId) { return $fds === $socketId; })->first() !== null;
    }

    /**
     * @param Websocket $socket
     *
     * @return ChannelContract
     */
    public function join(Websocket $socket)
    {
        EchoStorage::addToChannel($this->name, $socket->getSender());

        return $this;
    }

    public function leave(Websocket $socket)
    {
        EchoStorage::removeFromChannel($this->name, $socket->getSender());

        return $this;
    }

    public function isPrivate()
    {
        return false;
    }

    public function isPresence()
    {
        return false;
    }

    public function emit(Websocket $socket, string $event, array $data, $to=null)
    {
        if($to == null)
        {
            $socket->in($this->name)->emit($event, $data);
            return;
        }

        $socket->in($this->name)->to($to)->emit($event, $data);
    }
}