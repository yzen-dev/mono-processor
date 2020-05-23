<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;


class PhpInfoProcessor extends AbstractProcessor
{
    public function __invoke(array $record): array
    {
        if ( ! $this->isWrite($record['level_name'])) {
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
