<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Meal;
use App\Models\KitchenUser;
use App\Models\FoodItem;
use stdClass;
use DB;
use PDF;
use App\Exports\UsersExport;
use Excel;

class KitchenUserController extends BaseController
{
    public function planShortcodes(){
        $shortcodes = DB::table('meal_plan_types')->select('id', 'shortcode')->get();
        return $this->sendResponse($shortcodes, 'Shortcodes List');
    }

    public function markCompletedMeal(Request $request)
    {
        $kitchen_meals = KitchenUser::where([
            'food_item_id'   => $request->food_item_id,
            'plan_shortcode' => $request->plan_shortcode,
            'status'         => 'Pending',
        ])
        ->whereDate('preparation_at', '=', Carbon::today())
        ->pluck('id', 'user_id');

        foreach ($kitchen_meals as $userId => $itemId) {
            $item = KitchenUser::findOrFail($itemId);
            $gender = $this->findGender($userId);
            if($gender == $request->gender){
                $item->status = 'Ready';
                $item->save();
            }
        }

        return $this->sendResponse($kitchen_meals, 'Item Done');
    }

    function getPlanData($kitchen_meals){
        $itemData = $this->findOrderCount($kitchen_meals);
        // echo '<pre>';print_r($itemData);
        // exit;
        // if($itemData['total_count']){
        //     $total_count = $itemData['total_count'];
        // }else{
        //     $total_count = 0;
        // }

        $planData = new stdClass();
        $shortcodes = DB::table('meal_plan_types')->pluck('shortcode');
        foreach($shortcodes as $shortcode){
            if (array_key_exists($shortcode, $itemData)){
                foreach ($itemData as $key => $value) {
                    if($key == $shortcode){
                        $male_count    = $value['male']['count'];
                        $male_status   = $value['male']['status'];
                        $female_count  = $value['female']['count'];
                        $female_status = $value['female']['status'];
                        $total_count   = $value['male']['total_count'];
                        break;
                    }
                }
            }
            else{
                $male_count = 0;
                $female_count = 0;
                $total_count = 0;
                $male_status = 'Pending';
                $female_status = 'Pending';
            }
            $male_shortcode              = $shortcode.'_Male';
            $female_shortcode            = $shortcode.'_Female';
            // $planData->$male_shortcode   = $male_count;
            $total_count = $male_count + $female_count;
            $planData->$male_shortcode = [
                'item_count'  => $male_count,
                'item_status' => $male_status,
                'total_count' => $total_count,
            ];
            $planData->$female_shortcode = [
                'item_count'  => $female_count,
                'item_status' => $female_status,
                'total_count' => 0,
            ];
        // $planData->total_count = $total_count;
        }
        return $planData;
    }

    function findOrderCount($orders, $finalOrders = [], $orderKey = ''){
        $plan_shortcode = array_column($orders, 'plan_shortcode');
        array_multisort($plan_shortcode, SORT_ASC, $orders);

        foreach($orders as $order){
            if($orderKey != $order['plan_shortcode']){
                $male_count = 0; $female_count = 0;
                $mstatus = $fstatus = 'Pending';
            }
            $orderKey = $order['plan_shortcode'];
            // echo '<pre>';print_r($order['quantity']);exit;
            $gender = $this->findGender($order['user_id']);
            if($gender == 'Male'){
                $male_count += $order['quantity'];
                $mstatus = $order['status'];
            }else{
                $female_count += $order['quantity'];
                $fstatus = $order['status'];
            }
            $total_count = $male_count + $female_count;

            $finalOrders[$order['plan_shortcode']] = [
                'male'   => [
                    'status'     => $mstatus,
                    'count'      => $male_count,
                    'total_count'      => $total_count,
                ],
                'female'   => [
                    'status'     => $fstatus,
                    'count'      => $female_count,
                    'total_count'      => 0,
                ]
            ];
        }
        return $finalOrders;
    }

    function findGender($userId){
        return DB::table('freshly_users')->where(['user_id'=> $userId])->value('gender');
    }

    public function getPreparationList()
    {
        $finalData = [];

        $date = Carbon::today();
        $items = FoodItem::query()
                    ->with(array('kitchenMeals' => function($query) {
                        $query->whereDate('preparation_at', '=', date('Y-m-d', strtotime("+4 days")));
                    }))->get()->toArray();
        // echo '<pre>';print_r($items);exit;

        foreach($items as $item){
            if(count($item['kitchen_meals']) > 0){
                $count = DB::table('kithen_user_meals')->where(['food_item_id'=>$item['id']])
                            ->whereDate('preparation_at', '=', date('Y-m-d', strtotime("+4 days")))->sum('quantity');
                array_push($finalData, [
                    'id'        => $item['id'],
                    'item_name' => $item['title'],
                    'plan_data' => $this->getPlanData($item['kitchen_meals']),
                    'total_count'=>$count
                ]);
            }
        }
        return $this->sendResponse($finalData, 'Preparation Meals list');
    }

    public function getPreparationListExportCsv()
    {
        $dateMonth = date('d-F-Y', strtotime('+3 days'));
        $filename = $dateMonth.'-BREAKDOWN.xlsx';
        // return Excel::download(new UsersExport, $filename, \Maatwebsite\Excel\Excel::XLSX);
        return (new UsersExport)->download($filename, \Maatwebsite\Excel\Excel::XLSX);

    }
}