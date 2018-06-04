<?php

namespace HappyDemon\EchoServer;

use HappyDemon\EchoServer\Channels\Repository;
use HappyDemon\EchoServer\Events\WebSocketConnected;
use HappyDemon\EchoServer\Events\WebSocketDisonnected;
use HappyDemon\EchoServer\Facades\EchoStorage;
use HappyDemon\EchoServer\Storage\Storage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EchoServerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'happydemon');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'happydemon');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {

            // Publishing the configuration file.
            $this->publishes([
                __DIR__.'/../config/echoserver.php' => config_path('echoserver.php'),
            ], 'echoserver.config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/happydemon'),
            ], 'echoserver.views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/happydemon'),
            ], 'echoserver.views');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/happydemon'),
            ], 'echoserver.views');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/echoserver.php', 'echoserver');

        // Register the service the package provides.
        $this->app->singleton('echoserver', function ($app) {
            return new EchoServer;
        });

        $this->registerStorage();


        $this->app->singleton('echo.channels', function ($app) {
            return app(Repository::class);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['echoserver', 'echo.storage'];
    }

    protected function registerStorage()
    {
        // Register binding for the facade
        $this->app->singleton('echo.storage', function ($app) {
            return app(Storage::class);
        });

        // Set up event listeners
        Event::listen(WebSocketConnected::class, function(WebSocketConnected $connection){
            EchoStorage::onConnect($connection->webSocket, $connection->request);
        });
        Event::listen(WebSocketDisonnected::class, function(WebSocketDisonnected $connection){
            EchoStorage::onDisconnect($connection->webSocket);
        });
    }
}