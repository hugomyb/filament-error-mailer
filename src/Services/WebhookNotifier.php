<?php

namespace Hugomyb\FilamentErrorMailer\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookNotifier
{
    public static function send(string $url, array $payload)
    {
        if (!$url) {
            return false;
        }

        try {
            $response = Http::post($url, $payload);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send webhook notification: ' . $e->getMessage());
            return false;
        }
    }
}
