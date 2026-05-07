<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    // Mengarahkan model ini ke tabel 'izin' di database
    protected $table = 'izin';

    // Kolom-kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'id_ruas_jalan',
        'no_izin',
        'pemohon',
        'jenis_izin',
        'pnbp',
        'masa_berlaku_awal',
        'masa_berlaku_akhir',
        'status',
        'no_hp_pemohon', // <-- Pastikan kolom ini sudah ditambahkan di database
    ];

    // Mengkonversi tipe data datetime otomatis
    protected $casts = [
        'masa_berlaku_awal' => 'date',
        'masa_berlaku_akhir' => 'date',
    ];
}
