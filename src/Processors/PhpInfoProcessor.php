<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;

/**
 * Class PhpInfoProcessor
 * @package MonoProcessor\Processors
 */
class PhpInfoProcessor extends AbstractProcessor
{
    /**
     * Add in extra php version
     *
     * @param array $record
     * @return array
     */
    public function __invoke(array $record): array
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
