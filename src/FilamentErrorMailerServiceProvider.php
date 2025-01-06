<?php

namespace Hugomyb\FilamentErrorMailer;

use Filament\Support\Assets\Asset;
use Hugomyb\FilamentErrorMailer\Commands\FilamentErrorMailerCommand;
use Hugomyb\FilamentErrorMailer\Commands\PublishErrorMailerConfig;
use Hugomyb\FilamentErrorMailer\Listeners\NotifyAdminOfError;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Log\Events\MessageLogged;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentErrorMailerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'error-mailer';

    public static string $viewNamespace = 'error-mailer';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasRoutes($this->getRoutes())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('hugomyb/filament-error-mailer');
            });

        if (file_exists($package->basePath("/../config/error-mailer.php"))) {
            $package->hasConfigFile();
        }

//        if (file_exists($package->basePath('/../database/migrations'))) {
//            $package->hasMigrations($this->getMigrations());
//        }
//
//        if (file_exists($package->basePath('/../resources/lang'))) {
//            $package->hasTranslations();
//        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {

    }

    public function packageBooted(): void
    {
        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-error-mailer/{$file->getFilename()}"),
                ], 'filament-error-mailer-stubs');
            }
        }
    }

    protected function getAssetPackageName(): ?string
    {
        return 'hugomyb/filament-error-mailer';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            //
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
           //
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [
            'web',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            //
        ];
    }
}
