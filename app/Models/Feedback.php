<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class Feedback extends Model
{
    public static function storeDatas($data)
    {
        $values = [
            'user_id'       => $data['user_id'],
            'type'          => $data['type'],
            'subject'       => $data['subject'],
            'message'       => $data['message'],
            'created_at'    => date('Y-m-d'),
            'updated_at'    => date('Y-m-d'),
            'status'        => "0",
        ];
        
        return DB::table('feedbacks')->where(['user_id'=>$data['user_id']])->insert($values);
    }
}
