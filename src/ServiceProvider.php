<?php

namespace MonoProcessor;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
 * @package MonoProcessors
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * package name
     * @var string
     */
    public static $package = 'mono-processor';

    /**
     * Perform post-registration booting of services.
     * @return void
     */
    public function boot(): void
    {
        $this->bindEvents();

        $this->publishes(
            [
                /* @phpstan-ignore-next-line */
                __DIR__ . '/config/mono-processor.php' => config_path(static::$package . '.php'),
            ],
            'config'
        );
    }

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/mono-processor.php', self::$package);
    }

    /**
     * Bind to the Laravel event dispatcher to log events.
     */
    protected function bindEvents(): void
    {
        /* @phpstan-ignore-next-line */
        $handler = new EventHandler($this->app->events);

        $handler->subscribe();
    }
}
