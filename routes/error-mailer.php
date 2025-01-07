<?php

use Hugomyb\FilamentErrorMailer\Controllers\ErrorDetailsController;
use Illuminate\Support\Facades\Route;

Route::get('/error-mailer/{errorId}', [ErrorDetailsController::class, 'show'])
    ->name('error.details');
