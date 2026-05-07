<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Di Laravel 11, penjadwalan (scheduling) tidak lagi berada di file
| app/Console/Kernel.php. Anda sekarang mendaftarkan jadwal command
| langsung di dalam file routes/console.php ini.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mendaftarkan penjadwalan skrip notifikasi WhatsApp
// Command ini akan berjalan secara otomatis setiap hari pada pukul 08:00 pagi
Schedule::command('notify:whatsapp-reminder')->dailyAt('08:00');
