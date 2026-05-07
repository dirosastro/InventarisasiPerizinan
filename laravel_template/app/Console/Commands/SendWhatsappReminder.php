<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Izin;
use Carbon\Carbon;

class SendWhatsappReminder extends Command
{
    /**
     * Nama command untuk dijalankan via artisan (contoh: php artisan notify:whatsapp-reminder)
     *
     * @var string
     */
    protected $signature = 'notify:whatsapp-reminder';

    /**
     * Deskripsi dari command ini.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WhatsApp otomatis untuk perizinan yang akan jatuh tempo dalam 90 hari';

    /**
     * Eksekusi logika utama.
     */
    public function handle()
    {
        $this->info('Memeriksa izin yang akan jatuh tempo 90 hari lagi...');

        // 1. Cari data izin yang masa berlakunya tepat 90 hari dari hari ini
        $izinAkanJatuhTempo = Izin::whereDate('masa_berlaku_akhir', Carbon::today()->addDays(90))
            ->whereNotNull('no_hp_pemohon') // Pastikan ada nomor HP
            ->get();

        if ($izinAkanJatuhTempo->isEmpty()) {
            $this->info('Tidak ada izin yang jatuh tempo 90 hari lagi untuk hari ini.');
            return;
        }

        $berhasil = 0;
        $gagal = 0;

        // 2. Looping setiap data dan kirim pesan
        foreach ($izinAkanJatuhTempo as $izin) {
            $pesan = "Halo {$izin->pemohon}, kami dari BPJN NTB ingin mengingatkan bahwa izin {$izin->jenis_izin} Anda akan segera jatuh tempo dalam 90 hari (pada tanggal " . $izin->masa_berlaku_akhir->format('d M Y') . "). Mohon segera melakukan proses perpanjangan.";

            // 3. Mengirim Request API ke layanan WhatsApp Gateway (Contoh menggunakan Fonnte)
            // Ganti endpoint dan token sesuai dengan penyedia layanan yang Anda pilih nantinya
            try {
                $response = Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN', 'YOUR_API_TOKEN'), // Ambil dari file .env
                ])->post('https://api.fonnte.com/send', [
                    'target' => $izin->no_hp_pemohon,
                    'message' => $pesan,
                    'delay' => '2', // Delay 2 detik agar tidak di-banned karena spam
                ]);

                if ($response->successful() && json_decode($response->body())->status == true) {
                    $this->info("Pesan berhasil dikirim ke {$izin->pemohon} ({$izin->no_hp_pemohon})");
                    $berhasil++;
                } else {
                    $this->error("Gagal mengirim ke {$izin->pemohon}. Response: " . $response->body());
                    $gagal++;
                }
            } catch (\Exception $e) {
                Log::error("Error saat mengirim WA ke {$izin->pemohon}: " . $e->getMessage());
                $this->error("Terjadi kesalahan sistem saat mengirim ke {$izin->pemohon}.");
                $gagal++;
            }
        }

        $this->info("Proses selesai. Berhasil: {$berhasil}, Gagal: {$gagal}");
    }
}
