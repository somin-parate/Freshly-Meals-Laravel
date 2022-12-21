<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Offer;
use DB;

class UserMealPlans extends Model
{
    protected $table = 'user_meal_plans';

    protected $fillable = [
        'user_id', 'meal_plan_id', 'variation_id', 'cutlery', 'book_nutritionist','start_date'
    ];
    public static function selectMealPlan($data)
    {
        //Insert User Plan
        $today = date("Ymd");
        $rand   = sprintf("%04d", rand(0,9999));
        $unique = $today . $rand;
        $user = new UserMealPlans();
        $user->user_id             = $data['user_id'];
        $user->meal_plan_id        = $data['meal_plan_id'];
        $user->variation_id        = $data['variation_id'];
        $user->book_nutritionist   = $data['book_nutritionist'];
        $user->cutlery             = $data['add_cutlery'];
        $user->start_date          = $data['start_date'];
        $user->status              = "1";
        $user->order_number        = $unique;
        $user->save();

         //Return User Plan
         return $user;
    }

    public static function getStatus($userId)
    {
        return self::where(['user_id'=>$userId])->value('status');
    }

    public static function isAnyPlanUpcoming($userId){
        $upcoming = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                        ->whereDate('start_date','>',date('Y-m-d'))
                        ->first();
        if($upcoming){
            //status is 1 because user has an upcoming plan coming
            $status = "1";
        }else{
            //status is 0 because user has no upcoming plan
            $status = "0";
        }
        return $status;
    }

    public static function getOrderSummary($userId,$cartId,$getGender)
    {
        $data =  DB::table('cart_summary')->select('cart_summary.*','meal_plan_types.title','meal_plan_types.image','meal_plan_types.short_description',
                            'meal_plan_types.long_description','freshly_users.fname','freshly_users.lname','freshly_users.email',
                            'freshly_users.mobile_number','freshly_users.country_code')
                    ->join('meal_plan_types', 'meal_plan_types.id', 'cart_summary.meal_plan_id')
                    ->join('freshly_users', 'freshly_users.user_id', 'cart_summary.user_id')
                    // ->where('plan_variations.plan_id','=', 'cart_summary.meal_plan_id')
                    ->where('cart_summary.user_id', $userId)
                    ->where('cart_summary.id', $cartId)
                    ->orderBy('cart_summary.id', 'DESC')->first();
        $joinPlanVariations = DB::table('plan_variations')->where(['variation_id'=>$data->variation_id,'gender'=>$getGender,'plan_id'=>$data->meal_plan_id])->first();
        // echo '<pre>';print_r($joinPlanVariations);exit;
        $data->weeks = $joinPlanVariations->weeks;
        $data->days = $joinPlanVariations->days;
        $data->meals = $joinPlanVariations->meals;
        $data->snacks = $joinPlanVariations->snacks;
        $data->price = $joinPlanVariations->price;
        $data = json_decode(json_encode($data), true);
        return $data;
    }

    public static function getOfferData($userId,$cartId){
        $appliedCouponData = DB::table('apply_coupon')->where(['user_id'=>$userId,'cart_id'=>$cartId])->orderBy('id', 'DESC')->first();
        if($appliedCouponData){
            $coupon_type = Offer::where(['id'=>$appliedCouponData->offer_id])->value('coupon_type');
            $appliedCouponData->coupon_type = $coupon_type;
        }else{
            $appliedCouponData = "0";
        }

        return $appliedCouponData;
    }

    public static function saveMealsToCart($data)
    {
        $today = date("Ymd");
        $rand   = sprintf("%04d", rand(0,9999));
        $unique = $today . $rand;
        $sDate = date("Y-m-d", strtotime($data['start_date']));
        $user = [
            'user_id'             => $data['user_id'],
            'meal_plan_id'        => $data['meal_plan_id'],
            'variation_id'        => $data['variation_id'],
            'book_nutritionist'   => $data['book_nutritionist'],
            'cutlery'             => $data['add_cutlery'],
            'start_date'          => $sDate,
            'order_number'        => $unique,
            'created_at'          => date("Ymd"),
        ];
        return DB::table('cart_summary')->insertGetId($user);
         //Return User Plan
    }

