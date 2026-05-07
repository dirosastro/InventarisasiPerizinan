<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerizinanLokasi extends Model
{
    protected $table = 'perizinan_lokasi';

    protected $fillable = [
        'perizinan_id',
        'satker_id',
        'ppk_id',
        'nama_ruas_jalan',
        'sta_awal',
        'sta_akhir',
        'keterangan',
    ];

    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class);
    }

    public function satker()
    {
        return $this->belongsTo(\App\Models\Satker::class, 'satker_id');
    }

    public function ppk()
    {
        return $this->belongsTo(\App\Models\Ppk::class, 'ppk_id');
    }
}
