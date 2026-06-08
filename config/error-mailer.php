<?php

return [
    'email' => [
        'recipient' => ['recipient1@example.com'],
        'bcc' => [],
        'cc' => [],
        'subject' => 'An error has occurred - ' . config('app.name'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes

    'webhooks' => [
        'discord' => env('ERROR_MAILER_DISCORD_WEBHOOK'),

        /*
        |----------------------------------------------------------------------
        | Generic Webhook Endpoints
        |----------------------------------------------------------------------
        |
        | Additional webhook URLs the plugin will POST the raw error details
        | JSON to. Useful for forwarding errors to Slack-via-app, n8n, Zapier,
        | a custom API, etc. You may either list URLs directly or define them
        | via the ERROR_MAILER_WEBHOOK_URLS env variable as a comma-separated
        | list (e.g. https://a.example.com/hook,https://b.example.com/hook).
        |
        */
        'endpoints' => array_values(array_filter(array_map('trim', explode(',', (string) env('ERROR_MAILER_WEBHOOK_URLS', ''))))),

        'message' => [
            'title' => 'Error Alert - ' . config('app.name'),
            'description' => 'An error has occurred in the application.',
            'error' => 'Error',
            'file' => 'File',
            'line' => 'Line',
            'details_link' => 'See more details'
        ],
    ],

    'storage_path' => storage_path('app/errors'),

    /*
    |--------------------------------------------------------------------------
    | Error Filtering
    |--------------------------------------------------------------------------
    |
    | Configure which errors should be ignored.
    |
    */
    'ignore' => [
        'levels' => [
            // 'debug',
            // 'info',
            // 'notice',
            // 'warning',
        ],
        'exceptions' => [
            // \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            // \Illuminate\Validation\ValidationException::class,
        ],
    ],
];
