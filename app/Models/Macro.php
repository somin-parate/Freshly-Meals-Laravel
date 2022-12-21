<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Macro extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_id', 'gender', 'label', 'value', 'unit'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
