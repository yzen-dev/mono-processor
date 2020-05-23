<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;


class GitInfoProcessor extends AbstractProcessor
{
    public function __invoke(array $record): array
    {
        if ( ! $this->isWrite($record['level_name'])) {
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
