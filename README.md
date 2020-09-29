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
                MonoProcessor\MonoProcessors::class
            ]
        ]
    ]
```
As a result, you will get approximately the following information after the stack: 
![example](http://ipic.su/img/img7/fs/Bezymyannyj.1601029498.jpg)

## :scroll: **Configuration**

| Processor         | Description                                                  |
| ----------------- | ------------------------------------------------------------ |
| stacktrace        | Stack output when an error occurs                            |
| memoryPeak        | Memory peak at runtime                                       |
| git               | Git branch and Git commit SHA                                |
| phpinfo           | php info (version)                                           |
| json_format       | Output of additional information in the format JSON_PRETTY_PRINT|
| command           | Listen console commands                                      |
| levels            | levels (DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY)|
| uuid              | Adds a unique identifier                                     |
| ----------------- | ------------------------------------------------------------ |
| request           | Logging of the received request                              |
| request.base_info | add request host,ip,url,method                               |
| request.header    | add request header                                           |
| request.body      | add request body                                             |
| ----------------- | ------------------------------------------------------------ |
| breadcrumbs       | What breadcrumbs do you need to collect                      |
| breadcrumbs.auth  | auth info                                                    |
| breadcrumbs.sql   | List of sql queries                                          |
| breadcrumbs.route  | route info (name,action)                                    |

