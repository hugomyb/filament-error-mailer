<?php

namespace Hugomyb\FilamentErrorMailer\Controllers;

use Illuminate\Support\Facades\Cache;

class ErrorDetailsController
{
    public function show($errorId)
    {
        $errorDetails = Cache::get("error_details_{$errorId}");

        if (!$errorDetails) {
            abort(404, 'Erreur non trouvée');
        }

        return view('error-mailer::details', ['error' => $errorDetails]);
    }
}
