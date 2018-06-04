<?php

namespace HappyDemon\EchoServer\Channels;

use HappyDemon\EchoServer\Facades\EchoStorage;
use Swoole\Http\Client;
use SwooleTW\Http\Websocket\Websocket;

class PrivateChannel extends ChannelContract
{
    public function authenticate(Websocket $socket, $data)
    {
        // Build the headers
        $headers = (isset($data['auth']) && isset($data['auth']['headers'])) ? $data['auth']['headers'] : [];
        $headers['X-Requested-With'] = 'XMLHttpRequest';

        // Check for cookie
        $user = EchoStorage::getUser($socket);
        if (is_array($user) && count($user) == 2) {
            $headers['Cookie'] = $user['cookie'];
        }

        // Build the client (don't do an SSL check)
        $cli = new Client('127.0.0.1', 1215, false);
        $cli->setHeaders($headers);
        $cli->setData([
            'channel_name' => $this->name,
        ]);
        $cli->setMethod('post');

        $response = null;

        // @todo make sure this works 100%, getting mixed results
        // Send the request
        $cli->execute('/broadcasting/auth', function (Client $cli) use ($socket, &$response) {
            // Authentication was unsuccessful
            if ($cli->statusCode !== 200) {
                $this->emit(
                    $socket,
                    'subscription_error',
                    [
                        'reason' => 'Client can not be authenticated, got HTTP status ' . $cli->statusCode,
                        'status' => $cli->statusCode,
                    ],
                    $socket->getSender()
                );
                $response = false;
                return;
            }
            $response = true;
        });

        return $response;
    }

    public function isPrivate()
    {
        return true;
    }
}