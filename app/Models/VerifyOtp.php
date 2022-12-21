<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyOtp extends Model
{
    use HasFactory;

    protected $table = 'verify_otp';

    protected $fillable = [
        'user_id','email','otp','status',
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public static function insertOtp($userId, $email)
    {
        return self::create([
            'user_id' => $userId,
            'email' => $email,
            'otp' => mt_rand(1000,9999),
            'status' => '0',
        ]);
    }
}
