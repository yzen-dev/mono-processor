<?php

namespace MonoProcessor;

use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package MonoProcessors
 */
class ServiceProvider extends IlluminateServiceProvider
{
    public static $package = 'mono-processor';

    /**
     * Perform post-registration booting of services.
     * @return void
     */
    public function boot() : void
    {
        $this->bindEvents();

        $this->publishes(
            [
                __DIR__ . '/config/mono-processor.php' => config_path(static::$package . '.php'),
            ],
            'config');
    }

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/mono-processor.php', self::$package);
    }

    /**
     * Bind to the Laravel event dispatcher to log events.
     */
    protected function bindEvents()
    {
        $handler = new EventHandler($this->app->events,$this->getUserConfig());

        $handler->subscribe();
    }

    private function getUserConfig(): array
    {
        $config = $this->app['config'][static::$package];

        return empty($config) ? [] : $config;
    }
}
