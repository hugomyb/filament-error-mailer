<?php

return [
    'email' => [
        'recipient' => ['recipient1@example.com', 'recipient2@example.com'],
        'bcc' => ['bcc1@example.com', 'bcc2@example.com'],
        'cc' => ['cc1@example.com', 'cc2@example.com'],
        'subject' => 'An error was occured - ' . env('APP_NAME'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes
];
