<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Timeslot;
use App\Models\City;
use DB;

class Address extends Model
{
    protected $table = 'user_address';

    protected $fillable = [
        'user_id', 'current_location', 'house_no', 'area', 'landmark', 'location_tag','select_location', 'timeslot_by_emirate',
         'lat', 'long'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public static function addAdress($data)
    {
         //Insert User address
        $timeSlot = DB::table('time_slots')->where(['id'=>$data['timeslot_by_emirate']])->first();
        $time = $timeSlot->start_time ."-". $timeSlot->end_time;
        $cityId = DB::table('cities')->where(['city'=>$data['select_city']])->value('id');
        $user = new Address();
        $user->user_id                 = $data['user_id'];
        $user->current_location        = $data['current_location'];
        $user->house_no                = $data['house_no'];
        $user->area                    = $data['area'];
        $user->landmark                = $data['landmark'];
        $user->location_tag            = $data['location_tag'];
        $user->select_location         = $data['select_city'];
        $user->timeslot_by_emirate     = $time;
        $user->lat                     = $data['lat'];
        $user->long                    = $data['long'];
        $user->time_slot_id            = $data['timeslot_by_emirate'];
        $user->city_id                 = $cityId;
        $user->default_address         = '1';
        $user->address_status          = '1';
        $user->save();

        //Return User address
        return $user;
    }

    public static function editAddress($data)
    {
         //Insert User address
         $timeSlot = DB::table('time_slots')->where(['id'=>$data['timeslot_by_emirate']])->first();
         $time = $timeSlot->start_time ."-". $timeSlot->end_time;
         $cityId = DB::table('cities')->where(['city'=>$data['select_city']])->value('id');
         $user = Address::findOrFail($data['address_id']);
         $user->user_id                 = $data['user_id'];
         $user->current_location        = $data['current_location'];
         $user->house_no                = $data['house_no'];
         $user->area                    = $data['area'];
         $user->landmark                = $data['landmark'];
         $user->location_tag            = $data['location_tag'];
         $user->select_location         = $data['select_city'];
         $user->timeslot_by_emirate     = $time;
         $user->lat                     = $data['lat'];
         $user->long                    = $data['long'];
         $user->time_slot_id            = $data['timeslot_by_emirate'];
         $user->city_id                 = $cityId;
         $user->save();

         //Return User address
         return $user;
    }

    public static function isAddress($userId)
    {
        $data = self::where(['user_id'=>$userId])->get()->toArray();
        $status = "";
        if(!empty($data)){
            $status = "1";
        }else {
            $status = "0";
        }
        // echo '<pre>';print_r($data);exit;
        return $status;
    }

    public static function getAddressById($addressId)
    {
        $address = self::where(['id'=>$addressId])->first();
        $address = json_decode(json_encode($address), true);
        // $address = $address->house_no. " " .$address->area. " " .$address->landmark;
        return $address;
    }

    public static function getParticularAddress($userId, $addressId)
    {
        return self::where(['user_id'=>$userId,'id'=>$addressId])->get()->toArray();
    }

    public static function getDefaultAddress($userId)
    {
        $address = self::where(['user_id'=>$userId])->first();
        $address = json_decode(json_encode($address), true);
        // $address = $address->house_no. " " .$address->area. " " .$address->landmark;
        return $address;
    }

    public static function mySavedAddresses($userId)
    {
        return self::where(['user_id'=>$userId])
                    ->where('address_status','=',1)->get()->toArray();
    }
}
