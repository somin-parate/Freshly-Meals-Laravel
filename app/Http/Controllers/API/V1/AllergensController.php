<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Allergens;
use App\Models\FreshlyAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class AllergensController extends BaseController
{
    protected $allergen = '';

    public function __construct(Allergens $allergen)
    {
        // $this->middleware('auth:api');
        $this->allergen = $allergen;
    }

    public function show($id)
    {
        $allergen = Allergens::find($id);
        return $this->sendResponse($allergen, 'Allergens Loaded Successfully');
    }

    public function index()
    {
        $allergen = $this->allergen->latest()->paginate(10);
        $allAllergens = $this->allergen->latest()->get()->toArray();
        return $this->sendResponse(['allAllergens' => $allAllergens, 'allergen'=> $allergen], 'Freshly Users list');
        // return $this->sendResponse($allergen, 'Allergens list');
    }

    public function store(Request $request)
    {
        $rules = [
            'title'    =>  'required',
            'icon'    =>  'required',
        ];

        $customMessages = [
            'title.required'      => 'Title is required!',
            'icon.required'      => 'Icon is required!',
        ];

        $this->validate($request, $rules, $customMessages);

        if ($request->icon) {
            $name = time() . '.' . explode('/', explode(':', substr($request->icon, 0, strpos($request->icon, ';')))[1])[1];
            \Image::make($request->icon)->save(public_path('images/api_images/') . $name);
            $request->merge(['icon' => $name]);
        }

        $exercise_category = new Allergens();
        $exercise_category->icon = $request->icon;
        $exercise_category->title = $request->title;
        $exercise_category->save();
        return $this->sendResponse($exercise_category, 'Expertise Added Successfully');
    }


    public function update(Request $request, $id)
    {
        $rules = [
            'title'    =>  'required',
        ];

        $customMessages = [
            'title.required'      => 'Title is required!',
        ];

        $this->validate($request, $rules, $customMessages);

        $exercise_category = Allergens::findOrFail($id);
        $currentImage = $exercise_category->icon;
        $currentImageName = explode("/", $currentImage, 2);

        if (($request->icon) && ($request->icon != $currentImageName[0])) {
            $parsed = $this->get_string_between($request->icon, 'data:image/', ';base64');
            $name = time() . Str::random(5) . '.' . $parsed;
            \Image::make($request->icon)->save(public_path('images/api_images/') . $name);
            $request->merge(['icon' => $name]);
            $currentImage = public_path('images/api_images/') . $currentImage;
            if (file_exists($currentImage)) {
                @unlink($currentImage);
            }
        }
        $exercise_category->update($request->all());
        return $this->sendResponse([$exercise_category], 'Allergens Updated Successfully');
    }

    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $exercise_category = Allergens::findOrFail($id);
        $exercise_category->delete();
        return $this->sendResponse([$exercise_category], 'Allergens has been Deleted');
    }

    public function getBankRequestsList()
    {
        $requestData = DB::table('user_meal_plans')->where(['payment_mode' => 'bank'])
            ->latest()->paginate(10);
        $i = 0;
        foreach ($requestData as $value) {
            $userName = FreshlyAuth::fetchFirstName($value->user_id);
            $date = date('d M,Y', strtotime($value->start_date));
            $mealData = DB::table('meal_plan_types')->where(['id' => $value->meal_plan_id])->first();
            $value->user_name      = (string)$userName;
            $value->plan_name      = (string)$mealData->title;
            $value->image          = (string)$mealData->image;
            $value->price          = "AED " . (string)$value->grand_total;
            $value->date           = (string)$date;
            $i++;
        }
        // $allBank = DB::table('user_meal_plans')->where(['payment_mode' => 'bank'])
        //                  ->latest()->get()->toArray();
        // $j = 0;
        // foreach ($requestData as $value1) {
        //     $userName = FreshlyAuth::fetchFirstName($value1->user_id);
        //     $date = date('d M,Y', strtotime($value1->start_date));
        //     $mealData = DB::table('meal_plan_types')->where(['id' => $value1->meal_plan_id])->first();
        //     $value1->user_name      = (string)$userName;
        //     $value1->plan_name      = (string)$mealData->title;
        //     $value1->image          = (string)$mealData->image;
        //     $value1->price          = "AED " . (string)$value1->grand_total;
        //     $value1->date           = (string)$date;
        //     $j++;
        // }
        return $this->sendResponse($requestData, 'Bank Requests List Successfully');
    }

    public function onlinePayments()
    {
        $requestData = DB::table('user_meal_plans')->select('user_meal_plans.*', 'payment_details.transaction_reference', 'payment_details.is_pending')
                        ->join('payment_details','payment_details.cart_id','=','user_meal_plans.cart_id')
                        ->where('user_meal_plans.payment_mode' ,'=', 'online')
                        ->latest()->paginate(10);
        // echo '<pre>';print_r($requestData);exit;
        $requests = [];
        $i = 0;
        foreach ($requestData as $value) {
            if($value->is_pending == 'false'){
                $pending = "No";
            }else{
                $pending = "Yes";
            }
            $userName = FreshlyAuth::fetchFirstName($value->user_id);
            $date = date('d M,Y', strtotime($value->start_date));
            $mealData = DB::table('meal_plan_types')->where(['id' => $value->meal_plan_id])->first();
            $value->user_name      = (string)$userName;
            $value->plan_name      = (string)$mealData->title;
            $value->image          = (string)$mealData->image;
            $value->price          = "AED " . (string)$value->grand_total;
            $value->date           = (string)$date;
            $value->pending           = (string)$pending;
            $i++;
        }
        return $this->sendResponse($requestData, 'Online List Successfully');
    }

    public function transactionByUserId($userId)
    {
        $requestData = DB::table('user_meal_plans')->where(['user_id' => $userId])->latest()->paginate(10);
        $i = 0;
        foreach ($requestData as $value) {
            $userName = FreshlyAuth::fetchFirstName($value->user_id);
            $date = date('d M,Y', strtotime($value->start_date));
            $mealData = DB::table('meal_plan_types')->where(['id' => $value->meal_plan_id])->first();
            $value->user_name      = (string)$userName;
            $value->plan_name      = (string)$mealData->title;
            $value->image          = (string)$mealData->image;
            $value->price          = "AED " . (string)$value->grand_total;
            $value->date           = (string)$date;
            $value->created_at     = date("F jS, Y", strtotime($value->created_at));
            $value->payment_mode   = $value->payment_mode;
            $i++;
        }
        return $this->sendResponse($requestData, 'Bank Requests List Successfully');
    }

    public function notificationByUserId($userId)
    {
        $notifications = DB::table('notifications')->where(['user_id' => $userId])->latest()->paginate(10);
        foreach ($notifications as $notification) {
            $notification->created_at = date("F jS, Y", strtotime($notification->created_at));
        }
        return $this->sendResponse($notifications, 'Notification List Successfully');
    }

    public function getAllAllergens(){
        $allergens = DB::table('allergens')->get()->toArray();
        foreach ($allergens as $value) {
            unset($value->created_at,$value->updated_at);
        }
        return $allergens;
    }
}