<?php

use Hugomyb\FilamentErrorMailer\Http\Controllers\ErrorDetailsController;
use Illuminate\Support\Facades\Route;
use Filament\Facades\Filament;

Route::middleware(['web'])
    ->get('/error-mailer/{errorId}', function (string $errorId) {
        if (!Filament::auth()->check()) {
            return redirect()->guest(Filament::getLoginUrl());
        }

        return app(ErrorDetailsController::class)->show($errorId);
    })
    ->name('error.details');
