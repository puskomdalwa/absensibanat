<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama',
        'kode',
        'selisih',
        'nominal',
        'keterangan',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'kategori_id', 'id');
    }
}
