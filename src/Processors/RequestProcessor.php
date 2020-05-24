<?php
declare(strict_types = 1);

namespace MonoProcessor\Processors;


use MonoProcessor\Config;

class RequestProcessor extends AbstractProcessor
{
    public function __invoke(array $record) : array
    {
        if (!$this->isWrite($record['level_name']) ||
            !Config::isEnabledValue('request.base_info') ||
            !Config::isEnabledValue('request.header') ||
            !Config::isEnabledValue('request.body')) {
            return $record;
        }

        $record['extra']['request'] = [];

        if (Config::isEnabledValue('request.base_info')) {
            $record['extra']['request'] += [
                'base_info' => [
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'http_method' => $_SERVER['REQUEST_METHOD'],
                    'full_url' => $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                    'server' => $_SERVER['SERVER_NAME'],
                    'url' => $_SERVER['REQUEST_URI'],
                ]
            ];
        }

        $request = request();

        if ($request && Config::isEnabledValue('request.header')) {
            $record['extra']['request'] += [
                'header' => $request
            ];
        }
        if ($request && Config::isEnabledValue('request.body')) {
            $record['extra']['request'] += [
                'body' => $request->all()
            ];
        }

        return $record;
    }
}
