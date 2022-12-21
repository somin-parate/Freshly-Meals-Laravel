<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergens extends Model
{
    protected $table = "allergens";

    protected $fillable = [
        'icon', 
        'title',
    ];

    public function meals()
    {
        return $this->belongsToMany(Meal::class);
    }
}
