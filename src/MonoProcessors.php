<?php

declare(strict_types=1);

namespace MonoProcessor;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;
use MonoProcessor\Processors\UuidProcessor;
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
     * @param Logger $logger
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(new GitInfoProcessor());
            $handler->pushProcessor(new MemoryProcessor());
            $handler->pushProcessor(new PhpInfoProcessor());
            $handler->pushProcessor(new BreadcrumbsProcessor());
            $handler->pushProcessor(new UuidProcessor());
            if (!app()->runningInConsole()) {
                $handler->pushProcessor(new RequestProcessor());
            }

            $format = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message%\n%context%\n%extra%",
                'Y-m-d H:i:s',
                true,
                true
            );

            if (Config::isEnabledValue('json_format')) {
                $format->setJsonPrettyPrint(true);
            }
            if (Config::isEnabledValue('stacktrace')) {
                $format->includeStacktraces(true);
            }
            $handler->setFormatter($format);
        }
    }
}
