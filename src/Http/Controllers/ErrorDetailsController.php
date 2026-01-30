<?php

namespace Hugomyb\FilamentErrorMailer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ErrorDetailsController extends Controller
{
    public function show(string $errorId)
    {
        $storagePath = config('error-mailer.storage_path');
        $errorFile = "{$storagePath}/{$errorId}.json";

        if (!file_exists($errorFile)) {
            abort(404, 'Error not found');
        }

        $errorContent = file_get_contents($errorFile);
        if ($errorContent === false) {
            abort(500, 'Unable to read error file');
        }

        $error = json_decode($errorContent, true);
        if ($error === null) {
            abort(500, 'Invalid error data');
        }

        return view('error-mailer::details', ['error' => $error]);
    }
}

