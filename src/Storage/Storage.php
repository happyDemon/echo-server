<?php

namespace HappyDemon\EchoServer\Storage;


use HappyDemon\EchoServer\Events\WebSocketConnected;
use HappyDemon\EchoServer\Events\WebSocketDisonnected;
use HappyDemon\EchoServer\Facades\EchoChannels;
use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Websocket;

class Storage
{
    protected $config;

    /**
     * @var StorageContract
     */
    protected $driver;

    public function __construct()
    {
        $this->config = config('echoserver.storage');

        $class = $this->config['driver'];
        $this->driver = new $class($this->config);
    }

    /**
     * Add a user to a channel.
     *
     * @param string $channel
     * @param string $fd
     *
     * @return $this
     */
    public function addToChannel(string $channel, string $fd) {
        $this->driver->addToChannel($channel, $fd);

        return $this;
    }

    /**
     * Remove a user from a channel.
     *
     * @param string $channel
     * @param int    $userId
     *
     * @return $this
     */
    public function removeFromChannel(string $channel, int $userId)
    {
        $this->driver->removeFromChannel($channel, $userId);

        // Do some cleanup
        if(count($this->driver->getMembers($channel)) == 0)
        {
            EchoChannels::remove($channel);
        }

        return $this;
    }

    /**
     * Get all users in a specific channel.
     *
     * @param string $channel
     *
     * @return array
     */
    public function getMembers(string $channel)
    {
        return $this->driver->getMembers($channel);
    }

    /**
     * Cleanup inactive connections and returns all active users.
     *
     * @param string $channel
     *
     * @return array
     */
    public function getActiveMembers(string $channel)
    {
        $this->driver->cleanMembers($channel);

        return $this->getMembers($channel);
    }

    /**
     * Triggers the 'connect' event.
     *
     * @param Websocket $socket
     * @param Request   $request
     */
    public function connect(Websocket $socket, Request $request)
    {
        event(new WebSocketConnected($socket, $request));
    }

    /**
     * passes through to the driver's connect handler.
     *
     * @param Websocket $socket
     * @param Request   $request
     */
    public function onConnect(Websocket $socket, Request $request)
    {
        $this->driver->onConnect($socket, $request);
    }

    /**
     * Trigger a disconnect event.
     *
     * @param Websocket $socket
     */
    public function disconnect(Websocket $socket)
    {
        event(new WebSocketDisonnected($socket));
    }

    /**
     * passes through to the driver's disconnect handler.
     *
     * @param Websocket $socket
     */
    public function onDisconnect(Websocket $socket)
    {
        $this->driver->onDisconnect($socket);
    }

    /**
     * Checks if a given user id is present in a channel.
     *
     * @param string $channel
     * @param int    $userId
     *
     * @return bool
     */
    public function isUserInChannel(string $channel, int $userId)
    {
        return $this->driver->isUserInChannel($channel, $userId);
    }


    public function getUser(Websocket $socket){
        return $this->driver->getUser($socket);
    }
}