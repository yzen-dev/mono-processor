<?php

declare(strict_types=1);

namespace MonoProcessor\Processors;

use MonoProcessor\Config;

/**
 * Class RequestProcessor
 * @package MonoProcessor\Processors
 */
class RequestProcessor extends AbstractProcessor
{
    /**
     * Add in extra request info
     *
     * @param array $record
     * @return array<mixed>
     */
    public function __invoke(array $record): array
    {
        if (
            !$this->isWrite($record['level_name']) ||
            Config::getByKey('request')['base_info'] ||
            Config::getByKey('request')['header'] ||
            Config::getByKey('request')['body']
        ) {
            return $record;
        }

        $record['extra']['request'] = [];

        if (Config::getByKey('request')['base_info']) {
            $record['extra']['request'] += [
                'base_info' => [
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'http_method' => $_SERVER['REQUEST_METHOD'],
                    'full_url' => $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                    'server' => $_SERVER['SERVER_NAME'],
                    'url' => $_SERVER['REQUEST_URI'],
                ],
            ];
        }

        $request = request();
        if ($request && Config::getByKey('request')['header']) {
            $record['extra']['request'] += [
                'header' => $request->header(),
            ];
        }
        if ($request && Config::getByKey('request')['body']) {
            $record['extra']['request'] += [
                'body' => $request->all(),
            ];
        }

        return $record;
    }
}
