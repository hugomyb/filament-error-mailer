<?php

namespace Hugomyb\FilamentErrorMailer\Services;

use Illuminate\Log\Events\MessageLogged;

class ErrorFilter
{
    /**
     * Check if the error should be ignored based on configuration.
     */
    public function shouldIgnore(MessageLogged $event): bool
    {
        // Check if environment is disabled
        if (in_array(config('app.env'), config('error-mailer.disabledOn', []))) {
            return true;
        }

        // Check if log level should be ignored
        $ignoredLevels = config('error-mailer.ignore.levels', []);
        if (in_array($event->level, $ignoredLevels)) {
            return true;
        }

        // Check if exception type should be ignored
        if (isset($event->context['exception']) && $event->context['exception'] instanceof \Throwable) {
            $ignoredExceptions = config('error-mailer.ignore.exceptions', []);
            foreach ($ignoredExceptions as $ignoredException) {
                if ($event->context['exception'] instanceof $ignoredException) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if the event contains an exception.
     */
    public function hasException(MessageLogged $event): bool
    {
        return isset($event->context['exception']) && $event->context['exception'] instanceof \Throwable;
    }
}