    public static function payOrder($data)
    {
        $getOrderData = DB::table('cart_summary')->where(['user_id'=>$data['user_id']])->orderBy('id', 'DESC')->first();
        if($data['payment_mode'] == "online"){
            $status = 1;
        }else{
            $status =0;
        }
        $week = str_split($getOrderData->variation_id);
        $week_number = $week[0];

        $values = [
            'user_id'               => $data['user_id'],
            'meal_plan_id'          => $getOrderData->meal_plan_id,
            'variation_id'          => $getOrderData->variation_id,
            'book_nutritionist'     => $getOrderData->book_nutritionist,
            'cutlery'               => $getOrderData->cutlery,
            'start_date'            => $getOrderData->start_date,
            'status'                => 1,
            'order_number'          => $getOrderData->order_number,
            'order_address'         => $getOrderData->order_address,
            'offer_id'              => $getOrderData->offer_id,
            'coupon_code'           => $getOrderData->coupon_code,
            'discounted_amount'     => $getOrderData->discounted_amount,
            'offer_id'              => $getOrderData->offer_id,
            'cart_id'               => $data['cart_id'],
            'grand_total'           => $getOrderData->grand_total,
            'offer_status'          => 1,
            'status'                => $status,
            'payment_mode'          => $data['payment_mode'],
            'order_address'         => $getOrderData->order_address,
            'end_date'              => date('Y-m-d', strtotime('+'.$week_number.' week', strtotime($getOrderData->start_date))),
            'created_at'            => date("Ymd"),
            'updated_at'            => date("Ymd"),
        ];
        return DB::table('user_meal_plans')->where(['user_id'=>$data['user_id'],'cart_id'=>$data['cart_id']])->insert($values);
    }

    public static function isMealSelected($userId,$date,$mealId)
    {
        $is_meal_selected = DB::table('users_meals')->where(['user_id'=>$userId, 'date'=>$date, 'meal_id'=>$mealId])->first();
        // echo '<pre>';print_r($date);exit;
        $status = "";
        if($is_meal_selected){
            $status = "1";
        }else {
            $status = "0";
        }
        return $status;
    }

    public static function isPlanPaused($userId,$date)
    {
        $planData = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
        $status = "0";
        if($planData){
            if($planData->pause_end_date && $planData->pause_start_date){
                $pauseEndDate = date('Y-m-d', strtotime($planData->pause_end_date));
                $pauseStartDate = date('Y-m-d', strtotime($planData->pause_start_date));
                $status = "";
                if($date >= $pauseStartDate && $date <= $pauseEndDate){
                    //is between pause dates
                    $status = "1";
                }else {
                    //is not between pause dates
                    $status = "0";
                }
            }else{
                $status = "0";
            }
        }


        return $status;
    }

    public static function isPlanPausedCurrentDate($userId)
    {
        $planData = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
        $pauseEndDate = date('Y-m-d', strtotime($planData->pause_end_date));
        $pauseStartDate = date('Y-m-d', strtotime($planData->pause_start_date));
        $status = "";
        $current_date = date('Y-m-d');
        if($current_date >= $pauseStartDate && $current_date <= $pauseEndDate){
            //is between pause dates
            $status = "1";
        }else {
            //is not between pause dates
            $status = "0";
        }
        return $status;
    }

    public static function showPlayPause($userId)
    {
        $planData = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
        $pauseEndDate = date('Y-m-d', strtotime($planData->pause_end_date));
        $pauseStartDate = date('Y-m-d', strtotime($planData->pause_start_date));
        $status = "";
        $date = date('Y-m-d');
        if($pauseEndDate >= $date){
            //show play button
            $status = "1";
        }else {
            //show pause button
            $status = "0";
        }
        return $status;
    }

    public static function isResubscribe($userId)
    {
        $planData = DB::table('user_meal_plans')->where('user_meal_plans.user_id', '=' , $userId)
                    ->whereDate('user_meal_plans.start_date', '<=', date("Y-m-d"))
                    ->whereDate('user_meal_plans.end_date', '>', date('Y-m-d'))
                    ->orderBy('user_meal_plans.start_date', 'DESC')
                    ->first();
        $current_date = date('Y-m-d');
        $status = "";
        if($planData){
            $date = date( 'Y-m-d', strtotime( $planData->end_date . ' -3 day' ) );
            if($current_date == $date){
                //show resubscribe button
                $status = "1";
            }else {
                $status = "0";
            }
        }

        return $status;
    }
}