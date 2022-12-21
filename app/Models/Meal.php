<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FreshlyAuth;
use App\Models\UserMealPlans;
use Carbon\Carbon;
use DB;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'meal_type', 'meal_plan_id', 'meal_begin_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function macros()
    {
        return $this->hasMany(Macro::class);
    }

    public function allergens()
    {
        return $this->belongsToMany(Allergens::class);
    }

    public function fooditems()
    {
        return $this->belongsToMany(FoodItem::class);
    }

    public static function getMacrosAllergens($id)
    {
        return self::select('meals.*','macros.gender','macros.label','macros.value','macros.unit','allergens_meal.allergens_id')
        ->join('macros', 'macros.meal_id', 'meals.id')
        ->join('allergens_meal', 'allergens_meal.meal_id', 'meals.id')
        ->where('meals.id', $id)
        ->get()->toArray();
    }

    public static function getMealPlans()
    {
        return DB::table('meal_plan_types')->get();
    }

    public static function getMealPlanById($mealPlanId)
    {
        return DB::table('meal_plan_types')->where(['id'=>$mealPlanId])->get();
    }

    public static function getMealCategoryName($mealPlanId)
    {
        return DB::table('meal_plan_types')->where(['id'=>$mealPlanId])->value('title');
    }

    public static function getVariationData($mealPlanId,$gender)
    {
        return DB::table('plan_variations')->select('variation_id','price')->where(['plan_id'=>$mealPlanId, 'gender'=>$gender])->get();
    }

    public static function saveUserMeals($mealData)
    {
        $collectMeal = [];

        foreach($mealData['meal_data'] as $meal => $quantity){
            array_push($collectMeal, [
                'user_id'    => $mealData['user_id'],
                'meal_id'    => $meal,
                'date'       => date("Y-m-d",strtotime($mealData['date'])),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'quantity'   => $quantity,
                'address_id' => $mealData['address_id'],
            ]);
        }

        $savedMeal = DB::table('users_meals')->insert($collectMeal);

        return $savedMeal;
    }

    public static function editUserMeals($mealData)
    {
        // echo '<pre>';print_r($mealData);exit;
        $mealData['date'] = date("Y-m-d",strtotime($mealData['date']));
        DB::table('users_meals')->where(['user_id'=>$mealData['user_id']])
                                ->whereDate('date', '=', $mealData['date'])->delete();
        $collectMeal = [];

        foreach($mealData['meal_data'] as $meal => $quantity){
            array_push($collectMeal, [
                'user_id'    => $mealData['user_id'],
                'meal_id'    => $meal,
                'date'       => $mealData['date'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'quantity'   => $quantity,
                'address_id' => $mealData['address_id'],
            ]);
        }

        $editMeal = DB::table('users_meals')->insert($collectMeal);

        return $editMeal;
    }

    public static function savePreparationMeals($mealData)
    {
        $preparationMeals = [];

        foreach($mealData['meal_data'] as $meal => $quantity){

            $foodItems = DB::table('food_item_meal')->where(['meal_id'=> $meal])->pluck('food_item_id');

            $mealPlan =  DB::table('meals')->where(['id'=> $meal])->value('meal_plan_id');
            $getShortcode = DB::table('meal_plan_types')->where(['id'=> $mealPlan])->value('shortcode');

            foreach($foodItems as $item){
                array_push($preparationMeals, [
                    'meal_id'           => $meal,
                    'food_item_id'      => $item,
                    'user_id'           => $mealData['user_id'],
                    'plan_shortcode'    => $getShortcode,
                    'preparation_at'    => date('Y-m-d', strtotime('-1 day', strtotime($mealData['date']))),
                    'quantity'          => $quantity,
                    'status'            => 'Pending',
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ]);
            }
        }

        $savedMeal = DB::table('kithen_user_meals')->insert($preparationMeals);
        return $savedMeal;
    }

    public static function editPreparationMeals($mealData){
        $date = date('Y-m-d', strtotime('-1 day', strtotime($mealData['date'])));
        DB::table('kithen_user_meals')->where(['user_id'=>$mealData['user_id']])
                                ->whereDate('preparation_at', '=', $date)->delete();
        $preparationMeals = [];

        foreach($mealData['meal_data'] as $meal => $quantity){

            $foodItems = DB::table('food_item_meal')->where(['meal_id'=> $meal])->pluck('food_item_id');

            $mealPlan =  DB::table('meals')->where(['id'=> $meal])->value('meal_plan_id');
            $getShortcode = DB::table('meal_plan_types')->where(['id'=> $mealPlan])->value('shortcode');

            foreach($foodItems as $item){
                array_push($preparationMeals, [
                    'meal_id'           => $meal,
                    'food_item_id'      => $item,
                    'user_id'           => $mealData['user_id'],
                    'plan_shortcode'    => $getShortcode,
                    'preparation_at'    => date('Y-m-d', strtotime('-1 day', strtotime($mealData['date']))),
                    'quantity'          => $quantity,
                    'status'            => 'Pending',
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ]);
            }
        }

        $savedMeal = DB::table('kithen_user_meals')->insert($preparationMeals);
        return $savedMeal;
    }

    public static function selectMealsList($userId)
    {
        $variationId = DB::table('user_meal_plans')->where(['user_id'=>$userId])->value('variation_id');
        $planId = DB::table('user_meal_plans')->where(['user_id'=>$userId])->value('meal_plan_id');
        $getData = DB::table('plan_variations')->select('meals','snacks')->where(['variation_id'=>$variationId,'plan_id'=>$planId])->get()->toArray();
        $getData = json_decode(json_encode($getData), true);
        foreach ($getData as $value) {
            $finalData[] = [
                $finalData['meals']= $value['meals'],
                $finalData['snacks']= $value['snacks'],
            ];
        }
        $totalMeals = $finalData['meals'] + $finalData['snacks'];
        return $totalMeals;
    }

    public static function saveSelectedMeals($data)
    {
        return DB::table('users_meals')->insert([
            'user_id'   => $data['user_id'],
            'meal_id'   => $data['meal_id'],
            'date'      => $data['date'],
        ]);
    }

    public static function myPlan($userId,$date)
    {
        return DB::table('users_meals')->where(['user_id'=>$userId])->get()->toArray();
    }

    public static function myPlanPerDate($date)
    {
        return DB::table('meals')->where(['meal_begin_at'=>$date])->get()->toArray();
    }

    public static function getMacrosByMealId($mealId,$gender)
    {
        return DB::table('macros')->where(['meal_id'=>$mealId,'gender'=>$gender])->get()->toArray();
    }

    public static function getAllergenIdByMealId($mealId)
    {
        return DB::table('allergens_meal')->where(['meal_id'=>$mealId])->pluck('allergens_id')->toArray();
    }

    public static function myPlanData($userId,$date)
    {
        // echo '<pre>';print_r($date);exit;
        $date = date('Y-m-d',strtotime($date)). " 00:00:00";
        $myPlans = DB::table('users_meals')->where(['user_id'=>$userId])
                    ->whereDate('date','=',$date)
                    ->get()->toArray();
        $mealData = [];
        $gender = DB::table('freshly_users')->where(['user_id'=>$userId])->value('gender');
        foreach ($myPlans as $value) {
            $mealPlan = DB::table('user_meal_plans')->select('meal_plan_id', 'variation_id', 'start_date')
                        ->where(['user_id'=>$userId])->first();
            $mealCount = self::getMealCountCopy($userId, $mealPlan->variation_id, $mealPlan->start_date, $date);
            $getMacros = Meal::getMacrosByMealId($value->meal_id,$gender);
            $getAllergens = Meal::with(['allergens'])->where(['id'=>$value->meal_id])->get();
            $getMealData = DB::table('meals')->where(['id'=>$value->meal_id])->first();
            $getMealDataArray = DB::table('meals')->where(['id'=>$value->meal_id])->get()->toArray();
            foreach ($getMealDataArray as $value1) {
                $mealNames = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal', 'food_item_meal.meal_id', '=', 'meals.id')
                    ->join('food_items', 'food_items.id', '=', 'food_item_meal.food_item_id')
                    ->where('food_item_meal.meal_id', '=', $value1->id)
                    ->pluck('food_items.title');
                $mealNames = json_decode(json_encode($mealNames), true);
                $finalName = implode(' + ', $mealNames);
                $value1->name       = $finalName;
            }
            $date = date('Y-m-d', strtotime($value->date));
            $macroData = $allergenData = [];
            $i=0;
            foreach ($getMacros as $macros) {
                $macroData[$i]['macro_id']  = (string)$macros->id;
                $macroData[$i]['label']     = (string)$macros->label;
                $macroData[$i]['value']     = (string)$macros->value;
                $macroData[$i]['unit']      = (string)$macros->unit;
                $i++;
            }
            foreach ($getAllergens as $allergensData) {
                foreach ($allergensData->allergens as $allergen) {
                    $allergenData[] = [
                        'allergens_id'  => (string)$allergen->id,
                        'icon'          => (string)$allergen->icon,
                        'title'         => (string)$allergen->title,
                    ];
                }
            }
            $quantity = DB::table('users_meals')->where(['user_id'=>$value->user_id,'meal_id'=>$value->meal_id,'date'=>$value->date])->value('quantity');
            // $meals = DB::table('meals')->where(['id']);

            $mealData[] = [
                'id'                => (string)$value->meal_id,
                'name'              => $finalName,
                'image'             => $getMealData->image,
                'category'          => $getMealData->meal_type,
                'date'              => $date,
                'is_selected'       => UserMealPlans::isMealSelected($value->user_id, $value->date, $value->meal_id),
                'quantity'          => (string)$quantity,
                // 'is_meal'      => UserMealPlans::isMealSelected($request->user_id, $request->date),
                'is_meal'           => (string)self::mealInfo($getMealData->id),
                'macros'            => $macroData,
                'allergens'         => $allergenData,
            ];
        }
        return $mealData;
    }

    public static function getMealCountCopy($userId, $variation, $planStartDate, $postDate){
        $from = date('Y-m-d', strtotime($planStartDate));
        $to   = date('Y-m-d', strtotime("+3 months", strtotime($planStartDate)));

        $week  = (int)substr($variation, 0, 1);
        $day   = (int)substr($variation, 1, 1);
        $meal  = (int)substr($variation, 2, 1);
        $snack = (int)substr($variation, 3, 1);

        $totalMealsCount = ($meal + $snack) * $day* $week;
        $totalSnack = $snack * 1;
        $totalMeals = $meal * 1;

        $selectedMeals = DB::table('users_meals')
                ->join('meals','meals.id','=','users_meals.meal_id')
                ->where(['users_meals.user_id'=> $userId, 'meals.meal_info'=> 1])
                ->whereDate('users_meals.date', '=', $postDate)
                ->sum('users_meals.quantity');

        $selectedSnacks = DB::table('users_meals')
            ->join('meals','meals.id','=','users_meals.meal_id')
            ->where(['users_meals.user_id'=> $userId, 'meals.meal_info'=> 0])
            ->whereDate('users_meals.date', '=', $postDate)
            ->sum('users_meals.quantity');

        $selectedTotalMeals = $selectedMeals + $selectedSnacks;

        $availableMeals = $totalMeals - $selectedMeals;
        $availableSnacks = $totalSnack - $selectedSnacks;

        $mealCount =  [
            'meals'     => (string) $availableMeals,
            'snacks'    => (string) $availableSnacks,
            'selected'  => (string) $selectedTotalMeals,
            'total'     => (string) $totalMealsCount,
        ];

        return $mealCount;
    }

    public static function mealInfo($mealId){
        $mealInfo = DB::table('meals')->where(['id'=>$mealId])->value('meal_info');
        return $mealInfo;
    }

    public static function myMeals($userId,$planId,$date){
        $meals = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId])
                ->get()->toArray();
        foreach ($meals as $meal) {
            $mealData = self::mealData($meal->meal_id);
            $mealDate = date('d M,Y', strtotime($date));
            $myMeals[] = [
                'meal_name'     =>$mealData->name,
                'meal_image'    =>$mealData->image,
                'meal_type'     =>$mealData->meal_type,
                'date'          =>$mealDate,
            ];
        }
        return $myMeals;
    }

    public static function mealData($mealId){
        return DB::table('meals')->where(['id'=>$mealId])->first();
    }
}