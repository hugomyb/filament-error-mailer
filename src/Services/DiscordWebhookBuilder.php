<?php

namespace Hugomyb\FilamentErrorMailer\Services;

class DiscordWebhookBuilder
{
    /**
     * Build Discord webhook payload.
     */
    public function build(\Throwable $exception, string $errorHash): array
    {
        return [
            'embeds' => [
                [
                    'title' => config('error-mailer.webhooks.message.title') ?? 'Error Alert - ' . config('app.name'),
                    'description' => config('error-mailer.webhooks.message.description') ?? 'An error has occurred in the application.',
                    'color' => 16711680, // Red color
                    'fields' => [
                        [
                            'name' => config('error-mailer.webhooks.message.error') ?? 'Error',
                            'value' => $exception->getMessage() ?? 'N/A',
                            'inline' => false,
                        ],
                        [
                            'name' => config('error-mailer.webhooks.message.file') ?? 'File',
                            'value' => $exception->getFile() ?? 'N/A',
                            'inline' => false,
                        ],
                        [
                            'name' => config('error-mailer.webhooks.message.line') ?? 'Line',
                            'value' => (string) ($exception->getLine() ?? 'N/A'),
                            'inline' => false,
                        ],
                        [
                            'name' => '',
                            'value' => '[' . (config('error-mailer.webhooks.message.details_link') ?? 'See more details') . '](' . route('error.details', ['errorId' => $errorHash]) . ')',
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
    }
}

