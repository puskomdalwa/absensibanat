<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiClient extends Model
{
    use HasFactory;

    protected $table = 'api_clients';

    protected $fillable = [
        'name',
        'api_key',
        'secret_key',
        'is_active',
        'description',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public static function generateCredentials(): array
    {
        return [
            'api_key' => Str::random(32),
            'secret_key' => Str::random(64),
        ];
    }
}
