<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery';

    protected $fillable = [
        'filename',
        'original_name',
        'caption',
    ];

    /**
     * Get full URL path for the image
     */
    public function getImageUrlAttribute()
    {
        return asset('uploads/gallery/' . $this->filename);
    }
}
