<?php

use Hugomyb\FilamentErrorMailer\Services\DiscordWebhookBuilder;

beforeEach(function () {
    $this->builder = new DiscordWebhookBuilder();
    config(['app.name' => 'Test App']);
    config(['app.url' => 'https://test.app']);
});

it('builds valid discord webhook payload', function () {
    $exception = new \Exception('Test error message');
    $errorHash = 'test-hash-123';

    $payload = $this->builder->build($exception, $errorHash);

    expect($payload)->toBeArray();
    expect($payload)->toHaveKey('embeds');
    expect($payload['embeds'])->toBeArray();
    expect($payload['embeds'])->toHaveCount(1);
});

it('includes error details in webhook payload', function () {
    $exception = new \Exception('Test error message');
    $errorHash = 'test-hash-123';

    $payload = $this->builder->build($exception, $errorHash);
    $embed = $payload['embeds'][0];

    expect($embed)->toHaveKeys(['title', 'description', 'color', 'fields', 'footer', 'timestamp']);
    expect($embed['color'])->toBe(16711680); // Red color
});

it('includes error message in fields', function () {
    $exception = new \Exception('Test error message');
    $errorHash = 'test-hash-123';

    $payload = $this->builder->build($exception, $errorHash);
    $fields = $payload['embeds'][0]['fields'];

    $errorField = collect($fields)->firstWhere('name', 'Error');
    expect($errorField)->not->toBeNull();
    expect($errorField['value'])->toBe('Test error message');
});

it('includes file and line in fields', function () {
    $exception = new \Exception('Test error');
    $errorHash = 'test-hash-123';

    $payload = $this->builder->build($exception, $errorHash);
    $fields = $payload['embeds'][0]['fields'];

    $fileField = collect($fields)->firstWhere('name', 'File');
    $lineField = collect($fields)->firstWhere('name', 'Line');

    expect($fileField)->not->toBeNull();
    expect($lineField)->not->toBeNull();
});

it('uses custom webhook message configuration', function () {
    config(['error-mailer.webhooks.message.title' => 'Custom Error Title']);
    config(['error-mailer.webhooks.message.description' => 'Custom description']);

    $exception = new \Exception('Test error');
    $errorHash = 'test-hash-123';

    $payload = $this->builder->build($exception, $errorHash);
    $embed = $payload['embeds'][0];

    expect($embed['title'])->toBe('Custom Error Title');
    expect($embed['description'])->toBe('Custom description');
});

