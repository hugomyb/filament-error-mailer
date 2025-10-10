<?php

namespace Hugomyb\FilamentErrorMailer\Listeners;


use Hugomyb\FilamentErrorMailer\Notifications\ErrorOccurred;
use Hugomyb\FilamentErrorMailer\Services\WebhookNotifier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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
        if (!in_array(env('APP_ENV'), config('error-mailer.disabledOn'))) {
            $recipients = config('error-mailer.email.recipient', ['destinataire@example.com']);
            $bccRecipients = config('error-mailer.email.bcc', []);
            $ccRecipients = config('error-mailer.email.cc', []);

            if (isset($event->context['exception']) && $event->context['exception'] instanceof \Throwable) {
                $errorHash = md5($event->context['exception']->getMessage() . $event->context['exception']->getFile());
                $storagePath = config('error-mailer.storage_path');

                $errorFile = "{$storagePath}/{$errorHash}.json";

                $cacheCooldown = config('error-mailer.cacheCooldown', 10);
                $lastNotificationTime = null;

                if (file_exists($errorFile)) {
                    $existingError = json_decode(file_get_contents($errorFile), true);
                    $lastNotificationTime = $existingError['last_notified_at'] ?? null;

                    if ($lastNotificationTime && now()->diffInMinutes($lastNotificationTime, true) < $cacheCooldown) {
                        return;
                    }
                }

                $errorDetails = [
                    'id' => $errorHash,
                    'message' => $event->context['exception']->getMessage(),
                    'file' => $event->context['exception']->getFile(),
                    'line' => $event->context['exception']->getLine(),
                    'url' => url(request()->getPathInfo()),
                    'method' => request()->method(),
                    'ip' => request()->ip(),
                    'userAgent' => request()->userAgent(),
                    'referrer' => request()->header('referer') ?? 'N/A',
                    'requestTime' => \Carbon\Carbon::createFromTimestamp(request()->server('REQUEST_TIME'))->toDateTimeString(),
                    'requestUri' => request()->server('REQUEST_URI') ?? 'N/A',
                    'authUser' => auth()->check() ? [
                        'id' => auth()->id(),
                        'name' => auth()->user()->name ?? "",
                        'email' => auth()->user()->email ?? "",
                    ] : null,
                    'stackTrace' => $event->context['exception']->getTraceAsString(),
                    'last_notified_at' => now()->toDateTimeString(),
                ];

                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                file_put_contents($errorFile, json_encode($errorDetails, JSON_PRETTY_PRINT));

                $mail = Mail::to($recipients);
                if ($bccRecipients) {
                    $mail->bcc($bccRecipients);
                }
                if ($ccRecipients) {
                    $mail->cc($ccRecipients);
                }
                $mail->send(new ErrorOccurred($event->context['exception'], $errorHash));

                $webhookUrl = config('error-mailer.webhooks.discord');
                if ($webhookUrl) {
                    $payload = [
                        'embeds' => [
                            [
                                'title' => config('error-mailer.webhooks.message.title') ?? 'Error Alert - ' . config('app.name'),
                                'description' => config('error-mailer.webhooks.message.description') ?? 'An error has occurred in the application.',
                                'color' => 16711680,
                                'fields' => [
                                    [
                                        'name' => config('error-mailer.webhooks.message.error') ?? 'Error',
                                        'value' => $event->context['exception']->getMessage() ?? 'N/A',
                                        'inline' => false,
                                    ],
                                    [
                                        'name' => config('error-mailer.webhooks.message.file') ?? 'File',
                                        'value' => $event->context['exception']->getFile() ?? 'N/A',
                                        'inline' => false,
                                    ],
                                    [
                                        'name' => config('error-mailer.webhooks.message.line') ?? 'Line',
                                        'value' => $event->context['exception']->getLine() ?? 'N/A',
                                        'inline' => false,
                                    ],
                                    [
                                        'name' => '',
                                        'value' => "[" . (config('error-mailer.webhooks.message.details_link') ?? 'See more details') . "](" . route('error.details', ['errorId' => $errorHash]) . ")",
                                        'inline' => false,
                                    ],
                                ],
                                'footer' => [
                                    'text' => config('app.name') . ' - ' . config('app.url'),
                                ],
                                'timestamp' => now()->toIso8601String(),
                            ],
                        ],
                    ];

                    WebhookNotifier::send($webhookUrl, $payload);
                } else {
                    Log::warning('Discord webhook is not configured or is null. Skipping webhook notification.');
                }
            }
        }
    }
}
