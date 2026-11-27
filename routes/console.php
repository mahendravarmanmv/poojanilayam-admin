<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pooja:generate-recurring-slots')
    ->dailyAt('00:30')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping();
