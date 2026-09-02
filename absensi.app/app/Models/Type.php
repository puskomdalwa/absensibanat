<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'type';
    protected $fillable = [
        'nama',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'type_id', 'id');
    }
}
