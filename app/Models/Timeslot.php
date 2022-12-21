<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    protected $table = "time_slots";

    protected $fillable = [
        'start_time', 
        'end_time',
    ];

    public function cities()
    {
        return $this->belongsToMany(City::class);
    }

    public static function getTime($timeslotId)
    {
        return self::select('start_time','end_time')->where(['id'=>$timeslotId])->get()->toArray();
    }
}
