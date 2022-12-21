<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use DateTime;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = ['title', 'description', 'image', 'coupon_code','amount','coupon_type','start_date','end_date','customer_email'];

    protected $hidden = ['created_at', 'updated_at'];

    public static function getOffersList()
    {
        $current_date = date('Y-m-d');
        return self::select('*')->whereDate('end_date','>=',$current_date)
                                ->get();
    }

    public static function getTypeById($offerId)
    {
        #type = 1 ->%
        #type = 2 ->currency
        return self::where(['id'=>$offerId])->value('type');
    }

    public static function getAmountById($offerId)
    {
        return self::where(['id'=>$offerId])->value('amount');
    }

    public static function nb_mois($offerId)
    {
        $start_date = self::where(['id'=>$offerId])->value('start_date');
        $end_date = self::where(['id'=>$offerId])->value('end_date');
        $current_date = date('Y-m-d h:i:s');
        $status = "";
        if($current_date <= $end_date)
        {
            //$status = 1 show popup
            $status = "1";
        }else {
            //$status = 0 show dashboard
            $status = "0";
        }

        return $status;
    }

    public static function subscriptionExpired($userId)
    {
        $endDate = DB::table('user_meal_plans')->where(['user_id'=>$userId])->orderBy('id','DESC')->first();
        // echo '<pre>';print_r($endDate);exit;
        $date = date('Y-m-d'). " 00:00:00";
        $myMeals = DB::table('users_meals')->where(['user_id'=>$userId])
                    ->whereDate('date','>=',$date)
                    ->orderBy('date','ASC')->get()->toArray();
        $current_date = date('Y-m-d');
        $status = "";
        if(isset($endDate)){
            if($current_date > $endDate->end_date){
                $status = "1";
            }else if($myMeals) {
                $status = "0";
            }else if($endDate->remaining_meal > 0){
                $status = "0";
            }else{
                $status = "1";
            }
        }else{
            $status = "0";
        }
        return $status;
    }

    public static function getCoupon($offerId)
    {
        return self::where(['id'=>$offerId])->first();
    }

    public static function isOfferUsed($userId,$offerId,$planId,$cartId)
    {
        $where = ([
            'user_id'       =>$userId,
            'offer_id'      =>$offerId,
            'meal_plan_id'  =>$planId,
            'cart_id'       =>$cartId,
            'offer_status'  =>1
        ]);
        $offer = DB::table('user_meal_plans')->where($where)->first();
        $status = "";
        if($offer){
            $status = "1";
        }else {
            $status = "0";
        }
        return $status;
    }

    public static function getIdByCode($code)
    {
        return self::where(['coupon_code'=>$code])->value('id');
    }

    public static function disableOfferButton($userId)
    {
        $disableButton = DB::table('apply_coupon')->where(['user_id'=>$userId])->orderBy('id', 'DESC')->first();
        if($disableButton){
            $offer_id = $disableButton->offer_id;
            return $offer_id;
        }else {
            $data = "";
            return $data;
        }
    }
}
