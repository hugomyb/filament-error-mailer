<?php

return [
    'email' => [
        'recipient' => 'recipient@example.com',
        'subject' => 'An error was occured - ' . env('APP_NAME'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes
];
