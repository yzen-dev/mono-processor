<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;


use MonoProcessor\Breadcrumbs;

class BreadcrumbsProcessor extends AbstractProcessor
{
    public function __invoke(array $record):array 
    {
        if ( ! $this->isWrite($record['level_name'])) {
            return $record;
        }
    
        $record['extra'] += [
            'breadcrumbs' => Breadcrumbs::getInstance()->getBreadcrumbs(),
        ];
    
        return $record;
    }
}
