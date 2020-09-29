<?php

return [
    // stack output when an error occurs
    'stacktrace' => true,

    // memory peak at runtime
    'memoryPeak' => true,

    // information about the current branch and commit
    'git' => true,

    // php info (version)
    'phpinfo' => true,

    // Output of additional information in the format JSON_PRETTY_PRINT
    'json_format' => true,

    // Listen console commands
    'command' => true,

    // Logging of the received request
    'request' => [
        'base_info' => true,
        'header' => true,
        'body' => true,
    ],

    // What breadcrumbs do you need to collect
    'breadcrumbs' => [
        // auth info
        'auth' => true,

        // List of sql queries
        'sql' => true,

        // route info
        'route' => true,

        // queue info (WIP)
        'queue' => true,
    ],

    // Levels for which you want to display
    'levels' => [
        'ERROR',
        'EMERGENCY'
    ]
];
