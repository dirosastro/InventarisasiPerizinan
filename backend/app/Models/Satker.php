<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satker extends Model
{
    use HasFactory;
    protected $table = 'satker';
    protected $fillable = ['nama_satker', 'kode_satker'];

    public function ppk()
    {
        return $this->hasMany(PPK::class, 'satker_id');
    }
}
