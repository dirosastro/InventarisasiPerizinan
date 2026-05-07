<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;
    protected $table = 'dokumen';
    protected $fillable = ['perizinan_id', 'nama_file', 'file_path', 'tipe_dokumen', 'ukuran_file'];

    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class, 'perizinan_id');
    }
}
