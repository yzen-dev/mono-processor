<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;
use MonoProcessor\Helpers\Uuid;
use MonoProcessor\Helpers\LogLevel;

/**
 * Class UuidProcessor
 * @package MonoProcessor\Processors
 */
class UuidProcessor
{
    /**
     * Add in extra uuid
     *
     * @param array $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!LogLevel::isWrite($record['level_name']) || !Config::isEnabledValue('uuid')) {
            return $record;
        }
        $record['extra'] += [
            'uuid' => Uuid::uuidV4()
        ];

        return $record;
    }
}
