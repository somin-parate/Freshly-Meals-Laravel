<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $table = "food_items";

    protected $fillable = [
        'title',
    ];

    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function meals()
    {
        return $this->belongsToMany(Meal::class);
    }

    public function kitchenMeals()
    {
        return $this->hasMany(KitchenUser::class);
    }
}
