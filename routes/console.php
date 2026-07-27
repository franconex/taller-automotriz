<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Console\Application as ArtisanApplication;

ArtisanApplication::starting(function ($artisan) {
    $artisan->resolve(\App\Console\Commands\ServeCommand::class);
});
