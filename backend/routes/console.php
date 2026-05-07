<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Jalankan pengecekan izin setiap hari pukul 08:00 pagi
Schedule::command('permits:check-expiry')->dailyAt('08:00');
