<?php

namespace Hugomyb\FilamentErrorMailer\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ErrorStorage
{
    /**
     * Store error details to a JSON file.
     */
    public function store(string $errorHash, array $errorDetails): bool
    {
        $storagePath = config('error-mailer.storage_path');

        try {
            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0755, true);
            }

            $errorFile = "{$storagePath}/{$errorHash}.json";
            File::put($errorFile, json_encode($errorDetails, JSON_PRETTY_PRINT));

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to store error details: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get error details from storage.
     */
    public function get(string $errorHash): ?array
    {
        $storagePath = config('error-mailer.storage_path');
        $errorFile = "{$storagePath}/{$errorHash}.json";

        if (!File::exists($errorFile)) {
            return null;
        }

        $content = File::get($errorFile);
        return json_decode($content, true);
    }

    /**
     * Check if error was recently notified (within cooldown period).
     */
    public function wasRecentlyNotified(string $errorHash): bool
    {
        $error = $this->get($errorHash);

        if (!$error || !isset($error['last_notified_at'])) {
            return false;
        }

        $cacheCooldown = config('error-mailer.cacheCooldown', 10);
        $lastNotificationTime = $error['last_notified_at'];

        return now()->diffInMinutes($lastNotificationTime, true) < $cacheCooldown;
    }
}

