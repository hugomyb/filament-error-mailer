<?php

use Hugomyb\FilamentErrorMailer\Services\ErrorFilter;
use Illuminate\Log\Events\MessageLogged;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->errorFilter = new ErrorFilter();
});

it('ignores errors in disabled environments', function () {
    config(['app.env' => 'local']);
    config(['error-mailer.disabledOn' => ['local', 'testing']]);

    $event = new MessageLogged('error', 'Test error', []);

    expect($this->errorFilter->shouldIgnore($event))->toBeTrue();
});

it('does not ignore errors in enabled environments', function () {
    config(['app.env' => 'production']);
    config(['error-mailer.disabledOn' => ['local', 'testing']]);

    $event = new MessageLogged('error', 'Test error', ['exception' => new \Exception('Test')]);

    expect($this->errorFilter->shouldIgnore($event))->toBeFalse();
});

it('ignores errors with ignored log levels', function () {
    config(['error-mailer.ignore.levels' => ['debug', 'info']]);

    $event = new MessageLogged('debug', 'Debug message', []);

    expect($this->errorFilter->shouldIgnore($event))->toBeTrue();
});

it('does not ignore errors with non-ignored log levels', function () {
    config(['error-mailer.ignore.levels' => ['debug', 'info']]);

    $event = new MessageLogged('error', 'Error message', ['exception' => new \Exception('Test')]);

    expect($this->errorFilter->shouldIgnore($event))->toBeFalse();
});

it('ignores specific exception types', function () {
    config(['error-mailer.ignore.exceptions' => [NotFoundHttpException::class]]);

    $event = new MessageLogged('error', 'Not found', [
        'exception' => new NotFoundHttpException('Page not found'),
    ]);

    expect($this->errorFilter->shouldIgnore($event))->toBeTrue();
});

it('does not ignore non-configured exception types', function () {
    config(['error-mailer.ignore.exceptions' => [NotFoundHttpException::class]]);

    $event = new MessageLogged('error', 'Server error', [
        'exception' => new \RuntimeException('Runtime error'),
    ]);

    expect($this->errorFilter->shouldIgnore($event))->toBeFalse();
});

it('detects events with exceptions', function () {
    $event = new MessageLogged('error', 'Error message', [
        'exception' => new \Exception('Test exception'),
    ]);

    expect($this->errorFilter->hasException($event))->toBeTrue();
});

it('detects events without exceptions', function () {
    $event = new MessageLogged('info', 'Info message', []);

    expect($this->errorFilter->hasException($event))->toBeFalse();
});

