<?php

namespace Hugomyb\FilamentErrorMailer\Listeners;

use Hugomyb\FilamentErrorMailer\Notifications\ErrorOccurred;
use Hugomyb\FilamentErrorMailer\Services\DiscordWebhookBuilder;
use Hugomyb\FilamentErrorMailer\Services\ErrorDetailsBuilder;
use Hugomyb\FilamentErrorMailer\Services\ErrorFilter;
use Hugomyb\FilamentErrorMailer\Services\ErrorStorage;
use Hugomyb\FilamentErrorMailer\Services\WebhookNotifier;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyAdminOfError
{
    public function __construct(
        private ErrorFilter $errorFilter,
        private ErrorDetailsBuilder $errorDetailsBuilder,
        private ErrorStorage $errorStorage,
        private DiscordWebhookBuilder $discordWebhookBuilder,
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(MessageLogged $event): void
    {
        // Check if error should be ignored
        if ($this->errorFilter->shouldIgnore($event)) {
            return;
        }

        // Check if event has an exception
        if (!$this->errorFilter->hasException($event)) {
            return;
        }

        $exception = $event->context['exception'];
        $errorHash = $this->errorDetailsBuilder->generateHash($exception);

        // Check if error was recently notified (cooldown)
        if ($this->errorStorage->wasRecentlyNotified($errorHash)) {
            return;
        }

        // Build and store error details
        $errorDetails = $this->errorDetailsBuilder->build($exception, $errorHash);
        $this->errorStorage->store($errorHash, $errorDetails);

        // Send email notification
        $this->sendEmailNotification($exception, $errorHash);

        // Send webhook notification
        $this->sendWebhookNotification($exception, $errorHash);
    }

    /**
     * Send email notification to configured recipients.
     */
    private function sendEmailNotification(\Throwable $exception, string $errorHash): void
    {
        $recipients = config('error-mailer.email.recipient', []);
        $bccRecipients = config('error-mailer.email.bcc', []);
        $ccRecipients = config('error-mailer.email.cc', []);

        if (empty($recipients)) {
            Log::warning('No email recipients configured for error notifications.');
            return;
        }

        try {
            $mail = Mail::to($recipients);

            if (!empty($bccRecipients)) {
                $mail->bcc($bccRecipients);
            }

            if (!empty($ccRecipients)) {
                $mail->cc($ccRecipients);
            }

            $mail->send(new ErrorOccurred($exception, $errorHash));
        } catch (\Exception $e) {
            Log::error('Failed to send error notification email: ' . $e->getMessage());
        }
    }

    /**
     * Send webhook notification (Discord).
     */
    private function sendWebhookNotification(\Throwable $exception, string $errorHash): void
    {
        $webhookUrl = config('error-mailer.webhooks.discord');

        if (!$webhookUrl) {
            return;
        }

        $payload = $this->discordWebhookBuilder->build($exception, $errorHash);
        WebhookNotifier::send($webhookUrl, $payload);
    }
}
