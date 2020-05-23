<?php
declare(strict_types=1);

namespace MonoProcessor\Processors;


class RequestProcessor extends AbstractProcessor
{
    public function __invoke(array $record): array
    {
        if ( ! $this->isWrite($record['level_name'])) {
            return $record;
        }
    
        $record['extra']['request'] = [];
        $record['extra']['request'] += [
            'base_info' => [
                'ip' => $_SERVER['REMOTE_ADDR'],
                'http_method' => $_SERVER['REQUEST_METHOD'],
                'full_url' => $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
                'server' => $_SERVER['SERVER_NAME'],
                'url' => $_SERVER['REQUEST_URI'],
            ]
        ];
    
        $request = request();
    
        if ($request) {
            $record['extra']['request'] += [
                'header' => $request
            ];
        }
        if ($request) {
            $record['extra']['request'] += [
                'body' => $request->all()
            ];
        }
    
        return $record;
    }
}
