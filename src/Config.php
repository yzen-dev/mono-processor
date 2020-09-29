<?php

declare(strict_types=1);

namespace MonoProcessor;

/**
 * Class Config
 * @package MonoProcessor
 */
class Config
{
    /**
     * Get all config
     *
     * @return array|mixed
     */
    public static function getAll()
    {
        $config = app()['config']['mono-processor'];

        return empty($config) ? [] : $config;
    }

    /**
     * Get config value by key
     *
     * @param $key
     * @return mixed
     */
    public static function getByKey($key)
    {
        return self::getAll()[$key];
    }

    /**
     * Is parameter enabled in the config
     *
     * @param $field
     * @return bool
     */
    public static function isEnabledValue($field): bool
    {
        return array_key_exists($field, self::getAll()) ? self::getAll()[$field] === true : true;
    }
}
