<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenUser extends Model
{
    protected $table = "kithen_user_meals";

    protected $fillable = [
        'meal_id', 'food_item_id', 'user_id', 'plan_shortcode', 'quantity', 'status', 'preparation_at'
    ];

    protected $hidden = [
        'created_at', 
        'updated_at'
    ];

    public function foodItems()
    {
    	return $this->belongsTo(FoodItem::class);
    }
}
