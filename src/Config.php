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
     * @param string $key
     * @return mixed
     */
    public static function getByKey(string $key)
    {
        if (isset(self::getAll()[$key])) {
            return self::getAll()[$key];
        }
        return false;
    }

    /**
     * Is parameter enabled in the config
     *
     * @param string $field
     * @return bool
     */
    public static function isEnabledValue(string $field): bool
    {
        return array_key_exists($field, self::getAll()) ? self::getAll()[$field] === true : true;
    }
}
