<?php

namespace HappyDemon\EchoServer\Channels;


use SwooleTW\Http\Websocket\Websocket;

class Repository
{
    /**
     * @var ChannelContract[]
     */
    protected $channels = [];

    protected $parse = [
        'private-'  => PrivateChannel::class,
        'presence-' => PresenceChannel::class,
        ''          => Channel::class,
    ];

    /**
     * Subscribe to a channel.
     *
     * @param Websocket $socket
     * @param array     $data
     *
     * @return ChannelContract|false
     */
    public function subscribe(Websocket $socket, array $data)
    {
        $channel = $this->get($data['channel']);

        // Make sure the channel exists
        if ($channel == null) {
            $channel = $this->register($data['channel']);
        }

        // Private / presence channel need authentication
        if($channel->isPrivate())
        {
            $authentication = $channel->authenticate($socket, $data);

            // Authentication failed, abort
            if($authentication === false)
            {
                return false;
            }
        }

        // Join the channel
        return $channel->join($socket);
    }

    /**
     * Unsubscribe to a channel.
     *
     * @param Websocket $socket
     * @param array     $data
     *
     * @return ChannelContract
     */
    public function unsubscribe(Websocket $socket, array $data)
    {
        $channel = $this->get($data['channel']);

        if ($channel == null) return;

        return $channel->leave($socket);
    }

    /**
     * Registers the channel.
     *
     * @param string $channel
     *
     * @return ChannelContract
     */
    protected function register(string $channel)
    {
        foreach ($this->parse as $prefix => $class) {
            if (starts_with($channel, $prefix)) {
                return $this->channels[$channel] = new $class($channel);
                break;
            }
        }

        return $this->channels[$channel] = new Channel($channel);
    }

    /**
     * @param $channel
     *
     * @return ChannelContract|null
     */
    public function get($channel)
    {
        return $this->channels[$channel] ?? null;
    }

    /**
     * @param $channel
     *
     * @return bool
     */
    public function has($channel)
    {
        return isset($this->channels[$channel]);
    }

    /**
     * @param $channel
     *
     * @return Repository
     */
    public function remove($channel)
    {
        if($this->has($channel)) unset($this->channels[$channel]);
        return $this;
    }
}