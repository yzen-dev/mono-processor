<?php

declare(strict_types=1);

namespace MonoProcessor\Helpers;

use MonoProcessor\Config;

/**
 * Class LogLevel
 *
 * @package MonoProcessor\Helpers
 */
class LogLevel
{
    /**
     * Checking processor entry for the current level
     *
     * @param string $levelName
     * @return bool
     */
    public static function isWrite(string $levelName): bool
    {
        return in_array($levelName, Config::getByKey('levels'), true);
    }
}