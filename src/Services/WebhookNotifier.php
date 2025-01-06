<?php

namespace Hugomyb\FilamentErrorMailer\Services;

use Illuminate\Support\Facades\Http;

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
            return false;
        }
    }
}
