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

            $recipients = config()->has('error-mailer.email.recipient')
                ? (is_array(config('error-mailer.email.recipient')) ? config('error-mailer.email.recipient') : [config('error-mailer.email.recipient')])
                : ['destinataire@example.com'];

            $bccRecipients = config()->has('error-mailer.email.bcc')
                ? (is_array(config('error-mailer.email.bcc')) ? config('error-mailer.email.bcc') : [config('error-mailer.email.bcc')])
                : [];

            $ccRecipients = config()->has('error-mailer.email.cc')
                ? (is_array(config('error-mailer.email.cc')) ? config('error-mailer.email.cc') : [config('error-mailer.email.cc')])
                : [];

            if (isset($event->context['exception'])) {
                $errorHash = md5($event->context['exception']->getMessage() . $event->context['exception']->getFile());

                $cacheKey = 'error_mailer_' . $errorHash;
                $coolDownPeriod = config('error-mailer.cacheCooldown') ?? 10;

                if (!Cache::has($cacheKey)) {
                    $mail = Mail::to($recipients);
                    if($bccRecipients){
                        $mail->bcc($bccRecipients);
                    }

                    if($ccRecipients){
                        $mail->cc($ccRecipients);
                    }

                    $mail->send(new ErrorOccurred($event->context['exception']));
                    Cache::put($cacheKey, true, now()->addMinutes($coolDownPeriod));
                }
            }
        }
    }
}
