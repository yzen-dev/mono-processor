<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;

use Monolog\Processor\ProcessorInterface;
use MonoProcessor\Config;

abstract class AbstractProcessor implements ProcessorInterface
{
    public function isWrite($level_name)
    {
        return in_array($level_name, Config::getByKey('levels'), true);
    }    
}
