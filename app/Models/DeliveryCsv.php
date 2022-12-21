<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use stdClass;
use DB;

class DeliveryCsv extends Model
{
    use HasFactory;

    public static function getAllParcel(){
        $userId = '';
        $userMeals = $userData = $finalData = [];
        $tomorrowDateTime = Carbon::now()->addDays(3);
        $users = DB::table('users_meals')->whereDate('date', '=', $tomorrowDateTime)->get()->toArray();
        // echo '<pre>';print_r($users);exit;
        foreach ($users as $value) {
            if($userId != $value->user_id){
                $userId = '';
                $meals = [];
            }
            $userId = $value->user_id;
            $meals[] = [
                'meal' => $value->meal_id,
                'qty'  => $value->quantity,
                'planId'=>$value->plan_id
            ];
            $userMeals[$userId] = $meals;
        }


        foreach ($userMeals as $userKey => $meal) {
            $userInfo = self::getUser($userKey);
            $planData = DB::table('user_meal_plans')->where(['user_id'=>$userKey])
                        ->orderBy('start_date','DESC')->first();
            if($planData->cutlery == "true"){
                $cutlery = "Yes";
            }else{
                $cutlery = "No";
            }
            $explodeVariation = str_split($planData->variation_id);

            $noOfMeals = $explodeVariation[2] + $explodeVariation[3];
            $address = DB::table('user_address')->where(['user_id'=>$userKey,'id'=>$planData->order_address])
                        ->value('current_location');
            $finalData[] = [
                'user_name'     => $userInfo->fname.' '.$userInfo->lname,
                'email'         => $userInfo->email,
                'plan_name'     => self::getMealPlanName($planData->meal_plan_id),
                'gender'        => $userInfo->gender,
                'no_of_meals'   => $noOfMeals,
                'cutlery'       => $cutlery,
                'address'       => $address,
                'meal_list'     => self::getMealItems($meal),
                'snack_list'    => self::getSnackItems($meal),
            ];
        }
    //     echo '<pre>';print_r($finalData);
    // exit;
        // exit;
        return $finalData;
    }

    public static function getMealItems($mealData){
        $finalMeals = [];
        foreach ($mealData as $meal) {
            $foodItems = array();
            $mealInfo = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('meals.meal_info', '=', 'Meal')
                    ->where('food_item_meal.meal_id','=',$meal)
                    ->pluck('food_items.title');
            // $mealInfo = DB::table('food_item_meal')->select('food_items.title')
            //             ->join('food_items', 'food_items.id', '=', 'food_item_meal.food_item_id')
            //             ->join('meals', 'meals.id', '=', 'food_item_meal.meal_id')
            //             ->where('meals.meal_info', '=', 'Meal')
            //             ->where('food_item_meal.meal_id', '=', $meal)
            //             ->get()->toArray();
            $mealInfo = json_decode(json_encode($mealInfo), true);
            $finalName = implode(' + ',$mealInfo);
            $mealList[] = $finalName;
        }
        return $mealList;
    }

    public static function getSnackItems($mealData){
        $snackList = [];
        foreach ($mealData as $meal) {
            $mealInfo = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('meals.meal_info', '=', 'Snack')
                    ->where('food_item_meal.meal_id','=',$meal['meal'])
                    ->pluck('food_items.title')->toArray();
            // $mealInfo = DB::table('food_item_meal')->select('food_items.title')
            //             ->join('food_items', 'food_items.id', '=', 'food_item_meal.food_item_id')
            //             ->join('meals', 'meals.id', '=', 'food_item_meal.meal_id')
            //             ->where('meals.meal_info', '=', 'Snack')
            //             ->where('food_item_meal.meal_id', '=', $meal)
            //             ->get()->toArray();
            if($mealInfo){
                $mealInfo = json_decode(json_encode($mealInfo), true);
                $finalName = implode(' + ',$mealInfo);
                $snackList[] = $finalName;
            }

            // foreach ($mealInfo as $value) {
            //     $snackList[] = $value->title;
            // }
            // echo '<pre>';print_r($mealInfo);
        }
        return $snackList;
    }

    public static function getUser($userId){
        $user = DB::table('freshly_users')->where(['user_id' => $userId])->first();
        return $user;
    }

    public static function getMealPlanName($planId){
        $planName = DB::table('meal_plan_types')->where(['id' => $planId])->value('shortcode');
        return $planName;
    }

    public static function getAllDelivery(){
        $userId = '';
        $userMeals = $userData = $finalData = [];
        $tomorrowDateTime = Carbon::now()->addDays(3);
        $users = DB::table('users_meals')->whereDate('date', '=', $tomorrowDateTime)->get()->toArray();
        // echo '<pre>';print_r($users);exit;
        foreach ($users as $value) {
            if($userId != $value->user_id){
                $userId = '';
                $meals = [];
            }
            $userId = $value->user_id;
            $meals[] = [
                'meal' => $value->meal_id,
                'qty'  => $value->quantity,
                'planId'=>$value->plan_id
            ];
            $userMeals[$userId] = $meals;
        }


        foreach ($userMeals as $userKey => $meal) {
            $userInfo = self::getUser($userKey);
            $planData = DB::table('user_meal_plans')->where(['user_id'=>$userKey])
                        ->orderBy('start_date','DESC')->first();
            $address = DB::table('user_address')->where(['user_id'=>$userKey,'id'=>$planData->order_address])
                        ->first();
            $finalData[] = [
                'user_name'     => $userInfo->fname.' '.$userInfo->lname,
                'phone'         => $userInfo->mobile_number,
                'address'      => $address->house_no.', '.$address->area.', '.$address->landmark,
                'google_code'  => $address->current_location,
                'emirate'      => $address->select_location,
                'time_slot'    => $address->timeslot_by_emirate,
            ];
        }
        return $finalData;
    }
}