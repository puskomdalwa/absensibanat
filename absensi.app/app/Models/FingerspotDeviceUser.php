<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerspotDeviceUser extends Model
{
    use HasFactory;

    protected $table = 'fingerspot_device_users';

    protected $guarded = [];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'privilege'    => 'integer',
        'finger'       => 'integer',
        'face'         => 'integer',
        'vein'         => 'integer',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'cloud_id', 'cloud_id');
    }

    public function localUser()
    {
        return $this->belongsTo(User::class, 'pin', 'id');
    }

    public function getPrivilegeLabelAttribute()
    {
        switch ($this->privilege) {
            case 2:
                return 'Admin';
            case 3:
                return 'Sub-Admin';
            case 1:
            default:
                return 'User / Santri';
        }
    }
}
