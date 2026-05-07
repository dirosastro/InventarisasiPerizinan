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
}
