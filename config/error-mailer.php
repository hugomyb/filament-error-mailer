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
];
