<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DasarHukum extends Model
{
    protected $table = 'dasar_hukum';

    protected $fillable = [
        'kategori',
        'nomor',
        'judul',
        'ringkasan',
        'tahun',
        'link_file_id',
        'link_file_url',
        'sop_file_id',
        'sop_file_url',
        'urutan',
    ];
}
