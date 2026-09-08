<?php

use Hugomyb\FilamentErrorMailer\Services\ErrorDetailsBuilder;

beforeEach(function () {
    $this->builder = new ErrorDetailsBuilder();
});

it('generates consistent hash for same error', function () {
    $exception = new \Exception('Test error');

    $hash1 = $this->builder->generateHash($exception);
    $hash2 = $this->builder->generateHash($exception);

    expect($hash1)->toBe($hash2);
});

it('generates different hashes for different errors', function () {
    $exception1 = new \Exception('Error 1');
    $exception2 = new \Exception('Error 2');

    $hash1 = $this->builder->generateHash($exception1);
    $hash2 = $this->builder->generateHash($exception2);

    expect($hash1)->not->toBe($hash2);
});

it('builds complete error details', function () {
    $exception = new \Exception('Test error message');
    $errorHash = $this->builder->generateHash($exception);

    $details = $this->builder->build($exception, $errorHash);

    expect($details)->toBeArray();
    expect($details)->toHaveKeys([
        'id',
        'message',
        'file',
        'line',
        'url',
        'method',
        'ip',
        'userAgent',
        'referrer',
        'requestTime',
        'requestUri',
        'authUser',
        'stackTrace',
        'last_notified_at',
    ]);
    expect($details['id'])->toBe($errorHash);
    expect($details['message'])->toBe('Test error message');
});

it('formats request time in the application timezone', function () {
    config()->set('app.timezone', 'Europe/Paris');
    request()->server->set('REQUEST_TIME', 1757328694);

    $exception = new \Exception('Test error');
    $errorHash = $this->builder->generateHash($exception);
    $details = $this->builder->build($exception, $errorHash);

    // 1757328694 is 10:51:34 UTC, i.e. 12:51:34 in Europe/Paris
    expect($details['requestTime'])->toBe('2025-09-08 12:51:34');
});

it('falls back to the current time when REQUEST_TIME is unavailable', function () {
    config()->set('app.timezone', 'Europe/Paris');
    request()->server->remove('REQUEST_TIME');

    $exception = new \Exception('Test error');
    $details = $this->builder->build($exception, $this->builder->generateHash($exception));

    expect($details['requestTime'])->toBe(now()->toDateTimeString());
});

it('sets null for auth user when not logged in', function () {
    $exception = new \Exception('Test error');
    $errorHash = $this->builder->generateHash($exception);
    $details = $this->builder->build($exception, $errorHash);

    expect($details['authUser'])->toBeNull();
});

