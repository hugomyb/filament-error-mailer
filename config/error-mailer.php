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
