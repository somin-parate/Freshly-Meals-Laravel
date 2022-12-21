<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Meal;
use App\Exports\DeliveryExport;
use App\Exports\ParcelExport;
use Excel;
use DB;
use PDF;

class ParcelUserController extends BaseController
{
    public function getParcelsList()
    {
        $userId = '';
        $userMeals = $userData = $finalData = [];
        $tomorrowDateTime = Carbon::now()->addDays(3);
        $users = DB::table('users_meals')->whereDate('date', '=', $tomorrowDateTime)->get()->toArray();
        // echo '<pre>';print_r($tomorrowDateTime);exit;
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
            $userInfo = $this->getUser($userKey);
            $planData = DB::table('user_meal_plans')->where(['user_id'=>$userKey])
                        ->orderBy('start_date','DESC')->first();
            $getPlanName = DB::table('meal_plan_types')->where(['id'=> $planData->meal_plan_id])->value('title');
            $address = DB::table('user_address')->where(['user_id'=>$userKey,'id'=>$planData->order_address])
                        ->value('current_location');
            if($planData->cutlery == "true"){
                $cutlery = "Yes";
            }else{
                $cutlery = "No";
            }
            // echo '<pre>';print_r($planData);
            $finalData[] = [
                'user_name' => $userInfo->fname.' '.$userInfo->lname,
                'email'     => $userInfo->email,
                'gender'    => $userInfo->gender,
                'plan_name' => $getPlanName,
                'cutlery'   => $cutlery,
                'meal_list' => $this->getMealItems($meal, $userKey),
            ];
        }
        // exit;

        return $this->sendResponse($finalData, 'User Meals list');
    }

    public function getParcelsListExportCsv()
    {
        $dateMonth = date('d-F-Y');
        $filename = $dateMonth.'-CLIENTS MENU.xlsx';
        // echo '<pre>';print_r($filename);exit;
        // return (new DeliveryExport)->download($filename, \Maatwebsite\Excel\Excel::XLSX);
        return Excel::download(new ParcelExport, $filename);

    }

    function getMealItems($mealData, $userId){
        $finalMeals = [];
        $i=0;
        foreach ($mealData as $meal) {
            $foodItems = array();
            $mealInfo = Meal::where('id', $meal['meal'])->with('fooditems')->first();
            foreach ($mealInfo->fooditems as $item) {
            $planName = $this->getMealPlanName($mealInfo->meal_plan_id);
                $foodItems[] = [
                    'item'   => $item->title,
                    'qty'    => $meal['qty'],
                    'status' => $this->checkItemStatus($meal['meal'], $item->id, $userId, $planName, $meal['qty']),
                ];
            }
            $finalMeals[$planName.'-'.$i] = $foodItems;
            $i++;
        }
        return $finalMeals;
    }

    function checkItemStatus($mealId, $itemId, $userId, $shortcode, $qty){
        $condition = [
            'meal_id'        => $mealId,
            'food_item_id'   => $itemId,
            'user_id'        => $userId,
            'plan_shortcode' => $shortcode,
            'quantity'       => $qty,
        ];

        $status = DB::table('kithen_user_meals')->where($condition)->whereDate('preparation_at', '=', Carbon::now())->value('status');
            // echo '<pre>';print_r($status);exit;
        return $status;
    }

    function getUser($userId){
        $user = DB::table('freshly_users')->where(['user_id' => $userId])->first();
        return $user;
    }

    function getMealPlanName($planId){
        $planName = DB::table('meal_plan_types')->where(['id' => $planId])->value('shortcode');
        return $planName;
    }

    public function getDeliveryList(){
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
            $userInfo = $this->getUser($userKey);
            $planData = DB::table('user_meal_plans')->where(['user_id'=>$userKey])
                        ->orderBy('start_date','DESC')->first();
            $address = DB::table('user_address')->where(['user_id'=>$userKey,'id'=>$planData->order_address])
                        ->first();
            // echo '<pre>';print_r($userInfo);
            $finalData[] = [
                'user_name'    => $userInfo->fname.' '.$userInfo->lname,
                'phone'        => $userInfo->mobile_number,
                'address'      => $address->house_no.', '.$address->area.', '.$address->landmark,
                'google_code'  => $address->current_location,
                'emirate'      => $address->select_location,
                'time_slot'    => $address->timeslot_by_emirate,
            ];
        }
        // exit;

        return $this->sendResponse($finalData, 'User Meals list');
    }

    public function getDeliveryListExportCsv(){
        $dateMonth = date('d-F-Y');
        $filename = $dateMonth.'-DELIVERY.xlsx';
        // echo '<pre>';print_r($filename);exit;
        // return (new DeliveryExport)->download($filename, \Maatwebsite\Excel\Excel::XLSX);
        return Excel::download(new DeliveryExport, $filename);

    }
}
