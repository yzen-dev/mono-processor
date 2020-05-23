## MonoProcessor - Supplement your logging with auxiliary information on error
This Processor will display in the logs bread crumbs by which you can more quickly and accurately identify the cause of the error.

## :scroll: **Installation**
The package can be installed via composer:
```
composer require yzen.dev/mono-processor
```

## :scroll: **Usage**
To use MonoProcessor you need to add the following `tap` to your `logging.php` config:
```php
    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => env('LOG_DAYS', 7),
            'tap' => [
                App\Logging\MonoProcessors::class
            ]
        ]
    ]
```
