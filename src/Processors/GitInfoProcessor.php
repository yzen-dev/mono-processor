<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;
use MonoProcessor\Helpers\LogLevel;

/**
 * Class GitInfoProcessor
 * @package MonoProcessor\Processors
 */
class GitInfoProcessor
{
    /**
     * Add in extra git info
     *
     * @param array $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!LogLevel::isWrite($record['level_name']) || !Config::isEnabledValue('git')) {
            return $record;
        }

        $branches = shell_exec('git branch -v --no-abbrev');

        if (preg_match('{^\* (.+?)\s+([a-f0-9]{40})(?:\s|$)(.*)}m', $branches, $matches)) {
            $record['extra']['git'] = [
                'branch' => $matches[1],
                'commit' => $matches[2],
                'name' => $matches[3],
            ];
        }

        return $record;
    }
}
