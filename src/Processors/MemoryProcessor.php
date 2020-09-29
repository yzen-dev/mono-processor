<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;

/**
 * Class MemoryProcessor
 * @package MonoProcessor\Processors
 */
class MemoryProcessor extends AbstractProcessor
{
    /**
     * Add in extra memory_peak_usage
     * @param array $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!$this->isWrite($record['level_name']) || !Config::isEnabledValue('memoryPeak')) {
            return $record;
        }

        $usage = memory_get_peak_usage(true);
        $usage = $this->formatBytes($usage);
        $record['extra']['memory_peak_usage'] = $usage;

        return $record;
    }

    /**
     * @param $bytes
     * @return string
     */
    public function formatBytes($bytes): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . ' ' . $unit[$i];
    }
}
