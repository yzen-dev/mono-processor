<?php
return [
    'stacktrace' => true,
    'memoryPeak' => true,
    'git' => true,
    'phpinfo' => true,
    'route' => true,

    'json_format' => true,

    'request' => [
        'base_info' => true,
        'header' => true,
        'body' => true,
    ],

    'breadcrumbs' => [
        'auth' => true,
        'sql' => true,
        'route' => true,
        'queue' => true,
    ],
    'levels' => [
        'ERROR',
        'EMERGENCY'
    ]
];
