<?php

return [
    'email' => [
        'recipient' => ['recipient1@example.com'],
        'bcc' => [],
        'cc' => [],
        'subject' => 'An error has occured - ' . env('APP_NAME'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes

    'webhooks' => [
        'discord' => env('ERROR_MAILER_DISCORD_WEBHOOK'),

        'message' => [
            'title' => 'Error Alert - ' . env('APP_NAME'),
            'description' => 'An error has occured in the application.',
            'error' => 'Error',
            'file' => 'File',
            'line' => 'Line',
            'details_link' => 'See more details'
        ],
    ],
];
