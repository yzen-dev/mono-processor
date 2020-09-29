<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;
use MonoProcessor\Helpers\LogLevel;

/**
 * Class PhpInfoProcessor
 * @package MonoProcessor\Processors
 */
class PhpInfoProcessor
{
    /**
     * Add in extra php version
     *
     * @param array $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!LogLevel::isWrite($record['level_name']) || !Config::isEnabledValue('phpinfo')) {
            return $record;
        }
        $record['extra'] += [
            'php' => [
                'version' => PHP_VERSION,
            ]
        ];

        return $record;
    }
}
