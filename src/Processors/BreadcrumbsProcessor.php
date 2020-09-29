<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Breadcrumbs;

/**
 * Class BreadcrumbsProcessor
 * @package MonoProcessor\Processors
 */
class BreadcrumbsProcessor extends AbstractProcessor
{
    /**
     * Add in extra breadcrumbs
     *
     * @param array $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (!$this->isWrite($record['level_name'])) {
            return $record;
        }

        $record['extra'] += [
            'breadcrumbs' => Breadcrumbs::getInstance()->getBreadcrumbs(),
        ];

        return $record;
    }
}
