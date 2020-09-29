<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use Monolog\Processor\ProcessorInterface;
use MonoProcessor\Config;

/**
 * Class AbstractProcessor
 * @package MonoProcessor\Processors
 */
abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * Checking processor entry for the current level
     *
     * @param string $level_name
     * @return bool
     */
    public function isWrite(string $level_name): bool
    {
        return in_array($level_name, Config::getByKey('levels'), true);
    }
}
