<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    use HasFactory;

    protected $table = 'perizinan';

    protected $fillable = [
        'nomor_izin',
        'jenis_izin',
        'sub_jenis',
        'icon',
        'pemohon',
        'no_hp',
        'satker_id',
        'berkas',
        'pnbp',
        'tanggal_terbit',
        'tanggal_akhir',
        'status'
    ];

    public function lokasi()
    {
        return $this->hasMany(PerizinanLokasi::class, 'perizinan_id');
    }

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'perizinan_id');
    }
}
