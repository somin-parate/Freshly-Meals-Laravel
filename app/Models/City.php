<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "cities";

    public $timestamps = false;

    protected $fillable = [
        'city', 
        'code',
    ];

    public function timeslots()
    {
        return $this->belongsToMany(Timeslot::class);
    }
}