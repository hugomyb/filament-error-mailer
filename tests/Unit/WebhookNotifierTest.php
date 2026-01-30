<?php

use Hugomyb\FilamentErrorMailer\Services\WebhookNotifier;

it('returns false when webhook url is empty', function () {
    $result = WebhookNotifier::send('', ['content' => 'Test']);

    expect($result)->toBeFalse();
});

