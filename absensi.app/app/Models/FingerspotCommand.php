<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerspotCommand extends Model
{
    use HasFactory;

    protected $table = 'fingerspot_commands';

    protected $guarded = [];

    protected $casts = [
        'payload_request'  => 'array',
        'payload_response' => 'array',
        'callback_payload' => 'array',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'cloud_id', 'cloud_id');
    }
}
