<?php
return [
    // stack output when an error occurs2
    'stacktrace' => true,
    
    // memory peak at runtime
    'memoryPeak' => true,
    
    // information about the current branch and commit
    'git' => true,
    
    // php info (version)
    'phpinfo' => true,

    // Output of additional information in the format JSON_PRETTY_PRINT
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
    
    // levels for which you want to display
    'levels' => [
        'ERROR',
        'EMERGENCY'
    ]
];
