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

it('sets null for auth user when not logged in', function () {
    $exception = new \Exception('Test error');
    $errorHash = $this->builder->generateHash($exception);
    $details = $this->builder->build($exception, $errorHash);

    expect($details['authUser'])->toBeNull();
});

