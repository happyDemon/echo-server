<?php

namespace HappyDemon\EchoServer\Storage;


use HappyDemon\EchoServer\Facades\EchoChannels;
use Illuminate\Http\Request;
use Swoole\Table;
use SwooleTW\Http\Table\Facades\SwooleTable;
use SwooleTW\Http\Websocket\Facades\Room as RoomFacade;
use SwooleTW\Http\Websocket\Websocket;

class Room implements StorageContract
{
    /**
     * @var Table
     */
    protected $users;

    public function __construct($config)
    {
        $this->users = SwooleTable::get('echo:users');
    }

    /**
     * Add a user to a channel.
     *
     * @param string $channel
     * @param string $fd
     *
     * @return $this|SwooleTable
     */
    public function addToChannel(string $channel, string $fd)
    {
        RoomFacade::add($fd, $channel);

        return $this;
    }

    /**
     * Remove a user from a channel.
     *
     * @param string $channel
     * @param string $fd
     *
     * @return $this|SwooleTable
     */
    public function removeFromChannel(string $channel, string $fd)
    {
        RoomFacade::delete($fd, $channel);

        return $this;
    }

    /**
     * Get all users in a specific channel.
     *
     * @param string $channel
     *
     * @return array|mixed
     */
    public function getMembers(string $channel)
    {
        return RoomFacade::getClients($channel);
    }

    /**
     * Removes all inactive connections.
     *
     * @param string $channel
     */
    public function cleanMembers(string $channel)
    {
        // feature is standard-included by RoomFacade
    }

    /**
     * Check if the provided user id is in the channel.
     * @param string $channel
     * @param int    $userId
     *
     * @return bool
     */
    public function isUserInChannel(string $channel, int $userId)
    {
        return collect($this->getMembers($channel))
            ->filter(function($fds) use($userId){
                $member = $this->users->get($fds);

                if(!is_array($member)) return false;

                return $member['value'] == $userId;
            })
            ->count() > 0;
    }

    /**
     * Triggered when a new websocket connection is made.
     *
     * @param Websocket $socket
     * @param Request   $request
     */
    public function onConnect(Websocket $socket, Request $request)
    {
        if(is_int($socket->getUserId())) return;

        $this->users->set($socket->getSender(), ['value' => $socket->getUserId(), 'cookie' => $request->header('Cookie')]);
    }

    /**
     * Triggered hen a connection hangs up.
     *
     * @param Websocket $socket
     */
    public function onDisconnect(Websocket $socket)
    {
        if(is_int($socket->getUserId())) return;

        $this->users->del($socket->getSender());
    }

    /**
     * Get user info.
     *
     * @param Websocket $socket
     *
     * @return array
     */
    public function getUser(Websocket $socket)
    {
        return $this->users->get($socket->getSender());
    }
}