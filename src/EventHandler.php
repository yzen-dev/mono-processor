<?php

namespace MonoProcessor;

use MonoProcessor\Breadcrumbs;
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
     * @var array
     */
    protected static $queryEventHandlerMap = [
        'Illuminate\Database\Events\QueryExecuted' => 'queryExecuted',
    ];

    /**
     * Map event handlers to events.
     * @var array
     */
    protected static $consoleEventHandlerMap = [
        'Illuminate\Console\Events\CommandStarting' => 'commandStarting',
        'Illuminate\Console\Events\CommandFinished' => 'commandFinished',
    ];

    /**
     * Map route handlers to events.
     * @var array
     */
    protected static $routeEventHandlerMap = [
        'router.matched' => 'routerMatched',
        'Illuminate\Routing\Events\RouteMatched' => 'routeMatched',
    ];

    /**
     * Map queue event handlers to events.
     * @var array
     */
    protected static $queueEventHandlerMap = [
        'Illuminate\Queue\Events\JobProcessing' => 'queueJobProcessing',
        'Illuminate\Queue\Events\JobProcessed' => 'queueJobProcessed',
        'Illuminate\Queue\Events\JobExceptionOccurred' => 'queueJobExceptionOccurred',
        'Illuminate\Queue\Events\WorkerStopping' => 'queueWorkerStopping',
    ];

    /**
     * Map authentication event handlers to events.
     * @var array
     */
    protected static $authEventHandlerMap = [
        'Illuminate\Auth\Events\Authenticated' => 'authenticated',
    ];

    /**
     * The Laravel event dispatcher.
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $events;

    /**
     * EventHandler constructor.
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Attach all event handlers.
     */
    public function subscribe()
    {
        if (Config::getByKey('breadcrumbs')['route']) {
            $this->subscribeRoute();;
        }

        if (Config::getByKey('breadcrumbs')['sql']) {
            $this->subscribeQuery();
        }

        if (Config::getByKey('command')) {
            $this->subscribeConsole();
        }

        if (Config::getByKey('breadcrumbs')['auth']) {
            $this->subscribeAuthEvents();
        }

        if (Config::getByKey('breadcrumbs')['queue']) {
            $this->subscribeQueueEvents();
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
     * Attach all event handlers.
     */
    public function subscribeQuery()
    {
        foreach (static::$queryEventHandlerMap as $eventName => $handler) {
            $this->events->listen($eventName, [$this, $handler]);
        }
    }

    /**
     * Attach all event handlers.
     */
    public function subscribeConsole()
    {
        foreach (static::$consoleEventHandlerMap as $eventName => $handler) {
            $this->events->listen($eventName, [$this, $handler]);
        }
    }

    /**
     * Attach all event handlers.
     */
    public function subscribeRoute()
    {
        foreach (static::$routeEventHandlerMap as $eventName => $handler) {
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
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, $arguments)
    {
        $handlerMethod = $handlerMethod = "{$method}Handler";
        if (!method_exists($this, $handlerMethod)) {
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
     * @param Route $route
     */
    protected function routerMatchedHandler(Route $route)
    {
        $routeName = $route->getName();
        $routeAction = $route->getActionName();
        if (empty($routeName) || $routeName === 'Closure') {
            $routeName = $route->uri();
        }
        Breadcrumbs::getInstance()
            ->push(
                'route',
                [
                    'name' => $routeName,
                    'action' => $routeAction,
                ]);
    }

    /**
     * Since Laravel 5.2
     * @param \Illuminate\Routing\Events\RouteMatched $match
     */
    protected function routeMatchedHandler(RouteMatched $match)
    {
        $this->routerMatchedHandler($match->route);
    }

    /**
     * Until Laravel 5.1
     * @param string $query
     * @param array $bindings
     * @param int $time
     * @param string $connectionName
     */
    protected function queryHandler($query, $bindings, $time, $connectionName)
    {
        if (!Config::isEnabledValue('sql')) {
            return;
        }

        $data = ['connectionName' => $connectionName];

        if ($time !== null) {
            $data['time'] = $time;
        }
        $data['query'] = vsprintf(str_replace(['?'], ['\'%s\''], $query), $bindings);;

        Breadcrumbs::getInstance()
            ->push('sql', $data);
    }

    /**
     * Since Laravel 5.2
     * @param \Illuminate\Database\Events\QueryExecuted $query
     */
    protected function queryExecutedHandler(QueryExecuted $query)
    {
        if (!Config::getByKey('breadcrumbs')['sql']) {
            return;
        }
        $data = ['connectionName' => $query->connectionName];

        if ($query->time !== null) {
            $data['time'] = $query->time;
        }
        $data['query'] = vsprintf(str_replace(['?'], ['\'%s\''], $query->sql), $query->bindings);;

        Breadcrumbs::getInstance()
            ->push('sql', $data);
    }

    /**
     * Since Laravel 5.2
     * @param \Illuminate\Queue\Events\JobProcessing $event
     */
    protected function queueJobProcessingHandler(JobProcessing $event)
    {
        if (!Config::getByKey('breadcrumbs')['queue']) {
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

        Breadcrumbs::getInstance()
            ->push('job', $job);
    }

    protected function queueJobExceptionOccurredHandler(JobExceptionOccurred $event)
    {
        if (!Config::getByKey('breadcrumbs')['queue']) {
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

        Breadcrumbs::getInstance()
            ->push('job', $job);
    }

    /**
     * Since Laravel 5.2
     * @param \Illuminate\Queue\Events\JobProcessing $event
     */
    protected function queueWorkerStoppingHandler(WorkerStopping $event)
    {
        Breadcrumbs::getInstance()
            ->push('worker', $event);
    }

    /**
     * Since Laravel 5.5
     * @param \Illuminate\Console\Events\CommandStarting $event
     */
    protected function commandStartingHandler(CommandStarting $event)
    {
        if ($event->command) {
            if (Config::getByKey('command')) {
                return;
            }
            Breadcrumbs::getInstance()
                ->push('command', $event);
        }
    }

    /**
     * Since Laravel 5.5
     * @param \Illuminate\Console\Events\CommandFinished $event
     */
    protected function commandFinishedHandler(CommandFinished $event)
    {
        Breadcrumbs::getInstance()
            ->push('command', $event);
    }

    /**
     * Since Laravel 5.3
     * @param \Illuminate\Auth\Events\Authenticated $event
     */
    protected function authenticatedHandler(Authenticated $event)
    {
        Breadcrumbs::getInstance()
            ->push(
                'user',
                [
                    'id' => optional($event->user)->id,
                    'email' => optional($event->user)->email
                ]);
    }
}
