<?php

namespace Hugomyb\FilamentErrorMailer\Listeners;


use Hugomyb\FilamentErrorMailer\Notifications\ErrorOccurred;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfError
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $coolDownPeriod = 0;

        if (!in_array(env('APP_ENV'), config('error-mailer.disabledOn'))) {

            $recipient = config()->has('error-mailer.email.recipient')
                ? config('error-mailer.email.recipient')
                : 'destinataire@example.com';

            if (isset($event->context['exception'])) {
                $errorHash = md5($event->context['exception']->getMessage() . $event->context['exception']->getFile());

                $cacheKey = 'error_mailer_' . $errorHash;
                $coolDownPeriod = config('error-mailer.cacheCooldown') ?? 10;

                if (!Cache::has($cacheKey)) {
                    Mail::to($recipient)->send(new ErrorOccurred($event->context['exception']));
                    Cache::put($cacheKey, true, now()->addMinutes($coolDownPeriod));
                }
            }
        }
    }
}
