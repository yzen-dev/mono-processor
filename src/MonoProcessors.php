<?php
declare(strict_types=1);

namespace MonoProcessor;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;
use MonoProcessor\Processors\MemoryProcessor;
use MonoProcessor\Processors\PhpInfoProcessor;
use MonoProcessor\Processors\GitInfoProcessor;
use MonoProcessor\Processors\RequestProcessor;
use MonoProcessor\Processors\BreadcrumbsProcessor;

/**
 * Class MonoProcessors
 *
 * @package App\Logging
 */
class MonoProcessors
{
    /**
     * @param \Monolog\Logger $logger
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new GitInfoProcessor);
            $handler->pushProcessor(new MemoryProcessor);
            $handler->pushProcessor(new PhpInfoProcessor);
            $handler->pushProcessor(new RequestProcessor);
            $handler->pushProcessor(new BreadcrumbsProcessor);

            $format = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message%\n%context%\n%extra%",
                'Y-m-d H:i:s', true,
                true
            );

            $format->setJsonPrettyPrint(true);
            $format->includeStacktraces(true);
            $handler->setFormatter($format);
        }
    }
}
