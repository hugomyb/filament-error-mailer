<?php

namespace Hugomyb\FilamentErrorMailer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ErrorOccurred extends Mailable
{
    use Queueable, SerializesModels;

    public \Throwable $exception;
    public string $errorHash;

    public function __construct(\Throwable $exception, string $errorHash)
    {
        $this->exception = $exception;
        $this->errorHash = $errorHash;
    }

    public function build()
    {
        $stackTrace = $this->formatStackTrace($this->exception);

        return $this->subject(config('error-mailer.email.subject'))
            ->markdown('error-mailer::error')
            ->with([
                'exception' => $this->exception,
                'stackTrace' => $stackTrace,
                'errorHash' => $this->errorHash,
            ]);
    }

    private function formatStackTrace(\Throwable $exception): string
    {
        $trace = $exception->getTraceAsString();
        $traceLines = explode("\n", $trace);
        $formattedTrace = [];

        foreach ($traceLines as $line) {
            // Extract key information from each line
            if (preg_match('/^#(\d+) (.*?):(.*)$/', $line, $matches)) {
                $number = $matches[1];
                $path = trim($matches[2]);
                $detail = trim($matches[3]);

                // Format each stack trace entry
                $formattedLine = "### $number $path\n$detail\n";
                $formattedTrace[] = $formattedLine;
            }
        }

        return implode("\n", $formattedTrace);
    }
}
