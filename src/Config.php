<?php
declare(strict_types = 1);

namespace MonoProcessor;


class Config
{
    public static function getAll()
    {
        $config = app()['config']['mono-processor'];

        return empty($config) ? [] : $config;
    }

    public static function getByKey($key)
    {
        return self::getAll()[$key];
    }
    
    public static function isEnabledValue($field)
    {
        return array_key_exists($field, self::getAll()) ? self::getAll()[$field] === true : true;
    }
}
