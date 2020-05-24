<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;

use Monolog\Processor\ProcessorInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    private $config;

    /**
     * @param string|int $level The minimum logging level at which this Processor will be triggered
     */
    public function __construct()
    {
        $this->config = $this->getConfig();
    }

    public function isWrite($level_name)
    {
        return in_array($level_name, ['ERROR','EMERGENCY']);
    }

    private function getConfig() : array
    {
        $config = app()['config']['mono-processor'];

        return empty($config) ? [] : $config;
    }
}
