# Echo-Server

A swoole-based implementation for a laravel echo server

## Installation

``` bash
$ composer require happydemon/echo-server
```

Enable swoole websockets in your `.env`

```bash
SWOOLE_HTTP_WEBSOCKET=true
```

Add a users table under the `swoole_http.tables` config

```php
'echo:users' => [
    'size' => 2048,
    'columns' => [
        ['name' => 'value', 'type' => Table::TYPE_STRING, 'size' => 128],
        ['name' => 'cookie', 'type' => Table::TYPE_STRING, 'size' => 256],
    ]
],
```

Don't forget to enable broadcasting (uncomment the `BroadCastServiceProvider` and your `app.service_providers` config).

## Usage

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.


## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email maxim.kerstens@gmail.com instead of using the issue tracker.

## Credits

- [Maxim Kerstens](https://github.com/happyDemon)
- [All Contributors](https://github.com/happyDemon/echo-server/graphs/contributors)

## License

MIT. Please see the [license file](license.md) for more information.
