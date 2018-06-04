<?php

namespace HappyDemon\EchoServer\Channels;

use HappyDemon\EchoServer\Facades\EchoStorage;
use SwooleTW\Http\Websocket\Websocket;

class PresenceChannel extends PrivateChannel
{
    public function join(Websocket $socket)
    {
        if($socket->getUserId() == null)
        {
            // Unable to join if no user data is present
            return $this;
        }

        // Handle the join
        EchoStorage::addToChannel($this->name, $socket->getSender());
        $this->triggerSubscription($socket);

        // The join event gets sent to all other members
        // when it's the first connection the current user makes.
        if(!EchoStorage::isUserInChannel($this->name, $socket->getUserId()))
        {
            $this->triggerJoin($socket);
        }

        return $this;
    }

    public function leave(Websocket $socket)
    {
        // Handle the disconnection
        EchoStorage::removeFromChannel($this->name, $socket->getSender());

        // Only trigger the leave event if it's the last connection
        // the current user had open.
        if(!EchoStorage::isUserInChannel($this->name, $socket->getUserId()))
        {
            $this->triggerLeave($socket);
        }
        return $this;
    }

    protected function triggerSubscription(Websocket $socket)
    {
        $socket->to($socket->getSender())->emit('presence:subscribed', EchoStorage::getMembers($this->name));
    }

    protected function triggerJoin(Websocket $socket)
    {
        $socket->to($this->name)->emit('presence:joining', $socket->getUserId());
    }

    protected function triggerLeave(Websocket $socket)
    {
        $socket->to($this->name)->emit('presence:leaving', $socket->getUserId());
    }



    public function isPrivate()
    {
        return true;
    }

    public function isPresence()
    {
        return true;
    }
}