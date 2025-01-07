<?php

namespace Hugomyb\FilamentErrorMailer\Controllers;

use Illuminate\Support\Facades\Cache;

class ErrorDetailsController
{
    public function show($errorHash)
    {
        $storagePath = config('error-mailer.storage_path');
        $errorFile = "{$storagePath}/{$errorHash}.json";

        if (!file_exists($errorFile)) {
            abort(404, 'Error not found');
        }

        $error = json_decode(file_get_contents($errorFile), true);

        return view('error-mailer::details', ['error' => $error]);
    }
}
