<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;
    protected $table    = 'absensi';
    protected $fillable = [
        'users_id',
        'tgl_absen',
        'pagi',
        'sore',
        'latitude',
        'longitude',
        'kategori_id',
        'keterangan',
    ];

    public function getPagiAttribute($value)
    {
        if (!$value) {
            return "-";
        }

        return Carbon::parse($value)->format('H:i');
    }

    public function getSoreAttribute($value)
    {
        if (!$value) {
            return "-";
        }

        return Carbon::parse($value)->format('H:i');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function keterangans()
    {
        return $this->hasMany(Keterangan::class, 'absensi_id', 'id');
    }

    public function verify()
    {
        return $this->belongsTo(Verify::class, 'verify_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }
}
