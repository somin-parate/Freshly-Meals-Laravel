<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageUpload extends Model
{
    protected $table = 'upload_images';

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'user_id', 'image', 'type'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
