<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Typesense Client Settings
    |--------------------------------------------------------------------------
    |
    | Connection settings passed directly to the Typesense PHP client.
    | See: https://typesense.org/docs/guide/configure-typesense.html
    |
    */
    'client_settings' => [
        'api_key' => env('TYPESENSE_API_KEY', ''),
        'nodes'   => [
            [
                'host'     => env('TYPESENSE_HOST', 'localhost'),
                'port'     => env('TYPESENSE_PORT', '8108'),
                'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
            ],
        ],
        'connection_timeout_seconds' => (int) env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Slow Response Threshold
    |--------------------------------------------------------------------------
    |
    | If the health check response takes longer than this many seconds, the
    | result will be marked as a warning instead of OK.
    |
    */
    'timeout_seconds' => 5,
];
