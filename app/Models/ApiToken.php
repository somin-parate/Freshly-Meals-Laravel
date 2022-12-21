<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $table = 'api_tokens';

    protected $primaryKey = 'token_id';

    protected $fillable = [
        'device_token', 'device_type', 'language', 'user_id'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public static function checkDeviceExist($token)
    {   
        $whereArray = [
            'device_token'=> $token,
        ];
        return self::select('token_id','device_token','device_type', 'language', 'user_id')
                    ->where($whereArray)
                    ->first();
    }

    public static function checkDeviceExistLogin($token, $userId)
    {   
        $whereArray = [
            'device_token'=> $token,
            'user_id'     => $userId,
        ];
        return self::select('token_id','device_token','device_type', 'language', 'user_id')
                    ->where($whereArray)
                    ->first();
    }

    public static function deviceReg($data)
    {
        if(array_key_exists('user_id', $data)){
            $userId = $data['user_id'];
        }else{
            $userId = "";
        }
        $data =  self::create([
            'device_token'=> $data['device_token'],
            'device_type' => $data['device_type'],
            'language'    => $data['language'],
            'user_id'     => $userId,
        ]);
        return $data['token_id'];
    }

    public static function getDeviceData($device_id)
    {
        return self::select('token_id','device_token','device_type', 'language', 'user_id')
                    ->where(['token_id'=>$device_id])
                    ->first();
    }

    public static function updateDeviceData($tokenId, $userId)
    {
        $isUpdated = self::where('token_id', $tokenId)
                         ->update(['user_id' => $userId]);
        return $isUpdated;
    }

    public static function deleteDeviceData($data)
    {
        $whereArray = [
            'device_token'=> $data['device_token'],
            'user_id'     => $data['user_id'],
        ];
        $tokenId = self::select()->where($whereArray)->value('token_id');
        if(empty($tokenId)){
            return;
        }else{
            return self::where('token_id', $tokenId)->delete();
        }
    }

    public static function getDeviceToken($user_id)
    {
        return self::select('device_token')
                    ->where(['user_id'=>$user_id])
                    ->get()->toArray();
    }

    public static function getDeviceType($user_id)
    {
        return self::where(['user_id'=>$user_id])->value('device_type');
    }

    public static function getAllDeviceToken()
    {
        return self::select('device_token')->get()->toArray();
    }
}
