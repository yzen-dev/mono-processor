<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Breadcrumbs;
use MonoProcessor\Helpers\LogLevel;

/**
 * Class BreadcrumbsProcessor
 * @package MonoProcessor\Processors
 */
class BreadcrumbsProcessor
{
    /**
     * Add in extra breadcrumbs
     *
     * @param array<mixed> $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!LogLevel::isWrite($record['level_name'])) {
            return $record;
        }

        $record['extra'] += [
            'breadcrumbs' => Breadcrumbs::getInstance()->getBreadcrumbs(),
        ];

        return $record;
    }
}
