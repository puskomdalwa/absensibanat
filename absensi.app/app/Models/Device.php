<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table    = 'device';
    protected $guarded = [];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'device_id', 'id');
    }
}
