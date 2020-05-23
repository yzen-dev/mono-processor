<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;

use Monolog\Logger;

abstract class AbstractProcessor
{
    private $level;
    
    /**
     * @param string|int $level The minimum logging level at which this Processor will be triggered
     */
    public function __construct($level = Logger::DEBUG)
    {
        $this->level = Logger::toMonologLevel($level);
    }
    
    public function isWrite($level_name)
    {
        return in_array($level_name, ['ERROR','EMERGENCY']);
    }
}
