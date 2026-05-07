<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Perizinan;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiringPermits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permits:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek izin yang akan kadaluarsa dan kirim notifikasi WhatsApp';

    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        parent::__construct();
        $this->whatsapp = $whatsapp;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan masa berlaku izin...');

        // 1. Update status ke 'kadaluarsa' jika sudah lewat tanggal_akhir
        $expiredCount = Perizinan::where('tanggal_akhir', '<', Carbon::now()->toDateString())
            ->where('status', '!=', 'kadaluarsa')
            ->update(['status' => 'kadaluarsa']);
        
        if ($expiredCount > 0) {
            $this->warn("{$expiredCount} izin telah diupdate ke status 'kadaluarsa'.");
        }

        // 2. Update status ke 'hampir_habis' jika dalam rentang 30 hari
        $almostExpiredCount = Perizinan::whereBetween('tanggal_akhir', [
                Carbon::now()->toDateString(), 
                Carbon::now()->addDays(30)->toDateString()
            ])
            ->where('status', 'aktif')
            ->update(['status' => 'hampir_habis']);

        if ($almostExpiredCount > 0) {
            $this->info("{$almostExpiredCount} izin telah diupdate ke status 'hampir_habis'.");
        }

        // 3. Kirim Notifikasi WhatsApp Early Warning (H-90 & H-60) — pengingat awal perpanjangan
        $earlyWarningIntervals = [90, 60];

        foreach ($earlyWarningIntervals as $days) {
            $targetDate = Carbon::now()->addDays($days)->toDateString();

            $permits = Perizinan::with('lokasi')
                ->where('tanggal_akhir', $targetDate)
                ->where('status', 'aktif')
                ->get();

            foreach ($permits as $permit) {
                if ($permit->no_hp) {
                    $jenis = ($permit->sub_jenis && $permit->sub_jenis !== '-') ? $permit->sub_jenis : $permit->jenis_izin;
                    $tglFormat = Carbon::parse($permit->tanggal_akhir)->translatedFormat('d F Y');
                    $ruasJalan = $permit->lokasi->pluck('nama_ruas_jalan')->unique()->implode(', ');

                    $message = "📋 *PEMBERITAHUAN AWAL — MASA BERLAKU IZIN*\n\n" .
                               "Yth. *{$permit->pemohon}*,\n\n" .
                               "Kami dari *Balai Pelaksanaan Jalan Nasional (BPJN) NTB* mengingatkan bahwa " .
                               "izin pemanfaatan ruang milik jalan Anda akan berakhir dalam *{$days} hari* " .
                               "ke depan (pada tanggal *{$tglFormat}*).\n\n" .
                               "📌 *Nomor Izin:* {$permit->nomor_izin}\n" .
                               "📌 *Jenis Izin:* {$jenis}\n" .
                               "📌 *Ruas Jalan:* {$ruasJalan}\n" .
                               "📌 *Masa Berlaku Berakhir:* {$tglFormat}\n\n" .
                               "Jika Anda masih memerlukan izin tersebut, kami menyarankan untuk " .
                               "*segera mempersiapkan dokumen perpanjangan* agar proses dapat berjalan " .
                               "tepat waktu sebelum izin berakhir.\n\n" .
                               "📞 Untuk informasi lebih lanjut, silakan menghubungi kantor BPJN NTB.\n\n" .
                               "Terima kasih atas perhatian dan kerja samanya.\n\n" .
                               "_Pesan ini dikirim otomatis oleh Sistem Informasi Perizinan Siperjalan BPJN NTB._";

                    $this->info("Mengirim early warning H-{$days} ke {$permit->no_hp} (Izin: {$permit->nomor_izin})");

                    $result = $this->whatsapp->sendMessage($permit->no_hp, $message);

                    if ($result) {
                        $this->info("Early warning H-{$days} berhasil dikirim.");
                    } else {
                        $this->error("Gagal mengirim early warning H-{$days} ke {$permit->no_hp}.");
                    }
                }
            }
        }

        // 4. Kirim Notifikasi WhatsApp Urgent untuk H-30, H-14, H-7, H-1
        $urgentIntervals = [30, 14, 7, 1];
        
        foreach ($urgentIntervals as $days) {
            $targetDate = Carbon::now()->addDays($days)->toDateString();
            
            $permits = Perizinan::with('lokasi')
                ->where('tanggal_akhir', $targetDate)
                ->where('status', '!=', 'kadaluarsa')
                ->get();

            foreach ($permits as $permit) {
                if ($permit->no_hp) {
                    $jenis = ($permit->sub_jenis && $permit->sub_jenis !== '-') ? $permit->sub_jenis : $permit->jenis_izin;
                    $tglFormat = Carbon::parse($permit->tanggal_akhir)->translatedFormat('d F Y');
                    $ruasJalan = $permit->lokasi->pluck('nama_ruas_jalan')->unique()->implode(', ');

                    // Sesuaikan ikon urgensi berdasarkan sisa hari
                    $urgencyIcon = match(true) {
                        $days <= 1  => '🚨',
                        $days <= 7  => '⚠️',
                        $days <= 14 => '🔔',
                        default     => '📢',
                    };

                    $message = "{$urgencyIcon} *PERINGATAN MASA BERLAKU IZIN — {$days} HARI LAGI*\n\n" .
                               "Yth. *{$permit->pemohon}*,\n\n" .
                               "Kami menginformasikan bahwa izin pemanfaatan bagian-bagian jalan Anda:\n" .
                               "📌 *Nomor Izin:* {$permit->nomor_izin}\n" .
                               "📌 *Jenis:* {$jenis}\n" .
                               "📌 *Ruas Jalan:* {$ruasJalan}\n" .
                               "📌 *Tgl Berakhir:* {$tglFormat}\n\n" .
                               "Akan berakhir dalam *{$days} hari lagi*.\n\n" .
                               ($days <= 7
                                   ? "⚡ *Mohon segera menghubungi kantor BPJN NTB* untuk proses perpanjangan sebelum izin berakhir.\n\n"
                                   : "Mohon segera melakukan permohonan perpanjangan jika masih diperlukan.\n\n"
                               ) .
                               "Terima kasih.\n\n" .
                               "_Pesan ini dikirim otomatis oleh Sistem Siperjalan BPJN NTB._";
                    
                    $this->info("Mengirim notifikasi urgent H-{$days} ke {$permit->no_hp} (Izin: {$permit->nomor_izin})");
                    
                    $result = $this->whatsapp->sendMessage($permit->no_hp, $message);
                    
                    if ($result) {
                        $this->info("Notifikasi H-{$days} berhasil dikirim.");
                    } else {
                        $this->error("Gagal mengirim notifikasi ke {$permit->no_hp}.");
                    }
                }
            }
        }

        $this->info('Pengecekan selesai.');
    }
}
