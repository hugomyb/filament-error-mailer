<?php

use Hugomyb\FilamentErrorMailer\Listeners\NotifyAdminOfError;
use Hugomyb\FilamentErrorMailer\Notifications\ErrorOccurred;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->storagePath = storage_path('app/test-errors');
    config(['error-mailer.storage_path' => $this->storagePath]);
    config(['error-mailer.email.recipient' => ['test@example.com']]);
    config(['error-mailer.disabledOn' => []]);
    config(['app.env' => 'testing']);
    Mail::fake();
    Http::fake();
});

afterEach(function () {
    if (File::exists($this->storagePath)) {
        File::deleteDirectory($this->storagePath);
    }
});

it('sends email notification on error', function () {
    $exception = new \Exception('Test error');
    $event = new MessageLogged('error', 'Test error', ['exception' => $exception]);

    $listener = app(NotifyAdminOfError::class);
    $listener->handle($event);

    Mail::assertSent(ErrorOccurred::class, function ($mail) {
        return $mail->hasTo('test@example.com');
    });
});

it('stores error details to file', function () {
    $exception = new \Exception('Test error');
    $event = new MessageLogged('error', 'Test error', ['exception' => $exception]);

    $listener = app(NotifyAdminOfError::class);
    $listener->handle($event);

    $files = File::files($this->storagePath);
    expect($files)->toHaveCount(1);
});

it('respects cooldown period', function () {
    config(['error-mailer.cacheCooldown' => 10]);

    $exception = new \Exception('Test error');
    $event = new MessageLogged('error', 'Test error', ['exception' => $exception]);

    $listener = app(NotifyAdminOfError::class);

    // First notification
    $listener->handle($event);
    Mail::assertSent(ErrorOccurred::class, 1);

    // Second notification (should be ignored due to cooldown)
    $listener->handle($event);
    Mail::assertSent(ErrorOccurred::class, 1); // Still only 1
});



it('does not send notification in disabled environments', function () {
    config(['error-mailer.disabledOn' => ['testing']]);
    config(['app.env' => 'testing']);

    $exception = new \Exception('Test error');
    $event = new MessageLogged('error', 'Test error', ['exception' => $exception]);

    $listener = app(NotifyAdminOfError::class);
    $listener->handle($event);

    Mail::assertNothingSent();
});

it('ignores configured exception types', function () {
    config(['error-mailer.ignore.exceptions' => [\RuntimeException::class]]);

    $exception = new \RuntimeException('Runtime error');
    $event = new MessageLogged('error', 'Runtime error', ['exception' => $exception]);

    $listener = app(NotifyAdminOfError::class);
    $listener->handle($event);

    Mail::assertNothingSent();
});

it('sends to bcc and cc recipients', function () {
    config(['error-mailer.email.bcc' => ['bcc@example.com']]);
    config(['error-mailer.email.cc' => ['cc@example.com']]);

    $exception = new \Exception('Test error');
    $event = new MessageLogged('error', 'Test error', ['exception' => $exception]);

    $listener = app(NotifyAdminOfError::class);
    $listener->handle($event);

    Mail::assertSent(ErrorOccurred::class, function ($mail) {
        return $mail->hasBcc('bcc@example.com') && $mail->hasCc('cc@example.com');
    });
});

