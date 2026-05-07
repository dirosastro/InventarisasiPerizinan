<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PPK extends Model
{
    use HasFactory;
    protected $table = 'ppk';
    protected $fillable = ['nama_ppk', 'satker_id'];

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'satker_id');
    }
}
