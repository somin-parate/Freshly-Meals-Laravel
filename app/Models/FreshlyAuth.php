<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\PaymentDetails;
use Validator;
use Hash;
use URL;
use DB;
use Log;
use DateTime;
use DateInterval;
use DatePeriod;

class FreshlyAuth extends Authenticatable
{
    use HasFactory;

    protected $guard = 'freshly_meals';

    protected $table = 'freshly_users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'emirate_id', 'fname', 'lname', 'email ', 'gender ','dob ', 'height ', 'weight ', 'blood_group ',
         'cardio ', 'weight_training ', 'allergies ', 'med_conditions', 'created_at', 'user_name', 'country_code'
    ];

    protected $hidden = [
        'updated_at', 'password', 'confirm_password'
    ];

    public static function userRegistration($data)
    {
        //Insert User Data
        $user = new FreshlyAuth();
        $user->fname             = $data['fname'];
        $user->mobile_number     = $data['mobile_number'];
        $user->country_code      = $data['country_code'];
        $user->email             = $data['email'];
        $user->password          = Hash::make($data['password']);
        $user->confirm_password  = Hash::make($data['confirm_password']);
        $user->otp               = mt_rand(1000,9999);
        $user->status            = '0';
        $user->source_type       = 'normal';
        $user->image             = "default.png";
        $user->save();

        //Return User Data
        return $user;
    }

    public static function userRegistrationSocialLogin($data)
    {
        if($data['registerType'] == 'facebook'){
            $values = [
                'source_type'   => $data['registerType'],
                'fname'         => $data['name'],
                'app_id'        => $data['app_id'],
                'status'        => "0",
            ];
        }else if ($data['registerType'] == 'gmail'){
            $values = [
                'source_type'   => $data['registerType'],
                'fname'         => $data['name'],
                'email'         => $data['email'],
                'status'         => "0",
            ];
        }

        return self::insert($values);
    }

    public static function getEmailById($userId)
    {
        return self::where(['user_id'=>$userId])->value('email');
    }

    public static function fetchUser($userId)
    {
        return self::where(['user_id'=>$userId])->get()->toArray();
    }

    public static function fetchFirstName($userId)
    {
        return self::where(['user_id'=>$userId])->value('fname');
    }

    public static function fetchUsername($userId)
    {
        return self::where(['user_id'=>$userId])->value('user_name');
    }

    public static function getOtpById($userId)
    {
        return self::where(['user_id'=>$userId])->value('otp');
    }

    public static function otpNotVerified($emailId)
    {
        $verify = self::where(['email'=>$emailId])->value('status');
        $data = "0";
        if($verify == 1){
            return $data = "1";
        }else {
            return $data = "0";
        }
    }

    public static function isEditProfile($userId)
    {
        $data = self::where(['user_id'=>$userId])->first();
        $status = "";
        if($data->gender == NULL && $data->dob == NULL && $data->blood_group == NULL && $data->cardio == NULL && $data->weight_training == NULL){
            $status = "0";
        }else {
            $status = "1";
        }
        return $status;
    }

    public static function isSubscribed($userId)
    {
        $getData = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                    ->orderBy('user_meal_plans.id', 'DESC')->first();
        $status = "";
        if($getData){
            if($getData->end_date > date('Y-m-d')){
                $status = "1";
            }else if(isset($getData)){
                $status = "1";
            }else{
                $status = "0";
            }
        }else{
                $status = "0";
        }

        return $status;
    }

    public static function isPaymentDone($userId)
    {
        $getData = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                    ->orderBy('user_meal_plans.id', 'DESC')->first();
        $online = PaymentDetails::where('cart_id',$getData->cart_id)->first();
        // echo '<pre>';print_r($getData);exit;
        $status = "";
        if($getData){
            if($getData->payment_mode == "bank"){
                if($getData->status == "1"){
                    $status = "1";
                }else if($getData->status == "0"){
                    $status = "0";
                }
            }else if($getData->payment_mode == "online"){
                if($online){
                    if($online->is_pending == "true" || $online->on_hold == "true"){
                        $status = "0";
                    }else{
                        $status = "1";
                    }
                }else{
                    $status = "0";
                }
            }
        }else{
                $status = "0";
            }
        return $status;
    }

    public static function isPaymentPending($userId)
    {
        $getData = DB::table('user_meal_plans')->where(['user_id'=>$userId])
                    ->orderBy('user_meal_plans.id', 'DESC')->first();
        $online = PaymentDetails::where('cart_id',$getData->cart_id)->first();
        // echo '<pre>';print_r($getData);exit;
        $status = "";
        if($getData->payment_mode == "online"){
            if($online){
                if($online->is_pending == "true" || $online->on_hold == "true"){
                    $status = "0";
                }else{
                    $status = "1";
                }
            }else{
                $status = "1";
            }
        }else{
            $status = "0";
        }
        return $status;
    }

    public static function getUpcomingSubscriptions($userId)
    {
        return DB::table('user_meal_plans')->select('user_meal_plans.*','plan_variations.weeks','plan_variations.days',
                'plan_variations.meals','plan_variations.snacks')
                ->join('plan_variations','plan_variations.variation_id', 'user_meal_plans.variation_id')
                ->where('user_meal_plans.user_id', '=' , $userId)
                ->whereDate('user_meal_plans.start_date', '>', date("Y-m-d"))
                ->orderBy('user_meal_plans.start_date', 'DESC')
                ->first();
    }

    public static function getActiveSubscriptions($userId)
    {
        return DB::table('user_meal_plans')->select('user_meal_plans.*','plan_variations.weeks','plan_variations.days',
                'plan_variations.meals','plan_variations.snacks')
                ->join('plan_variations','plan_variations.variation_id', 'user_meal_plans.variation_id')
                ->where('user_meal_plans.user_id', '=' , $userId)
                ->whereDate('user_meal_plans.start_date', '<=', date("Y-m-d"))
                ->whereDate('user_meal_plans.end_date', '>=', date("Y-m-d"))
                ->orderBy('user_meal_plans.start_date', 'DESC')
                ->first();
    }

    public static function getCompletedSubscriptions($userId)
    {
        $allMealComplete = DB::table('user_meal_plans')->select('user_meal_plans.*','plan_variations.weeks',
                            'plan_variations.days','plan_variations.meals','plan_variations.snacks')
                            ->join('plan_variations','plan_variations.variation_id', 'user_meal_plans.variation_id')
                            ->where('user_meal_plans.user_id', '=' , $userId)
                            ->whereColumn('user_meal_plans.total_meal', 'user_meal_plans.remaining_meal')
                            ->whereDate('user_meal_plans.end_date', '<', date('Y-m-d'))
                            ->orderBy('user_meal_plans.start_date', 'ASC')
                            ->get()->toArray();
        // echo '<pre>';print_r($allMealComplete);exit;
        $dateExpired = DB::table('user_meal_plans')->select('user_meal_plans.*','plan_variations.weeks',
                            'plan_variations.days','plan_variations.meals','plan_variations.snacks')
                        ->join('plan_variations','plan_variations.variation_id', 'user_meal_plans.variation_id')
                        ->where('user_meal_plans.user_id', '=' , $userId)
                        ->whereDate('user_meal_plans.end_date', '<', date('Y-m-d'))
                        ->orderBy('user_meal_plans.start_date', 'ASC')
                        ->get()->toArray();
        return array_merge($allMealComplete, $dateExpired);
    }

    public static function runningSubscription($userId,$startDate)
    {
        $data = DB::table('user_meal_plans')->where('user_meal_plans.user_id', '=' , $userId)
                ->whereDate('user_meal_plans.start_date', '<=', date("Y-m-d"))
                ->whereDate('user_meal_plans.end_date', '>', date('Y-m-d'))
                ->orderBy('user_meal_plans.start_date', 'DESC')
                ->first();
        $date = date('Y-m-d'). " 00:00:00";
        $myMeals = DB::table('users_meals')->where(['user_id'=>$userId])
                ->whereDate('date','>=',$date)
                ->orderBy('date','ASC')->get()->toArray();
        $last_meal_date = DB::table('users_meals')->where(['user_id'=>$userId])->orderBy('plan_id','DESC')->max('date');
        $status = "";
        if($data){
            $current_date_less_3_days = date('Y-m-d', strtotime('-3 days', strtotime($data->end_date)));
            $last_meal_date_less_3_days = date('Y-m-d', strtotime('-3 days', strtotime($last_meal_date)));
            if($startDate >= $last_meal_date_less_3_days){
                // echo 'test';exit;
                //UPDATING END DATE
                $plan_data = DB::table('user_meal_plans')->where('user_meal_plans.user_id', '=' , $userId)
                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                ->first();
                DB::table('user_meal_plans')->where('id', '=' , $plan_data->id)->update(['end_date'=>date('Y-m-d')]);
                $status = "0";
            }else if($startDate >= $current_date_less_3_days){
                //UPDATING END DATE
                $plan_data = DB::table('user_meal_plans')->where('user_meal_plans.user_id', '=' , $userId)
                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                ->first();
                DB::table('user_meal_plans')->where('id', '=' , $plan_data->id)->update(['end_date'=>date('Y-m-d')]);
                $status = "0";
            }else if($myMeals){
                $status = "1";
            }else{
                //UPDATING END DATE
                $plan_data = DB::table('user_meal_plans')->where('user_meal_plans.user_id', '=' , $userId)
                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                ->first();
                DB::table('user_meal_plans')->where('id', '=' , $plan_data->id)->update(['end_date'=>date('Y-m-d')]);
                $status = "0";
            }
        }else {
                $status = "0";
        }
        return $status;
    }

    public static function pauseSubscription($data)
    {
        $getPlanData = DB::table('user_meal_plans')->where(['user_id'=>$data['user_id'], 'id'=>$data['subscription_id']])->first();
        $finalDate = date('Y-m-d', strtotime('+8 week', strtotime($getPlanData->start_date)));
                // echo '<pre>';print_r($data['start_date']);exit;

        $from = $data['start_date'];
        $to = $data['end_date'];
        $fromKitchen = date('Y-m-d', strtotime('-1 day', strtotime($data['start_date'])));
        $toKitchen = date('Y-m-d', strtotime('-1 day', strtotime($data['end_date'])));
        $getDataBwDates = DB::table('users_meals')->where(['user_id'=>$data['user_id']])->whereBetween('date', [$from, $to])->get();
        $getKitchenDataBwDates = DB::table('kithen_user_meals')->where(['user_id'=>$data['user_id']])
                                    ->whereBetween('preparation_at', [$fromKitchen, $toKitchen])->get();
                // echo '<pre>';print_r($getKitchenDataBwDates);exit;
        if($finalDate > date('Y-m-d')){
            if(empty($getDataBwDates) && empty($getKitchenDataBwDates)){
                $paused = DB::table('users_meals')->where(['user_id'=>$data['user_id']])->whereBetween('date', [$from, $to])->delete();
                $paused = DB::table('kithen_user_meals')->where(['user_id'=>$data['user_id']])
                                    ->whereBetween('preparation_at', [$fromKitchen, $toKitchen])->delete();
            }else {
                return "1";
            }
        }else {
            $paused = "Can,t pause plan";
            return $paused;
        }
        return $paused;
    }

    public static function resubscribePlan($data)
    {
        $fetchSubscription = DB::table('user_meal_plans')->where(['id'=>$data['subscription_id']])->first();
        $today = date("Ymd");
        $rand   = sprintf("%04d", rand(0,9999));
        $startDate = date('Y-m-d',strtotime($data['start_date']));
        $unique = $today . $rand;
        $values = [
            'user_id'               => $data['user_id'],
            'meal_plan_id'          => $fetchSubscription->meal_plan_id,
            'variation_id'          => $fetchSubscription->variation_id,
            'book_nutritionist'     => $fetchSubscription->book_nutritionist,
            'start_date'            => $startDate,
            'order_number'          => $unique,
            'order_address'         => $fetchSubscription->order_address,
            'created_at'            => date('Y-m-d'),
            'updated_at'            => date('Y-m-d'),
        ];
        return DB::table('cart_summary')->insert($values);
    }

    public static function getUnreadCount($userId){
        return DB::table('notifications')->where(['user_id'=>$userId,'status'=>1])->count('status');
    }

    public static function isMealSelectedOnDate($userId,$date){
        $date = $date." 00:00:00";
        // echo '<pre>';print_r($date);exit;
        $selected_meals = DB::table('users_meals')->where('user_id',$userId)
                            ->whereDate('date','=',$date)
                            ->get()->toArray();
        $status = "0";
        if(!empty($selected_meals)){
            $status = "1";
        }else{
            $status = "0";
        }
        return $status;
    }

    public static function getUsersMeals($userId,$date)
    {
        $userMeals = DB::table('users_meals')->where(['user_id'=>$userId,'date'=>$date])->get()->toArray();
        $meals = [];
        $i=0;
        foreach ($userMeals as $meal) {
            $getMeal = DB::table('meals')->where(['id'=>$meal->meal_id])->first();
            $date = explode(' ',$meal->date);
            $date = $date[0];
            $meals[$i]['date']          = $date;
            $meals[$i]['meal_name']     = $getMeal->name;
            $meals[$i]['image']         = $getMeal->image;
            $meals[$i]['quantity']      = $meal->quantity;
            $i++;
        }
        return $meals;
    }

    // public static function FindDataBwDates($start,$end)
    // {
    //     $array = array();
    //     $format = 'Y-m-d';
    //     // Variable that store the date interval
    //     // of period 1 day
    //     $interval = new DateInterval('P1D');

    //     $realEnd = new DateTime($end);
    //     $realEnd->add($interval);

    //     $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    //     // Use loop to store date into array
    //     foreach($period as $date) {
    //         $array[] = $date->format($format);
    //     }

    //     // Return the array elements
    //     return $array;
    // }
}
