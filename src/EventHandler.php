<?php

namespace MonoProcessor;

use Exception;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Route;
use RuntimeException;

class EventHandler
{
    /**
     * Map event handlers to events.
     *
     * @var array
     */
    protected static $eventHandlerMap = [
        // Laravel 5.1
        'router.matched' => 'routerMatched',
        // Laravel 5.2
        'Illuminate\Routing\Events\RouteMatched' => 'routeMatched',

        // Laravel 5.1
        'illuminate.query' => 'query',
        // Laravel 5.2
        'Illuminate\Database\Events\QueryExecuted' => 'queryExecuted',

        // Laravel 5.5
        'Illuminate\Console\Events\CommandStarting' => 'commandStarting',
        'Illuminate\Console\Events\CommandFinished' => 'commandFinished',
    ];

    /**
     * Map authentication event handlers to events.
     *
     * @var array
     */
    protected static $authEventHandlerMap = [
        'Illuminate\Auth\Events\Authenticated' => 'authenticated', // Since Laravel 5.3
    ];

    /**
     * Map queue event handlers to events.
     *
     * @var array
     */
    protected static $queueEventHandlerMap = [
        'Illuminate\Queue\Events\JobProcessing' => 'queueJobProcessing', // Since Laravel 5.2
        'Illuminate\Queue\Events\JobProcessed' => 'queueJobProcessed', // Since Laravel 5.2
        'Illuminate\Queue\Events\JobExceptionOccurred' => 'queueJobExceptionOccurred', // Since Laravel 5.2
        'Illuminate\Queue\Events\WorkerStopping' => 'queueWorkerStopping', // Since Laravel 5.2
    ];

    /**
     * The Laravel event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * Indicates if we should we add SQL queries to the breadcrumbs.
     *
     * @var bool
     */
    private $recordSqlQueries;

    /**
     * Indicates if we should we add query bindings to the breadcrumbs.
     *
     * @var bool
     */
    private $recordSqlBindings;

    /**
     * Indicates if we should we add Laravel logs to the breadcrumbs.
     *
     * @var bool
     */
    private $recordLaravelLogs;

    /**
     * Indicates if we should we add queue info to the breadcrumbs.
     *
     * @var bool
     */
    private $recordQueueInfo;

    /**
     * EventHandler constructor.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
        $this->recordSqlQueries = true;
        $this->recordSqlBindings = true;
        $this->recordLaravelLogs = true;
        $this->recordQueueInfo = true;
    }

    /**
     * Attach all event handlers.
     */
    public function subscribe()
    {
        foreach (static::$eventHandlerMap as $eventName => $handler) {
            $this->events->listen($eventName, [$this, $handler]);
        }
    }

    /**
     * Attach all authentication event handlers.
     */
    public function subscribeAuthEvents()
    {
        foreach (static::$authEventHandlerMap as $eventName => $handler) {
            $this->events->listen($eventName, [$this, $handler]);
        }
    }

    /**
     * Attach all queue event handlers.
     */
    public function subscribeQueueEvents()
    {
        foreach (static::$queueEventHandlerMap as $eventName => $handler) {
            $this->events->listen($eventName, [$this, $handler]);
        }
    }

    /**
     * Pass through the event and capture any errors.
     *
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, $arguments)
    {
        $handlerMethod = $handlerMethod = "{$method}Handler";
        if ( ! method_exists($this, $handlerMethod)) {
            throw new RuntimeException("Missing event handler: {$handlerMethod}");
        }

        try {
            call_user_func_array([$this, $handlerMethod], $arguments);
        } catch (Exception $exception) {
            // Ignore
        }
    }

    /**
     * Until Laravel 5.1
     *
     * @param Route $route
     */
    protected function routerMatchedHandler(Route $route)
    {

        $routeName = $route->getName();
        $routeAction = $route->getActionName();
        if (empty($routeName) || $routeName === 'Closure') {
            $routeName = $route->uri();
        }
        Breadcrumbs::getInstance()->add([
            'route' => [
                'name' => $routeName,
                'action' => $routeAction,
            ]
        ]);
    }

    /**
     * Since Laravel 5.2
     *
     * @param \Illuminate\Routing\Events\RouteMatched $match
     */
    protected function routeMatchedHandler(RouteMatched $match)
    {
        $this->routerMatchedHandler($match->route);
    }

    /**
     * Until Laravel 5.1
     *
     * @param string $query
     * @param array $bindings
     * @param int $time
     * @param string $connectionName
     */
    protected function queryHandler($query, $bindings, $time, $connectionName)
    {
        if ( ! $this->recordSqlQueries) {
            return;
        }

        $data = ['connectionName' => $connectionName];

        if ($time !== null) {
            $data['time'] = $time;
        }
        $data['query'] = vsprintf(str_replace(array('?'), array('\'%s\''), $query), $bindings);;

        Breadcrumbs::getInstance()->add(['sql' => $data]);
    }

    /**
     * Since Laravel 5.2
     *
     * @param \Illuminate\Database\Events\QueryExecuted $query
     */
    protected function queryExecutedHandler(QueryExecuted $query)
    {
        if ( ! $this->recordSqlQueries) {
            return;
        }
        $data = ['connectionName' => $query->connectionName];

        if ($query->time !== null) {
            $data['time'] = $query->time;
        }
        $data['query'] = vsprintf(str_replace(array('?'), array('\'%s\''), $query->sql), $query->bindings);;

        Breadcrumbs::getInstance()->add(['sql' => $data]);
    }

    /**
     * Since Laravel 5.2
     *
     * @param \Illuminate\Queue\Events\JobProcessing $event
     */
    protected function queueJobProcessingHandler(JobProcessing $event)
    {
        if ( ! $this->recordQueueInfo) {
            return;
        }

        $job = [
            'job' => $event->job->getName(),
            'queue' => $event->job->getQueue(),
            'attempts' => $event->job->attempts(),
            'connection' => $event->connectionName,
        ];

        if (method_exists($event->job, 'resolveName')) {
            $job['resolved'] = $event->job->resolveName();
        }

        Breadcrumbs::getInstance()->add(['job' => $job]);
    }

    /**
     * Since Laravel 5.2
     *
     * @param \Illuminate\Queue\Events\JobProcessing $event
     */
    protected function queueWorkerStoppingHandler(WorkerStopping $event)
    {
        Breadcrumbs::getInstance()->add(['worker' => $event]);
    }

    /**
     * Since Laravel 5.5
     *
     * @param \Illuminate\Console\Events\CommandStarting $event
     */
    protected function commandStartingHandler(CommandStarting $event)
    {
        if ($event->command) {
            if ( ! $this->recordQueueInfo) {
                return;
            }
            Breadcrumbs::getInstance()->add([
                'command' => $event
            ]);
        }
    }

    /**
     * Since Laravel 5.5
     *
     * @param \Illuminate\Console\Events\CommandFinished $event
     */
    protected function commandFinishedHandler(CommandFinished $event)
    {
        file_put_contents(__DIR__ . '/errors.txt', '>>> commandFinishedHandler ' . json_encode($event) . PHP_EOL, FILE_APPEND);
        Breadcrumbs::getInstance()->add([
            'command' => $event
        ]);
    }
}
