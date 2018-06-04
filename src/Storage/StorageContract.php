<?php

namespace HappyDemon\EchoServer\Storage;


use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Websocket;

interface StorageContract
{
    /**
     * Add a connection to a channel.
     *
     * @param string $channel
     * @param string $fd
     *
     * @return $this
     */
    public function addToChannel(string $channel, string $fd);

    /**
     * Remove a connection from a channel.
     *
     * @param string $channel
     * @param string $fd
     *
     * @return mixed
     */
    public function removeFromChannel(string $channel, string $fd);

    /**
     * Get all users in a specific channel.
     *
     * @param string $channel
     *
     * @return array
     */
    public function getMembers(string $channel);

    /**
     * Removes all inactive connections.
     *
     * @param string $channel
     */
    public function cleanMembers(string $channel);


    /**
     * Is the user already present
     *
     * @param string $channel
     * @param int    $userId
     *
     * @return bool
     */
    public function isUserInChannel(string $channel, int $userId);

    /**
     * Get user info.
     *
     * @param Websocket $socket
     *
     * @return array
     */
    public function getUser(Websocket $socket);

    /**
     * Triggered when a new websocket connection is made.
     *
     * @param Websocket $socket
     * @param Request   $request
     */
    public function onConnect(Websocket $socket, Request $request);

    /**
     * Triggered hen a connection hangs up.
     *
     * @param Websocket $socket
     */
    public function onDisconnect(Websocket $socket);
}