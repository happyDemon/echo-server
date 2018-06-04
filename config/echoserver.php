<?php

return [
    'storage' => [
        'driver' => \HappyDemon\EchoServer\Storage\Room::class,
        'channels_table' => env('ES_TABLE', 'echo:channels'),

        'swoole_table' => [
            'rows' => env('ES_TABLE_ROWS', 4096),
            'size' => env('ES_TABLE_SIZE', 2048)
        ]
    ]
];