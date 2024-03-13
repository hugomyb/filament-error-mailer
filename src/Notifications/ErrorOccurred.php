<?php

namespace Hugomyb\FilamentErrorMailer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class ErrorOccurred extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function build()
    {
        $stackTrace = $this->formatStackTrace($this->exception);

        return $this->subject(config('error-mailer.email.subject'))
            ->markdown('error-mailer::error')
            ->with(['exception' => $this->exception, 'stackTrace' => $stackTrace]);
    }

    function formatStackTrace($exception) {
        $trace = $exception->getTraceAsString();
        $traceLines = explode("\n", $trace);
        $formattedTrace = [];

        foreach ($traceLines as $line) {
            // Extraction des informations clés de chaque ligne
            if (preg_match('/^#(\d+) (.*?):(.*)$/', $line, $matches)) {
                $number = $matches[1];
                $path = trim($matches[2]);
                $detail = trim($matches[3]);

                // Formatage de chaque entrée de la stack trace
                $formattedLine = "### $number $path\n$detail\n";
                $formattedTrace[] = $formattedLine;
            }
        }

        return implode("\n", $formattedTrace);
    }
}
