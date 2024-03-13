<?php

namespace Hugomyb\FilamentErrorMailer;

use Hugomyb\FilamentErrorMailer\Listeners\NotifyAdminOfError;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Log\Events\MessageLogged;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MessageLogged::class => [
            NotifyAdminOfError::class,
        ],
    ];
}
