<?php

namespace Hugomyb\FilamentErrorMailer;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentErrorMailerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-error-mailer';
    }

    public function register(Panel $panel): void
    {
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        app()->register(EventServiceProvider::class);
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
