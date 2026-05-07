<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WhatsAppService;
use App\Models\Perizinan;

$whatsapp = new WhatsAppService();
$nomor = '087758525792';

// Ambil satu data contoh dari database untuk tes
$permit = Perizinan::with('lokasi')->first();

if ($permit) {
    $ruasJalan = $permit->lokasi->pluck('nama_ruas_jalan')->unique()->implode(', ') ?: '-';
    $pesan = "🔔 *TES SISTEM SIPERJALAN (DATA ASLI)*\n\n" .
             "Halo *{$permit->pemohon}*,\n\n" .
             "Ini adalah pesan tes menggunakan data dari database:\n" .
             "📌 *Pemohon:* {$permit->pemohon}\n" .
             "📌 *No. HP:* {$permit->no_hp}\n" .
             "📌 *Ruas Jalan:* {$ruasJalan}\n" .
             "📌 *Nomor Izin:* {$permit->nomor_izin}\n\n" .
             "Jika Anda menerima pesan ini, berarti data sudah berhasil ditarik! ✅";
} else {
    $pesan = "🔔 *TES SISTEM SIPERJALAN*\n\n" .
             "Halo *[NAMA PEMOHON]*,\n\n" .
             "Ini adalah pesan tes (Data Belum Ada di DB):\n" .
             "📌 *Pemohon:* [NAMA PEMOHON]\n" .
             "📌 *No. HP:* [NOMOR HP]\n" .
             "📌 *Ruas Jalan:* [NAMA RUAS JALAN]\n\n" .
             "Jika Anda menerima pesan ini, berarti format pesan sudah sesuai! ✅";
}

echo "Mencoba mengirim pesan ke $nomor...\n";

$result = $whatsapp->sendMessage($nomor, $pesan);

if ($result) {
    echo "BERHASIL! Pesan telah dikirim ke bot API.\n";
    print_r($result);
} else {
    echo "GAGAL! Pastikan bot sudah jalan (node bot.js) dan sudah di-scan QR-nya.\n";
}
