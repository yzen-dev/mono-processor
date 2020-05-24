<?php
declare(strict_types = 1);

namespace MonoProcessor\Processors;


use MonoProcessor\Config;

class PhpInfoProcessor extends AbstractProcessor
{
    public function __invoke(array $record) : array
    {
        if (!$this->isWrite($record['level_name']) || !Config::isEnabledValue('phpinfo')) {
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
