<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FoodItem;
use Carbon\Carbon;
use stdClass;
use DB;

class KitchenUserCsv extends Model
{
    use HasFactory;

    public static function planShortcodes(){
        $shortcodes = DB::table('meal_plan_types')->select('id', 'shortcode')->get();
        return $this->sendResponse($shortcodes, 'Shortcodes List');
    }

    public static function getAllUsers(){
        $finalData = [];

        $items = FoodItem::query()
        ->with(array('kitchenMeals' => function($query) {
            $query->whereDate('preparation_at', '=', date('Y-m-d', strtotime("+2 days")));
        }))->get()->toArray();

        foreach($items as $item){
            if(count($item['kitchen_meals']) > 0){
                $count = DB::table('kithen_user_meals')->where(['food_item_id'=>$item['id']])
                            ->whereDate('preparation_at', '=', date('Y-m-d', strtotime("+2 days")))->sum('quantity');
                array_push($finalData, [
                    'item_name' => $item['title'],
                    'plan_data' => self::getPlanData($item['kitchen_meals']),
                    'total_count'=>$count
                ]);
            }
        }
        return $finalData;
    }

    public static function getPlanData($kitchen_meals){
        $itemData = self::findOrderCount($kitchen_meals);

        $planData = new stdClass();
        $shortcodes = DB::table('meal_plan_types')->pluck('shortcode');
        foreach($shortcodes as $shortcode){
            if (array_key_exists($shortcode, $itemData)){
                foreach ($itemData as $key => $value) {
                    if($key == $shortcode){
                        $male_count    = $value['male']['count'];
                        $female_count  = $value['female']['count'];
                        break;
                    }
                }
            }
            else{
                $male_count = 0;
                $female_count = 0;
                $total_count = 0;
            }
            if($shortcode == "GF/DF"){
                $fShortcode = "gf_df";
            }else{
                $fShortcode = $shortcode;
            }
            $male_shortcode              = $fShortcode.'_Male';
            $female_shortcode            = $fShortcode.'_Female';
            $planData->$male_shortcode =  $male_count;
            $planData->$female_shortcode = $female_count;
        }
        return $planData;
    }

    public static function findOrderCount($orders, $finalOrders = [], $orderKey = ''){
        $plan_shortcode = array_column($orders, 'plan_shortcode');
        array_multisort($plan_shortcode, SORT_ASC, $orders);

        foreach($orders as $order){
            if($orderKey != $order['plan_shortcode']){
                $male_count = 0; $female_count = 0;
                $mstatus = $fstatus = 'Pending';
            }
            $orderKey = $order['plan_shortcode'];
            // echo '<pre>';print_r($order['quantity']);exit;
            $gender = self::findGender($order['user_id']);
            if($gender == 'Male'){
                $male_count += $order['quantity'];
                $mstatus = $order['status'];
            }else{
                $female_count += $order['quantity'];
                $fstatus = $order['status'];
            }
            $finalOrders[$order['plan_shortcode']] = [
                'male'   => [
                    'count'      => $male_count,
                ],
                'female'   => [
                    'count'      => $female_count,
                ]
            ];
        }
        return $finalOrders;
    }

    public static function findGender($userId){
        return DB::table('freshly_users')->where(['user_id'=> $userId])->value('gender');
    }
}