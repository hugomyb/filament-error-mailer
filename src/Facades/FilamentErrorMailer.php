<?php

namespace Hugomyb\FilamentErrorMailer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hugomyb\FilamentErrorMailer\FilamentErrorMailer
 */
class FilamentErrorMailer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hugomyb\FilamentErrorMailer\FilamentErrorMailer::class;
    }
}
