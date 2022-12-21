<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\Allergens;
use Illuminate\Support\Str;
use App\Models\Macro;
use Illuminate\Support\Facades\DB as FacadesDB;
use Validator;
use DB;

class MealController extends BaseController
{
    protected $meal = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Meal $meal)
    {
        $this->middleware('auth:api');
        $this->meal = $meal;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $meals = $this->meal->latest()->paginate(10);
        foreach ($meals as $value) {
            $mealNames = DB::table('meals')->select('food_items.title')
                ->join('food_item_meal', 'food_item_meal.meal_id', '=', 'meals.id')
                ->join('food_items', 'food_items.id', '=', 'food_item_meal.food_item_id')
                ->where('food_item_meal.meal_id', '=', $value->id)
                ->pluck('food_items.title');
            $gender = DB::table('meals')->select('macros.gender')
                    ->join('macros', 'macros.meal_id', '=', 'meals.id')
                    ->where('meals.id', '=', $value->id)
                    ->value('gender');
            $mealNames = json_decode(json_encode($mealNames), true);
            $mealPlanType = DB::table('meal_plan_types')->where('id',$value->meal_plan_id)->value('title');
            $finalName = implode(' + ', $mealNames);
            $value['gender']       = $gender;
            $value['name']       = $finalName;
            $value['plan_name']       = $mealPlanType;
        }
        $allMeals1 = $this->meal::latest()->get();
        foreach ($allMeals1 as $value1) {
            $mealNames = DB::table('meals')->select('food_items.title','macros.gender')
                ->join('food_item_meal', 'food_item_meal.meal_id', '=', 'meals.id')
                ->join('food_items', 'food_items.id', '=', 'food_item_meal.food_item_id')
                ->join('macros', 'macros.meal_id', '=', 'meals.id')
                ->where('food_item_meal.meal_id', '=', $value1->id)
                ->pluck('food_items.title');
            $gender = DB::table('meals')->select('macros.gender')
                ->join('macros', 'macros.meal_id', '=', 'meals.id')
                ->where('meals.id', '=', $value1->id)
                ->value('gender');
                // echo '<pre>';print_r($gender);
            $mealNames = json_decode(json_encode($mealNames), true);
            $mealPlanType = DB::table('meal_plan_types')->where('id',$value1->meal_plan_id)->value('title');
            $finalName = implode(' + ', $mealNames);
            $value1['name']       = $finalName;
            $value1['gender']       = $gender;
            $value1['plan_name']       = $mealPlanType;
        }
        // exit;
        $allMeals = $allMeals1->toArray();
        return $this->sendResponse(['allMeals' => $allMeals, 'meals'=> $meals], 'Freshly Users list');
        // return $this->sendResponse($meals, 'Meal list');
    }

    public function loadMeals()
    {
        $meals = $this->meal->select('id', 'name')->get();
        // echo '<pre>';print_r($meals);exit;
        return $this->sendResponse($meals, 'Meals Shown Successfully!');
    }

    public function store(Request $request)
    {

        $rules = [
            // 'meal_name'             =>  'required',
            'meal_image'            =>  'required',
            'meal_category'         =>  'required',
            'meal_type'             =>  'required',
            'meal_plan_type'        =>  'required',
            'gender'                =>  'required',
        ];

        $customMessages = [
            // 'meal_name.required'          => 'Meal name is required !',
            'meal_image.required'         => 'Select Meal image !',
            'meal_category.required'      => 'Select Meal Category !',
            'meal_type.required'          => 'Select Meal Type !',
            'meal_plan_type.required'     => 'Select Meal plan !',
            'gender.required'             => 'Select Gender !',
        ];

        $this->validate($request, $rules, $customMessages);

        if ($request->meal_image) {
            $name = time() . '.' . explode('/', explode(':', substr($request->meal_image, 0, strpos($request->meal_image, ';')))[1])[1];
            \Image::make($request->meal_image)->save(public_path('images/api_images/') . $name);
            $request->merge(['image' => $name]);
        }

        // if($request->get('meal_type') == 'Meal'){
        //     $mealType = '1';
        // }else{
        //     $mealType = '0';
        // }
        foreach ($request->date as $value) {
            $meal_date = date('Y-m-d',strtotime($value));
            $meal = new Meal();
            $meal->image              = $request->get('image');
            $meal->meal_type          = $request->get('meal_category');
            $meal->meal_info          = $request->get('meal_type');
            $meal->meal_plan_id       = $request->get('meal_plan_type');
            $meal->meal_begin_at       = $meal_date;
            $meal->save();

            // Synk Allergens
            $meal->allergens()->sync($request->allergens_options);

            // Synk Food Items
            $meal->fooditems()->sync($request->food_items);

            // Synk Macros
            foreach (json_decode($request->input('dynamic_macros')) as $key => $macro) {
                if ($key == 0) {
                    $label = 'Calories';
                    $val = 'kcalsVal';
                    $unit = 'kcalsUnit';
                } else if ($key == 1) {
                    $label = 'Protein';
                    $val = 'proteinVal';
                    $unit = 'proteinUnit';
                } else if ($key == 2) {
                    $label = 'Energy';
                    $val = 'energyVal';
                    $unit = 'energyUnit';
                } else if ($key == 3) {
                    $label = 'Carbs';
                    $val = 'carbsVal';
                    $unit = 'carbsUnit';
                }
                $synkmacro = new Macro;
                $synkmacro->meal_id = $meal->id;
                $synkmacro->gender  = $request->gender;
                $synkmacro->label   = $label;
                $synkmacro->value   = $macro->$val;
                $synkmacro->unit    = $macro->$unit;
                $meal->macros()->save($synkmacro);
            }
        }


        return $this->sendResponse($meal, 'Meal Created Successfully');
    }

    public function show($id)
    {
        $meal = Meal::with(['macros', 'allergens', 'fooditems'])->where(['id' => $id])->first();
        return $this->sendResponse($meal, 'Meal Shown Successfully');
    }

    public function update(Request $request, $id)
    {

        // dd($request->all());
        // exit;
        $rules = [
            // 'meal_name'             =>  'required',
            'meal_category'         =>  'required',
            'meal_plan_type'        =>  'required',
            'meal_type'        =>  'required',
            'gender'        =>  'required',
        ];

        $customMessages = [
            // 'meal_name.required'          => 'Meal name is required !',
            'meal_category.required'      => 'Select Meal Category !',
            'meal_type.required'      => 'Select Meal Type !',
            'meal_plan_type.required'     => 'Select Meal plan !',
            'gender.required'     => 'Select Gender !',
        ];

        $this->validate($request, $rules, $customMessages);

        // if($request->get('meal_type') == 'Meal'){
        //     $mealType = '1';
        // }else{
        //     $mealType = '0';
        // }

        $meal = Meal::findOrFail($id);
        // $meal->name               = $request->get('meal_name');
        $meal->meal_type          = $request->get('meal_category');
        $meal->meal_info          = $request->get('meal_type');
        $meal->meal_plan_id       = $request->get('meal_plan_type');
        $meal->meal_begin_at      = $request->get('meal_begin_at');

        if ($request->image != NULL) {
            $parsed = $this->get_string_between($request->image, 'data:image/', ';base64');
            $name = time() . Str::random(5) . '.' . $parsed;
            \Image::make($request->image)->save(public_path('images/api_images/') . $name);
            $currentImage = public_path('images/api_images/') . $meal->image;
            if (file_exists($currentImage)) {
                @unlink($currentImage);
            }
            $meal->image = $name;
        }
        $meal->save();

        // Synk Macros
        $meal->macros()->delete();
        foreach (json_decode($request->input('dynamic_macros')) as $key => $macro) {
            if ($key == 0) {
                $label = 'Kcals';
                $val = 'kcalsVal';
                $unit = 'kcalsUnit';
            } else if ($key == 1) {
                $label = 'Protein';
                $val = 'proteinVal';
                $unit = 'proteinUnit';
            } else if ($key == 2) {
                $label = 'Energy';
                $val = 'energyVal';
                $unit = 'energyUnit';
            } else if ($key == 3) {
                $label = 'Carbs';
                $val = 'carbsVal';
                $unit = 'carbsUnit';
            }
            $synkmacro = new Macro;
            $synkmacro->meal_id = $meal->id;
            $synkmacro->gender  = $request->gender;
            $synkmacro->label   = $label;
            $synkmacro->value   = $macro->$val;
            $synkmacro->unit    = $macro->$unit;
            $meal->macros()->save($synkmacro);
        }

        // Synk Allergens
        $meal->allergens()->sync($request->selected_allergens);

        // Synk Food Items
        $meal->fooditems()->sync($request->selected_fooditems);

        return $this->sendResponse([$meal], 'Meal Updated Successfully');
    }

    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $meal = Meal::findOrFail($id);

        //Delete image if plan type deleted
        $currentImage = $meal->image;
        $currentImage = public_path('images/api_images/') . $currentImage;
        if (file_exists($currentImage)) {
            @unlink($currentImage);
        }

        // remove synked allergens
        $allergensIds = FacadesDB::table('allergens_meal')->where([
            'meal_id'    => $id,
        ])->pluck('allergens_id');
        $meal->allergens()->detach($allergensIds);

        // remove synked food_items
        $foodItemsIds = FacadesDB::table('food_item_meal')->where([
            'meal_id'    => $id,
        ])->pluck('food_item_id');
        $meal->fooditems()->detach($foodItemsIds);

        $meal->delete();
        return $this->sendResponse([$meal], 'Plan Type has been Deleted');
    }

    public function getAllergensName()
    {
        $allergens = DB::table('allergens')->get();
        return $this->sendResponse($allergens, 'Allergens Name list');
    }

    public function getMacros()
    {
        $macros = DB::table('macros')->get();
        return $this->sendResponse($macros, 'Allergens Name list');
    }

    public function MealsList(Request $request, $mealData = [])
    {
        $validatedData = Validator::make($request->all(), [
            'date'             => 'required',
            'category'         => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $date = implode(" ", $request->date);
        // echo "<pre>";
        // print_r($date);
        // exit;
        if ($request->category) {
            $mealList = Meal::with(['macros', 'allergens'])->where(['meal_begin_at' => $request->date, 'meal_type' => $request->category])->get();
        } else {
            $mealList = Meal::with(['macros', 'allergens'])->where(['meal_begin_at' => $request->date])->get();
        }
        foreach ($mealList as $meal) {
            $macroData = $allergenData = [];
            foreach ($meal->macros as $macro) {
                $macroData[] = [
                    'id'     => (string)$macro->id,
                    'label'  => $macro->label,
                    'value'  => (string)$macro->value,
                    'unit'   => $macro->unit,
                ];
            }
            foreach ($meal->allergens as $allergen) {
                $allergenData[] = [
                    'id'     => (string)$allergen->id,
                    'title'  => $allergen->title,
                    'icon'   => $allergen->icon,
                ];
            }
            $mealData[] = [
                'id'           => (string)$meal->id,
                'name'         => $meal->name,
                'image'        => $meal->image,
                'category'     => $meal->meal_type,
                'date'         => $meal->meal_begin_at,
                'macros'       => $macroData,
                'allergens'    => $allergenData,
            ];
        }
        return $this->sendResponse($mealData, 'Meal List Successfully');
    }

    public function dashboardData()
    {
        $meals          = DB::table('meals')->count();
        $foodItems      = DB::table('food_items')->count();
        $users          = DB::table('freshly_users')->count();
        $subscriptions  = DB::table('user_meal_plans')->count();
        return $this->sendResponse(['meals' => $meals, 'foodItems'=> $foodItems, 'users'=> $users, 'subscriptions'=> $subscriptions], 'Dashbard Data list');
    }
}