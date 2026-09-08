<?php

namespace Hugomyb\FilamentErrorMailer\Notifications;

use Hugomyb\FilamentErrorMailer\Services\ErrorDetailsBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ErrorOccurred extends Mailable
{
    use Queueable, SerializesModels;

    public \Throwable $exception;
    public string $errorHash;

    /**
     * Details captured when the error occurred. The email is rendered from this
     * snapshot so it always matches the details page, even when the mail is
     * queued and the original request is long gone by rendering time.
     */
    public array $errorDetails;

    public function __construct(\Throwable $exception, string $errorHash, ?array $errorDetails = null)
    {
        $this->exception = $exception;
        $this->errorHash = $errorHash;
        $this->errorDetails = $errorDetails ?? app(ErrorDetailsBuilder::class)->build($exception, $errorHash);
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
                'errorDetails' => $this->errorDetails,
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
