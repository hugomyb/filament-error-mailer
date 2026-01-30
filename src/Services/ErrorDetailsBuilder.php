<?php

namespace Hugomyb\FilamentErrorMailer\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class ErrorDetailsBuilder
{
    /**
     * Build error details array from exception.
     */
    public function build(\Throwable $exception, string $errorHash): array
    {
        $appTrace = $this->findFirstApplicationTrace($exception);

        return [
            'id' => $errorHash,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'appFile' => $appTrace['file'] ?? null,
            'appLine' => $appTrace['line'] ?? null,
            'url' => url(request()->getPathInfo()),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'userAgent' => request()->userAgent(),
            'referrer' => request()->header('referer') ?? 'N/A',
            'requestTime' => Carbon::createFromTimestamp(request()->server('REQUEST_TIME'))->toDateTimeString(),
            'requestUri' => request()->server('REQUEST_URI') ?? 'N/A',
            'authUser' => $this->getAuthenticatedUser(),
            'stackTrace' => $exception->getTraceAsString(),
            'last_notified_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Generate a unique hash for the error.
     */
    public function generateHash(\Throwable $exception): string
    {
        return md5($exception->getMessage() . $exception->getFile());
    }

    /**
     * Find the first trace entry from application code (not vendor).
     */
    private function findFirstApplicationTrace(\Throwable $exception): array
    {
        $basePath = base_path();
        $vendorPath = base_path('vendor');

        // Check exception file first
        $exceptionFile = $exception->getFile();
        if (str_starts_with($exceptionFile, $basePath) && !str_starts_with($exceptionFile, $vendorPath)) {
            return [
                'file' => $exceptionFile,
                'line' => $exception->getLine(),
            ];
        }

        // Search in stack trace
        foreach ($exception->getTrace() as $trace) {
            if (!isset($trace['file'])) {
                continue;
            }

            // Skip vendor files
            if (str_starts_with($trace['file'], $vendorPath)) {
                continue;
            }

            // Found application code
            if (str_starts_with($trace['file'], $basePath)) {
                return [
                    'file' => $trace['file'],
                    'line' => $trace['line'] ?? 0,
                ];
            }
        }

        return [];
    }

    /**
     * Get authenticated user details if available.
     */
    private function getAuthenticatedUser(): ?array
    {
        if (!auth()->check()) {
            return null;
        }

        return [
            'id' => auth()->id(),
            'name' => auth()->user()->name ?? '',
            'email' => auth()->user()->email ?? '',
        ];
    }
}

