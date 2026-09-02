<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verify extends Model
{
    use HasFactory;

    protected $table    = 'verify';
    protected $guarded = [];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'verify_id', 'id');
    }
}
