<?php

use Hugomyb\FilamentErrorMailer\Services\ErrorStorage;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->errorStorage = new ErrorStorage();
    $this->storagePath = storage_path('app/test-errors');
    config(['error-mailer.storage_path' => $this->storagePath]);
});

afterEach(function () {
    if (File::exists($this->storagePath)) {
        File::deleteDirectory($this->storagePath);
    }
});

it('can store error details', function () {
    $errorHash = 'test-hash-123';
    $errorDetails = [
        'id' => $errorHash,
        'message' => 'Test error message',
        'file' => '/path/to/file.php',
        'line' => 42,
    ];

    $result = $this->errorStorage->store($errorHash, $errorDetails);

    expect($result)->toBeTrue();
    expect(File::exists("{$this->storagePath}/{$errorHash}.json"))->toBeTrue();
});

it('can retrieve stored error details', function () {
    $errorHash = 'test-hash-456';
    $errorDetails = [
        'id' => $errorHash,
        'message' => 'Another test error',
        'file' => '/path/to/another.php',
        'line' => 100,
    ];

    $this->errorStorage->store($errorHash, $errorDetails);
    $retrieved = $this->errorStorage->get($errorHash);

    expect($retrieved)->toBeArray();
    expect($retrieved['id'])->toBe($errorHash);
    expect($retrieved['message'])->toBe('Another test error');
});

it('returns null for non-existent error', function () {
    $result = $this->errorStorage->get('non-existent-hash');

    expect($result)->toBeNull();
});

it('detects recently notified errors', function () {
    $errorHash = 'recent-error';
    $errorDetails = [
        'id' => $errorHash,
        'message' => 'Recent error',
        'last_notified_at' => now()->toDateTimeString(),
    ];

    config(['error-mailer.cacheCooldown' => 10]);
    $this->errorStorage->store($errorHash, $errorDetails);

    expect($this->errorStorage->wasRecentlyNotified($errorHash))->toBeTrue();
});

it('detects errors outside cooldown period', function () {
    $errorHash = 'old-error';
    $errorDetails = [
        'id' => $errorHash,
        'message' => 'Old error',
        'last_notified_at' => now()->subMinutes(15)->toDateTimeString(),
    ];

    config(['error-mailer.cacheCooldown' => 10]);
    $this->errorStorage->store($errorHash, $errorDetails);

    expect($this->errorStorage->wasRecentlyNotified($errorHash))->toBeFalse();
});

