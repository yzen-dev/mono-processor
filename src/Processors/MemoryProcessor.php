<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;
use MonoProcessor\Helpers\LogLevel;

/**
 * Class MemoryProcessor
 * @package MonoProcessor\Processors
 */
class MemoryProcessor
{
    /**
     * Add in extra memory_peak_usage
     * @param array<mixed> $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!LogLevel::isWrite($record['level_name']) || !Config::isEnabledValue('memoryPeak')) {
            return $record;
        }

        $usage = memory_get_peak_usage(true);
        $usage = $this->formatBytes($usage);
        $record['extra']['memory_peak_usage'] = $usage;

        return $record;
    }

    /**
     * @param int $bytes
     * @return string
     */
    public function formatBytes(int $bytes): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . ' ' . $unit[$i];
    }
}
