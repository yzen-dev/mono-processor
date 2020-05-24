## MonoProcessor - Supplement your logging with auxiliary information on error
<img alt="Packagist Downloads" src="https://img.shields.io/packagist/dm/yzen.dev/mono-processor">
<img alt="Packagist Downloads" src="https://img.shields.io/packagist/dt/yzen.dev/mono-processor">

This Processor will display in the logs bread crumbs by which you can more quickly and accurately identify the cause of the error.

## :scroll: **Installation**
The package can be installed via composer:
```
composer require yzen.dev/mono-processor
```
To get started, first publish MonoProcessor config and view files into your own project:
```
php artisan vendor:publish --provider "MonoProcessor\ServiceProvider"
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
                MonoProcessors\MonoProcessors::class
            ]
        ]
    ]
```
