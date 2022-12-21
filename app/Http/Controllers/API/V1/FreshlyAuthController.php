<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\FreshlyAuth;
use App\Models\UserMealPlans;
use App\Models\Address;
use App\Models\ImageUpload;
use App\Models\VerifyOtp;
use App\Models\PaymentDetails;
use App\Models\ApiToken;
use App\Models\Offer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\PushNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyTestMail;
use App\Mail\ResendOtp;
use App\Mail\SignUpMail;
use App\Mail\PauseSubscriptionMail;
use App\Mail\ResumeSubscriptionMail;
use App\Mail\ResubscriptionMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Http\Request;
use Validator;
use Hash;
use URL;
use DB;
use Illuminate\Support\Str;
use Log;
use Password;

class FreshlyAuthController extends BaseController
{
    use AuthenticatesUsers;

    public function register(Request $request)
    {
        // Log::Info($request->all());exit;
        $validatedData = Validator::make($request->all(), [
            'fname'             => 'required|max:255',
            'mobile_number'     => 'required|max:255',
            'email'             => 'email|required|unique:freshly_users',
            'password'          => 'required',
            'confirm_password'  => 'required|same:password',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
          return $this->sendError([], 'Please enter a valid email!');
        }

        $user_reg = FreshlyAuth::userRegistration($request->all());
        // echo '<pre>';print_r($user_reg->user_id);exit;
        if(!empty($user_reg))
        {
            //Collecting Token Info
            $token_data = array(
                'device_token' => $request->device_token,
                'device_type'  => $request->device_type,
                'language'     => $request->language,
                'user_id'      => $user_reg['user_id'],
            );
            //Insert Or Update Token
            $ext_device = ApiToken::checkDeviceExist($request->device_token);
            if(!empty($ext_device)){
                $fill_token = ApiToken::updateDeviceData($ext_device->token_id, $user_reg->user_id);
            }else{
                $fill_token = ApiToken::deviceReg($token_data);
            }
            if((!empty($user_reg)) && (!empty($fill_token))){
                $otpInsert = DB::table('freshly_users')->where(['user_id'=>$user_reg->user_id])->value('otp');
                $explodeOtp = str_split($otpInsert);
                // echo '<pre>';print_r($explodeOtp);exit;
                //Send Mail To User
                $to_email = $request->email;
                Mail::to($to_email)->send(new MyTestMail($explodeOtp));
                //End Send Mail To User
                $getList = [
                    'user_id'           => (string)$user_reg['user_id'],
                    'fname'             => (string)$user_reg['fname'],
                    'mobile_number'     => (string)$user_reg['mobile_number'],
                    'country_code'      => (string)$user_reg['country_code'],
                    'email'             => (string)$user_reg['email'],
                    'otp'               => (string)$user_reg['otp'],
                    'status'            => (string)$user_reg['status'],
                    'image'             => "default.png",
                ];
                return $this->sendResponse($getList, 'Verification code sent to your email!');
            }
        }else {
            return $this->sendError([], 'Registered Fail');
        }
    }

    public function socialLogin(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'registerType'     => 'required|max:255',
            'name'      => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        if($request->registerType == 'facebook'){
            $validatedData = Validator::make($request->all(), [
                'app_id'    => 'required|max:255',
            ]);
            if ($validatedData->fails()) {
                $validation_error['status']  = 'fail';
                $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
                $validation_error['data']    = [];
                return response()->json($validation_error);
            }
        }else if ($request->registerType == 'gmail'){
            $validatedData = Validator::make($request->all(), [
                'email'  => '',
            ]);
            if ($validatedData->fails()) {
                $validation_error['status']  = 'fail';
                $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
                $validation_error['data']    = [];
                return response()->json($validation_error);
            }
        }
        $appleId = DB::table('freshly_users')->where(['app_id'=>$request->app_id])->value('app_id');
        if($appleId){
            $userData = DB::table('freshly_users')->where(['app_id'=>$request->app_id])->first();
            $getAppList = [
                    'user_id'           => (string)$userData->user_id,
                    // "emirate_id"        => (string)$user->emirate_id,
                    "fname"             => (string)$userData->fname,
                    // "lname"             => (string)$user->lname,
                    "email"             => (string)$userData->email,
                    "image"             => (string)$userData->image,
                    "gender"            => (string)$userData->gender,
                    "dob"               => (string)$userData->dob,
                    "height"            => (string)$userData->height,
                    "weight"            => (string)$userData->weight,
                    "blood_group"       => (string)$userData->blood_group,
                    "cardio"            => (string)$userData->cardio,
                    "weight_training"   => (string)$userData->weight_training,
                    "allergies"         => (string)$userData->allergies,
                    "med_conditions"    => (string)$userData->med_conditions,
                    "source_type"       => (string)$userData->source_type,
                    "app_id"            => (string)$userData->app_id,
                    "app_id"            => (string)$userData->app_id,
                    'is_otp'            => "1",
                    'is_edit_profile'   => FreshlyAuth::isEditProfile($userData->user_id),
                    'is_address'        => Address::isAddress($userData->user_id),
                    'is_subscribed'     => FreshlyAuth::isSubscribed($userData->user_id),
                ];
             //Collecting Token Info
            $token_data = array(
                'device_token' => $request->device_token,
                'device_type'  => $request->device_type,
                'language'     => $request->language,
                'user_id'      => $userData->user_id,
            );
            //Insert Or Update Token
            $ext_device = ApiToken::checkDeviceExist($request->device_token);
            if(!empty($ext_device)){
                $fill_token = ApiToken::updateDeviceData($ext_device->token_id, $userData->user_id);
            }else{
                $fill_token = ApiToken::deviceReg($token_data);
            }
            return $this->sendResponse([$getAppList], 'Login through facebook successfully!');
        }
        $emailId = DB::table('freshly_users')->where(['email'=>$request->email])->value('email');
        if($emailId){
            $userData = DB::table('freshly_users')->where(['email'=>$request->email])->first();
            $getUpdatedList = [
                    "fname"             => $request->name,
                    "source_type"       => $request->registerType,
                    "app_id"            => $request->app_id,
                ];
            $userListUpdated = DB::table('freshly_users')->where(['user_id'=>$userData->user_id])->update($getUpdatedList);
            $updateList = DB::table('freshly_users')->where(['user_id'=>$userData->user_id])->first();
            // echo '<pre>';print_r($userListUpdated);exit;
            $getEmailList = [
                     'user_id'           => (string)$updateList->user_id,
                    // "emirate_id"        => (string)$user->emirate_id,
                    "fname"             => (string)$updateList->fname,
                    // "lname"             => (string)$user->lname,
                    "email"             => (string)$updateList->email,
                    "image"             => (string)$updateList->image,
                    "gender"            => (string)$updateList->gender,
                    "dob"               => (string)$updateList->dob,
                    "height"            => (string)$updateList->height,
                    "weight"            => (string)$updateList->weight,
                    "blood_group"       => (string)$updateList->blood_group,
                    "cardio"            => (string)$updateList->cardio,
                    "weight_training"   => (string)$updateList->weight_training,
                    "allergies"         => (string)$updateList->allergies,
                    "med_conditions"    => (string)$updateList->med_conditions,
                    "source_type"       => (string)$updateList->source_type,
                    "app_id"            => (string)$updateList->app_id,
                    'is_otp'            => "1",
                    'is_edit_profile'   => FreshlyAuth::isEditProfile($updateList->user_id),
                    'is_address'        => Address::isAddress($updateList->user_id),
                    'is_subscribed'     => FreshlyAuth::isSubscribed($updateList->user_id),
                ];
            //Collecting Token Info
            $token_data = array(
                'device_token' => $request->device_token,
                'device_type'  => $request->device_type,
                'language'     => $request->language,
                'user_id'      => $updateList->user_id,
            );
            //Insert Or Update Token
            $ext_device = ApiToken::checkDeviceExist($request->device_token);
            if(!empty($ext_device)){
                $fill_token = ApiToken::updateDeviceData($ext_device->token_id, $updateList->user_id);
            }else{
                $fill_token = ApiToken::deviceReg($token_data);
            }
            return $this->sendResponse([$getEmailList], 'Login through gmail successfully!');
        }
        $user_reg = FreshlyAuth::userRegistrationSocialLogin($request->all());
        if($request->registerType == 'facebook'){
            $fetchUser = DB::table('freshly_users')->where(['app_id'=>$request->app_id])->first();
        }else if($request->registerType == 'gmail'){
            $fetchUser = DB::table('freshly_users')->where(['email'=>$request->email])->first();
        }
        if(!empty($fetchUser))
        {
            //Collecting Token Info
            $token_data = array(
                'device_token' => $request->device_token,
                'device_type'  => $request->device_type,
                'language'     => $request->language,
                'user_id'      => $fetchUser->user_id,
            );
            //Insert Or Update Token
            $ext_device = ApiToken::checkDeviceExist($request->device_token);
            if(!empty($ext_device)){
                $fill_token = ApiToken::updateDeviceData($ext_device->token_id, $fetchUser->user_id);
            }else{
                $fill_token = ApiToken::deviceReg($token_data);
            }
            if((!empty($fetchUser)) && (!empty($fill_token))){
                $getList = [
                    'user_id'           => (string)$fetchUser->user_id,
                    // "emirate_id"        => (string)$user->emirate_id,
                    "fname"             => (string)$fetchUser->fname,
                    // "lname"             => (string)$user->lname,
                    "email"             => (string)$fetchUser->email,
                    "image"             => (string)$fetchUser->image,
                    "gender"            => (string)$fetchUser->gender,
                    "dob"               => (string)$fetchUser->dob,
                    "height"            => (string)$fetchUser->height,
                    "weight"            => (string)$fetchUser->weight,
                    "blood_group"       => (string)$fetchUser->blood_group,
                    "cardio"            => (string)$fetchUser->cardio,
                    "weight_training"   => (string)$fetchUser->weight_training,
                    "allergies"         => (string)$fetchUser->allergies,
                    "med_conditions"    => (string)$fetchUser->med_conditions,
                    "source_type"       => (string)$fetchUser->source_type,
                    "app_id"            => (string)$fetchUser->app_id,
                    'is_otp'            => "1",
                    'is_edit_profile'   => FreshlyAuth::isEditProfile($fetchUser->user_id),
                    'is_address'        => Address::isAddress($fetchUser->user_id),
                    'is_subscribed'    => FreshlyAuth::isSubscribed($fetchUser->user_id),
                ];
                return $this->sendResponse([$getList], 'Registered Successfully');
            }
        }else {
            return $this->sendError([], 'Registered Fail');
        }
    }

    public function verifyEmail(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'   => 'required',
            'otp'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $otp_exists = FreshlyAuth::getOtpById($request->user_id);
        if($otp_exists == $request->otp) {
            $to_email = FreshlyAuth::getEmailById($request->user_id);
            Mail::to($to_email)->send(new SignUpMail);
            DB::table('freshly_users')->where(['user_id'=>$request->user_id])->update(['status'=>'1']);
            $OriginalString1 = 'You have successfully m1 verified your account.';
            $message1 = explode("m1 ",$OriginalString1);

            return $this->sendResponse([], $message1);
        }else {
            $OriginalString2 = 'Oops! Your verification code m2 is not a match.';
            $message2 = explode("m2 ",$OriginalString2);
            return $this->sendError([], $message2);
        }
    }

    public function resendOtp(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $otp_resend = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->update(['otp'=>mt_rand(1000,9999)]);
        if($otp_resend) {
            $otpInsert = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('otp');
            $explodeOtp = str_split($otpInsert);
            //Send Mail To User
            $to_email = FreshlyAuth::getEmailById($request->user_id);
            Mail::to($to_email)->send(new ResendOtp($explodeOtp));
            //End Send Mail To User

            return $this->sendResponse([], 'Otp Resend Successfully');
        }else {
            return $this->sendError([], 'Unable to resend code');
        }
    }

    public function signIn(Request $request)
    {
        $validatedData = Validator::make($request->all(), ['email' => 'required','password' => 'required']);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
          return $this->sendError([], 'Please enter a valid email!');
        }

        $otpVerify = FreshlyAuth::otpNotVerified($request->email);
        // if($otpVerify == '0'){
        //     return $this->sendError([], 'You need to first verify your email!');
        // }

        if ($validatedData->passes()) {
            $field = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'fname';
            $value = $request->get('email');
            $credentials = array($field => $value, 'password' => $request->get('password'));
            if (Auth::guard('fresly_meals')->attempt($credentials)) {
                $user = Auth::guard('fresly_meals')->user();
                array_walk_recursive($user, function(&$input){
                    $input = strval($input);
                });

                // echo '<pre>';print_r($user);exit;
                // $user = json_decode($user, true);
                $getList = [
                    'user_id'           => (string)$user->user_id,
                    // "emirate_id"        => (string)$user->emirate_id,
                    "fname"             => (string)$user->fname,
                    // "lname"             => (string)$user->lname,
                    "country_code"      => (string)$user->country_code,
                    "mobile_number"     => (string)$user->mobile_number,
                    "email"             => (string)$user->email,
                    "image"             => (string)$user->image,
                    "gender"            => (string)$user->gender,
                    "dob"               => (string)$user->dob,
                    "height"            => (string)$user->height,
                    "weight"            => (string)$user->weight,
                    "blood_group"       => (string)$user->blood_group,
                    "cardio"            => (string)$user->cardio,
                    "weight_training"   => (string)$user->weight_training,
                    "allergies"         => (string)$user->allergies,
                    "med_conditions"    => (string)$user->med_conditions,
                    // "source_type"       => (string)$user->source_type,
                    // "app_id"            => (string)$user->app_id,
                    'is_otp'            => $otpVerify,
                    'is_edit_profile'   => FreshlyAuth::isEditProfile($user->user_id),
                    'is_address'        => Address::isAddress($user->user_id),
                    'is_subscribed'    => FreshlyAuth::isSubscribed($user->user_id),
                ];
                //Check If Device Token Exists
                if(!empty($request->device_token))
                {
                    $ext_device = APIToken::checkDeviceExistLogin($request->device_token, $user->user_id);
                    if(empty($ext_device))
                    {
                        $validateToken = Validator::make($request->all(),
                        ['device_token' => 'required','device_type' => 'required','language' => 'required']);

                        if ($validateToken->fails()) {
                            $token_error = array();
                            $token_error['errors'] = implode('|| ', $validateToken->messages()->all());
                            return response()->json(['data' => $token_error]);
                        }
                        $token_data = [
                            'device_token' => $request->device_token,
                            'device_type'  => $request->device_type,
                            'language'     => $request->language,
                            'user_id'      => $user->user_id,
                        ];
                        $fill_token = APIToken::deviceReg($token_data);
                    }
                }
                    return $this->sendResponse([$getList], 'Login Successfully');
            }
            else
            {
                return $this->sendError([], 'Login Failed');
            }
        }
    }

    public function Logout(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'      => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $isDeleted = ApiToken::deleteDeviceData($request->all());
        if($isDeleted){
            return $this->sendResponse([], 'Logout Successfully!');
        }else {
            return $this->sendError([], 'Logout Failed');
        }
    }

    public function editprofile(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required|exists:freshly_users',
            'fname'             => 'required|max:255',
            // 'lname'             => 'required|max:255',
            'image'             => '',
            'gender'            => 'required',
            'dob'               => 'required',
            // 'emirate_id'        => 'required',
            'height'            => '',
            'weight'            => '',
            'blood_group'       => 'required',
            'cardio'            => 'required',
            // 'weight_training'   => 'required',
            'allergies'         => '',
            'med_conditions'    => '',
            'mobile_number'     => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }


        $dob = date('Y-m-d',strtotime($request->dob));
        $userdata = [
            'fname'             => (string)$request->fname,
            // 'lname'             => (string)$request->lname,
            // 'emirate_id'        => (string)$request->emirate_id,
            //'image'             => "default.png",
            'dob'               => (string)$dob,
            'height'            => (string)$request->height,
            'weight'            => (string)$request->weight,
            'blood_group'       => (string)$request->blood_group,
            'cardio'            => (string)$request->cardio,
            // 'weight_training'   => (string)$request->weight_training,
            'allergies'         => (string)$request->allergies,
            'med_conditions'    => (string)$request->med_conditions,
            'gender'            => (string)$request->gender,
            'country_code'      => (string)$request->country_code,
            'mobile_number'     => (string)$request->mobile_number,
            'remember_token'    => "",
        ];

        $updateProfile = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->update($userdata);
        $fetchUser     = FreshlyAuth::fetchUser($request->user_id);
        if(($fetchUser)){
            unset($fetchUser->password);
            $fetchUser = [
                'user_id'           => (string)$request->user_id,
                'fname'             => (string)$request->fname,
                // 'lname'             => (string)$request->lname,
                // 'emirate_id'        => (string)$request->emirate_id,
                //'image'             => "default.png",
                'dob'               => (string)$request->dob,
                'height'            => (string)$request->height,
                'weight'            => (string)$request->weight,
                'blood_group'       => (string)$request->blood_group,
                'cardio'            => (string)$request->cardio,
                // 'weight_training'   => (string)$request->weight_training,
                'allergies'         => (string)$request->allergies,
                'med_conditions'    => (string)$request->med_conditions,
                'gender'            => (string)$request->gender,
                'mobile_number'     => (string)$request->mobile_number,
                'country_code'      => (string)$request->country_code,
                'remember_token'    => "",
            ];
            return $this->sendResponse([$fetchUser], 'User Profille Updated Successfully!');
        }else{
            return $this->sendError([], 'Unable to Update User Profile');
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'user_id'      => 'required|exists:freshly_users',
            ]);
            if ($validatedData->fails()) {
                $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
                $validation_error['status']  = 'fail';
                $validation_error['data']    = [];
                return response()->json($validation_error);
            }
            $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
            if(!empty($userData)){
                unset($userData->password);
                unset($userData->confirm_password);
                if(empty($userData->image)){ $userData->image = 'default.jpg'; }
                $userData = [
                    'unread_count'      => (string)FreshlyAuth::getUnreadCount($request->user_id),
                    'user_id'           => (string)$userData->user_id,
                    'fname'             => (string)$userData->fname,
                    // 'lname'             => (string)$userData->lname,
                    'emirate_id'        => (string)$userData->emirate_id,
                    'email'             => (string)$userData->email,
                    'image'             => (string)$userData->image,
                    'dob'               => (string)date('d-m-Y',strtotime($userData->dob)),
                    'height'            => (string)$userData->height,
                    'weight'            => (string)$userData->weight,
                    'blood_group'       => (string)$userData->blood_group,
                    'cardio'            => (string)$userData->cardio,
                    'weight_training'   => (string)$userData->weight_training,
                    'allergies'         => (string)$userData->allergies,
                    'med_conditions'    => (string)$userData->med_conditions,
                    'gender'            => (string)$userData->gender,
                    'mobile_number'     => (string)$userData->country_code.'-'.$userData->mobile_number,
                    'country_code'      => (string)$userData->country_code,
                ];
                return $this->sendResponse([$userData], 'User Profille Successfully!');
            }else{
                return $this->sendError([], 'Unable to Get User Profile');
            }
        }
        catch (\Exception $e) {
            return $this->sendError([], 'User Profile Fail');
        }
    }

    public function getOffers(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'      => 'required|exists:freshly_users',
            // 'offer_id'     => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getOffers = Offer::getOffersList();
        $user_email = FreshlyAuth::where('user_id',$request->user_id)->value('email');
        if($getOffers){
            $getListWithEmail = [];
            $getList = [];
            $i=0;
            foreach($getOffers as $category){
                if(!empty($category->customer_email)){
                    // echo '<pre>';print_r($category);
                    if($category->customer_email === $user_email){
                        $offerId = Offer::disableOfferButton($request->user_id);
                        $isOffer = "";
                        if($category->id == $offerId){
                            $isOffer = "1";
                        }else {
                            $isOffer = "0";
                        }
                        $getListWithEmail[$i]['id']              = $category->id;
                        $getListWithEmail[$i]['title']           = $category->title;
                        $getListWithEmail[$i]['customer_email']  = (string)$category->customer_email;
                        $getListWithEmail[$i]['description']     = $category->description;
                        $getListWithEmail[$i]['image']           = $category->image;
                        $getListWithEmail[$i]['coupon_code']     = $category->coupon_code;
                        $getListWithEmail[$i]['is_offer']        = $isOffer;
                    }

                }
                if(empty($category->customer_email)){
                    $offerId = Offer::disableOfferButton($request->user_id);
                    $isOffer = "";
                    if($category->id == $offerId){
                        $isOffer = "1";
                    }else {
                        $isOffer = "0";
                    }
                    $getList[$i]['id']              = $category->id;
                    $getList[$i]['title']           = $category->title;
                    $getList[$i]['customer_email']  = (string)$category->customer_email;
                    $getList[$i]['description']     = $category->description;
                    $getList[$i]['image']           = $category->image;
                    $getList[$i]['coupon_code']     = $category->coupon_code;
                    $getList[$i]['is_offer']        = $isOffer;
                }
                $i++;
            }
            $output = array_merge($getListWithEmail, $getList);
            // echo '<pre>';print_r($getList);
            // exit;
            return $this->sendResponse($output, 'Offers List Shown Successfully!');
        }else{
            return $this->sendError([], 'Offers List Empty');
        }
    }

    public function uploadProfilePic(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'  => 'required',
            'image'    => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        if($request->hasfile('image'))
        {
            //Upload Image In DestinationPath
            $file = $request->file('image');
            $filename = uniqid().$file->getClientOriginalName();
            $iparr=  $file->getClientOriginalName();
            $split = explode(".", $iparr);
            $name = $split[0];
            $destinationPath = public_path('/images/users/');
            $file->move($destinationPath, $filename);

            //update image
            $uploadPic = DB::table('freshly_users')->where(['user_id' => $request->user_id])->update(['image' => $filename]);
            if(!empty($uploadPic)){
                $image = array(
                    'image'     => $filename,
                    'filename'  => $name,
                );
                return $this->sendResponse([$image], 'Image uploaded Successfully!');
            }else{
                return $this->sendError([], 'Image upload failed');
            }
        }
        else{
            return $this->sendError([], 'Images upload failed');
        }
    }

    public function freshlyUsers(){
        $users = FreshlyAuth::where(['status'=>1])->latest()->paginate(10);
        $usersArray = FreshlyAuth::where(['status'=>1])->latest()->get()->toArray();
        foreach ($users as $value) {
            $getData = DB::table('user_meal_plans')->where(['user_id'=>$value->user_id])
                    ->orderBy('user_meal_plans.id', 'DESC')->first();
            if($getData){
                $value->add_cutlery = $getData->cutlery;
            }else{
                $value->add_cutlery = "false";
            }
            if($getData){
                $value->book_nutritionist = $getData->book_nutritionist;
            }else{
                $value->book_nutritionist = "false";
            }
        }
        return $this->sendResponse(['usersArray' => $usersArray, 'users'=> $users], 'Freshly Users list');
        // return $this->sendResponse($users, 'Freshly Users list');
    }

    public function freshlyUsersWithoutPaginate(){
        $usersArray = FreshlyAuth::latest()->get()->toArray();
        return $this->sendResponse($usersArray, 'Freshly Users list');
    }

    public function freshlyUserProfile($id){
        $profile = FreshlyAuth::findOrFail($id);
        $mealPlan = DB::table('user_meal_plans')->where(['user_id'=>$id])->orderBy('id','DESC')->first();
        $book = DB::table('user_meal_plans')->where(['user_id'=>$id])->orderBy('id','DESC')->value('book_status');
        if($book){
            if($book == 1){
                $is_done = "true";
            }else{
                $is_done = "false";
            }
        }else{
            $is_done = "false";
        }
        if($mealPlan){
            $book_nutritionist  = $mealPlan->book_nutritionist;
            $cutlery            = $mealPlan->cutlery;
        }else{
            $book_nutritionist  = '';
            $cutlery            = '';
        }
        $profile->book_nutritionist = $book_nutritionist;
        $profile->cutlery = $cutlery;
        $profile->book_status = $is_done;
        // echo '<pre>';print_r($profile);exit;
        return $this->sendResponse($profile, 'User profile successfully');
    }

    public function notificationsReadCount(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'  => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $fArray = [
            'unread_count'  => (string)FreshlyAuth::getUnreadCount($request->user_id),
        ];
        return $this->sendResponse($fArray, 'Read count shown successfully');
    }

    public function freshlyUsersMeals($id){
        $dateCurrent = date('Y-m-d'). " 00:00:00";
        $userMeals = DB::table('users_meals')->where(['user_id'=>$id])->where('users_meals.date','>=',$dateCurrent)->orderBy('date','ASC')->get()->toArray();
        $meals = [];
        $i=0;
        $finalMealList = [];
        foreach ($userMeals as $meal) {
            if($meal->address_id != '' && $meal->address_id !== 'null'){
                $address = Address::where('id',$meal->address_id)->value('current_location');
            }else{
                $address = Address::where('user_id',$meal->user_id)->value('current_location');
            }
            $meal_info = DB::table('meals')->where('id','=',$meal->meal_id)->value('meal_info');
            // echo '<pre>';print_r($meal_info);
            $mealNames = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('food_item_meal.meal_id','=',$meal->meal_id)
                    ->pluck('food_items.title');
            $mealNames = json_decode(json_encode($mealNames), true);
            $finalName = implode(' + ',$mealNames);
            $getMeal = DB::table('meals')->where(['id'=>$meal->meal_id])->first();
            $date = explode(' ',$meal->date);
            $date = $date[0];
            $date = date('d M,Y',strtotime($date));
            $meals[$i]['date']      = $date;
            $meals[$i]['meal_name']      = $finalName;
            $meals[$i]['image']      = $getMeal->image;
            $meals[$i]['quantity']      = $meal->quantity;
            $finalMealList[$date][] = [
                'id'  => $meal->id,
                'meal_name' => $finalName,
                'image'     => $getMeal->image,
                'quantity'  => $meal->quantity,
                'meal_info'  => $meal_info,
                'address'  => $address,
            ];
            $i++;
        }
        // exit;
        return $this->sendResponse($finalMealList, 'Freshly Users Meals list');
    }

    function super_unique($array,$key){
        $temp_array = [];
        foreach ($array as &$v) {
            if (!isset($temp_array[$v[$key]]))
            $temp_array[$v[$key]] =& $v;
        }
        $array = array_values($temp_array);
        return $array;

    }

    public function myActiveMeal($userId){

        $mealPlan =  DB::table('user_meal_plans')
                        ->select('user_meal_plans.*', 'meals.name', 'meals.image')
                        ->join('meals','meals.id','=','user_meal_plans.meal_plan_id')
                        ->where(['user_meal_plans.user_id' => $userId])
                        ->whereDate('user_meal_plans.start_date', '<=', date("Y-m-d"))
                        ->whereDate('user_meal_plans.end_date', '>=', date("Y-m-d"))
                        ->orderBy('user_meal_plans.start_date', 'DESC')
                        ->first();
        // echo '<pre>';print_r($mealPlan);exit;

        if($mealPlan){
            $mealPlan->week  = (int)substr($mealPlan->variation_id, 0, 1);
            $mealPlan->day   = (int)substr($mealPlan->variation_id, 1, 1);
            $mealPlan->meal  = (int)substr($mealPlan->variation_id, 2, 1);
            $mealPlan->snack = (int)substr($mealPlan->variation_id, 3, 1);

            $getPlanName = DB::table('meal_plan_types')->where(['id'=> $mealPlan->meal_plan_id])->value('title');

            $mealPlan->plan_name = $getPlanName;
            $mealPlan->start_date = date('F d,Y', strtotime($mealPlan->start_date));
            $mealPlan->order_date = date('F d,Y', strtotime($mealPlan->created_at));

            return $this->sendResponse($mealPlan, 'Meal Plan successfully');
        }else{
            return response()->json(['status'=>false,'message'=>'Plan not found', 'data'=>[]]);
        }
    }

    public function myPastPlan($userId){
        // echo '<pre>';print_r($allMealComplete);exit;
        $mealPlan = DB::table('user_meal_plans')
                        ->select('user_meal_plans.*', 'meals.image')
                        ->join('meals','meals.id','=','user_meal_plans.meal_plan_id')
                        ->where('user_meal_plans.user_id', '=' , $userId)
                        ->whereDate('user_meal_plans.end_date', '<', date('Y-m-d'))
                        ->where(['user_meal_plans.user_id' => $userId])
                        ->orderBy('user_meal_plans.start_date', 'ASC')
                        ->get()->toArray();
        // echo '<pre>';print_r($mealPlan);exit;
        if($mealPlan){
            foreach ($mealPlan as $key => $value) {
                if($value->cutlery == "false"){
                    $value->cutlery = "No";
                }else{
                    $value->cutlery = "Yes";
                }
                if($value->book_nutritionist == "false"){
                    $value->book_nutritionist = "No";
                }else{
                    $value->book_nutritionist = "Yes";
                }
                $value->week  = (int)substr($value->variation_id, 0, 1);
                $value->day   = (int)substr($value->variation_id, 1, 1);
                $value->meal  = (int)substr($value->variation_id, 2, 1);
                $value->snack = (int)substr($value->variation_id, 3, 1);

                $getPlanName = DB::table('meal_plan_types')->where(['id'=> $value->meal_plan_id])->value('title');
                $value->plan_name = $getPlanName;
                $value->start_date = date('F d,Y', strtotime($value->start_date));
                $value->order_date = date('F d,Y', strtotime($value->created_at));
            }
            return $this->sendResponse($mealPlan, 'Meal Plan successfully');
        }else{
            return response()->json(['status'=>false,'message'=>'Plan not found', 'data'=>[]]);
        }
    }

    public function myUpcomingPlan($userId){
        // echo '<pre>';print_r($allMealComplete);exit;
        $mealPlan = DB::table('user_meal_plans')
                        ->select('user_meal_plans.*', 'meals.image')
                        ->join('meals','meals.id','=','user_meal_plans.meal_plan_id')
                        ->whereDate('user_meal_plans.start_date', '>', date("Y-m-d"))
                        ->where(['user_meal_plans.user_id' => $userId])
                        ->orderBy('user_meal_plans.start_date', 'ASC')
                        ->first();
        // echo '<pre>';print_r($mealPlan);exit;
        if($mealPlan){
            // foreach ($mealPlan as $key => $value) {
                if($mealPlan->cutlery == "false"){
                    $mealPlan->cutlery = "No";
                }else{
                    $mealPlan->cutlery = "Yes";
                }
                if($mealPlan->book_nutritionist == "false"){
                    $mealPlan->book_nutritionist = "No";
                }else{
                    $mealPlan->book_nutritionist = "Yes";
                }
                $mealPlan->week  = (int)substr($mealPlan->variation_id, 0, 1);
                $mealPlan->day   = (int)substr($mealPlan->variation_id, 1, 1);
                $mealPlan->meal  = (int)substr($mealPlan->variation_id, 2, 1);
                $mealPlan->snack = (int)substr($mealPlan->variation_id, 3, 1);

                $getPlanName = DB::table('meal_plan_types')->where(['id'=> $mealPlan->meal_plan_id])->value('title');
                $mealPlan->plan_name = $getPlanName;
                $mealPlan->start_date = date('F d,Y', strtotime($mealPlan->start_date));
                $mealPlan->order_date = date('F d,Y', strtotime($mealPlan->created_at));
            // }
            return $this->sendResponse($mealPlan, 'Meal Plan successfully');
        }else{
            return response()->json(['status'=>false,'message'=>'Plan not found', 'data'=>[]]);
        }
    }

    public function updateMeal(Request $request,$userId){
        // echo '<pre>';print_r($request->all());exit;
        $mealPlan =  DB::table('user_meal_plans')
                    ->select('user_meal_plans.*', 'meals.name', 'meals.image')
                    ->join('meals','meals.id','=','user_meal_plans.meal_plan_id')
                    ->where(['user_meal_plans.user_id' => $userId])
                    ->orderBy('start_date','DESC')
                    ->first();
        $variationId = str_split($mealPlan->variation_id);
        if($request['edit_meal'] < $variationId[2]){
            // return $this->sendError([], 'Selected Meals should not be less than existing!');
            return response()->json(['status'=>false,'message'=>'Selected Meals should not be less than existing!', 'data'=>[]]);
        }
        $updatedVariationId = $variationId[0].$variationId[1].$request['edit_meal'].$variationId[3];
        DB::table('user_meal_plans')->where(['id'=>$mealPlan->id])->update(['variation_id'=>$updatedVariationId]);
    }

    public function updateSnack(Request $request,$userId){
        $mealPlan =  DB::table('user_meal_plans')
                    ->select('user_meal_plans.*', 'meals.name', 'meals.image')
                    ->join('meals','meals.id','=','user_meal_plans.meal_plan_id')
                    ->where(['user_meal_plans.user_id' => $userId])
                    ->orderBy('start_date','DESC')
                    ->first();
        $variationId = str_split($mealPlan->variation_id);
        if($request['edit_snack'] < $variationId[3]){
            // return $this->sendError([], 'Selected Meals should not be less than existing!');
            return response()->json(['status'=>false,'message'=>'Selected Snack should not be less than existing!', 'data'=>[]]);
        }
        $updatedVariationId = $variationId[0].$variationId[1].$variationId[2].$request['edit_snack'];
        DB::table('user_meal_plans')->where(['id'=>$mealPlan->id])->update(['variation_id'=>$updatedVariationId]);
    }

    public function mySubscriptions(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'  => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getActiveSubscriptions     = FreshlyAuth::getActiveSubscriptions($request->user_id);
        $getCompletedSubscriptions  = FreshlyAuth::getCompletedSubscriptions($request->user_id);
        $getUpcomingSubscriptions   = FreshlyAuth::getUpcomingSubscriptions($request->user_id);
        $isAnyPlanUpcoming =  UserMealPlans::isAnyPlanUpcoming($request->user_id);
        // echo '<pre>';print_r($getActiveSubscriptions);exit;
        if($getActiveSubscriptions){
        $mealTitle = DB::table('meal_plan_types')->where(['id'=>$getActiveSubscriptions->meal_plan_id])->first();
        $date = date('d-M-Y', strtotime($getActiveSubscriptions->start_date));
        if($getActiveSubscriptions->pause_start_date){
            $pauseStartDate = date('d-M-Y', strtotime($getActiveSubscriptions->pause_start_date));
        }else{
            $pauseStartDate = "";
        }
        if($getActiveSubscriptions->pause_end_date){
            $pauseEndDate = date('d-M-Y', strtotime($getActiveSubscriptions->pause_end_date));
        }else{
            $pauseEndDate = "";
        }
        $startDate = date('d-M-Y', strtotime($getActiveSubscriptions->start_date));
        $endDate = date('d-M-Y', strtotime($getActiveSubscriptions->end_date));
            $activeSubs[] = [
                'subscription_id'   => (string)$getActiveSubscriptions->id,
                'cart_id'           => (string)$getActiveSubscriptions->cart_id,
                'plan_name'         => (string)$mealTitle->title,
                'plan_image'        => (string)$mealTitle->image,
                'meals'             => (string)$getActiveSubscriptions->meals,
                'snacks'            => (string)$getActiveSubscriptions->snacks,
                'days'              => (string)$getActiveSubscriptions->days,
                'weeks'             => (string)$getActiveSubscriptions->weeks,
                'date'              => (string)$date,
                'price'             => (string)$getActiveSubscriptions->grand_total,
                'is_resubscribe'    => UserMealPlans::isResubscribe($request->user_id),
                'is_paused'         => UserMealPlans::showPlayPause($request->user_id),
                'is_plan_upcoming'  => UserMealPlans::isAnyPlanUpcoming($request->user_id),
                'pause_start_date'  => (string)$pauseStartDate,
                'pause_end_date'    => (string)$pauseEndDate,
                'start_date'        => (string)$startDate,
                'end_date'          => (string)$endDate,
            ];
        }else {
            $activeSubs = [];
        }

        if($getCompletedSubscriptions){
            $completeSubs = [];
            $i=0;
            foreach ($getCompletedSubscriptions as $completed) {
                $mealTitle = DB::table('meal_plan_types')->where(['id'=>$completed->meal_plan_id])->first();
                $date = date('d-M-Y', strtotime($completed->start_date));
                $startDate = date('d-M-Y', strtotime($completed->start_date));
                $endDate = date('d-M-Y', strtotime($completed->end_date));
                $completeSubs[$i]['subscription_id']    = (string)$completed->id;
                $completeSubs[$i]['cart_id']            = (string)$completed->cart_id;
                $completeSubs[$i]['plan_name']          = (string)$mealTitle->title;
                $completeSubs[$i]['plan_image']         = (string)$mealTitle->image;
                $completeSubs[$i]['meals']              = (string)$completed->meals;
                $completeSubs[$i]['snacks']             = (string)$completed->snacks;
                $completeSubs[$i]['days']               = (string)$completed->days;
                $completeSubs[$i]['weeks']              = (string)$completed->weeks;
                $completeSubs[$i]['date']               = (string)$date;
                $completeSubs[$i]['price']              = (string)$completed->grand_total;
                $completeSubs[$i]['start_date']         = (string)$startDate;
                $completeSubs[$i]['end_date']           = (string)$endDate;
                $completeSubs[$i]['is_plan_upcoming']   = UserMealPlans::isAnyPlanUpcoming($request->user_id);
                $i++;
            }
            $uniques = self::super_unique($completeSubs, 'subscription_id');
        }else {
            $uniques = [];
        }

        if($getUpcomingSubscriptions){
            $upcomingSubs = [];
            $i=0;
            foreach ($getUpcomingSubscriptions as $upcoming) {
                $mealTitle = DB::table('meal_plan_types')->where(['id'=>$getUpcomingSubscriptions->meal_plan_id])->first();
                $date = date('d-M-Y', strtotime($getUpcomingSubscriptions->start_date));
                $startDate = date('d-M-Y', strtotime($getUpcomingSubscriptions->start_date));
                $endDate = date('d-M-Y', strtotime($getUpcomingSubscriptions->end_date));
                $upcomingSubs[$i]['subscription_id']    = (string)$getUpcomingSubscriptions->id;
                $upcomingSubs[$i]['cart_id']            = (string)$getUpcomingSubscriptions->cart_id;
                $upcomingSubs[$i]['plan_name']          = (string)$mealTitle->title;
                $upcomingSubs[$i]['plan_image']         = (string)$mealTitle->image;
                $upcomingSubs[$i]['meals']              = (string)$getUpcomingSubscriptions->meals;
                $upcomingSubs[$i]['snacks']             = (string)$getUpcomingSubscriptions->snacks;
                $upcomingSubs[$i]['days']               = (string)$getUpcomingSubscriptions->days;
                $upcomingSubs[$i]['weeks']              = (string)$getUpcomingSubscriptions->weeks;
                $upcomingSubs[$i]['date']               = (string)$date;
                $upcomingSubs[$i]['price']              = (string)$getUpcomingSubscriptions->grand_total;
                $upcomingSubs[$i]['start_date']         = (string)$startDate;
                $upcomingSubs[$i]['end_date']           = (string)$endDate;
            }
        }else {
            $upcomingSubs = [];
        }


        $finalData[] = [
            'unread_count'      => (string)FreshlyAuth::getUnreadCount($request->user_id),
            'running_subscriptions'      =>$activeSubs,
            'completed_subscriptions'   =>$uniques,
            'upcoming_subscriptions'   =>$upcomingSubs,
        ];
        // echo '<pre>';print_r($activeSubs);exit;
        if($getActiveSubscriptions || $getCompletedSubscriptions || $getUpcomingSubscriptions)
        {
            return $this->sendResponse($finalData, 'My subscription successfully');
        }else {
            return $this->sendResponse([], 'Unable to list My subscription!');
        }
    }

    public function resumeSubscription(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
            'subscription_id'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $resumePlan = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id,'id'=>$request->subscription_id])
                                ->update(['pause_start_date'=>'','pause_end_date'=>'']);
        $fetchPlan = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id,'id'=>$request->subscription_id])->orderBy('id','DESC')->first();
        foreach ($fetchPlan as $value) {
            $variation_data = DB::table('plan_variations')->where(['variation_id'=>$fetchPlan->variation_id])->first();
            $planData = DB::table('meal_plan_types')->where(['id'=>$fetchPlan->meal_plan_id])->first();
            $fetchPlan->meal_name = $planData->title;
            $fetchPlan->image = $planData->image;
            $fetchPlan->short_description = $planData->short_description;
            $fetchPlan->long_description = $planData->long_description;
            $fetchPlan->weeks = $variation_data->weeks;
            $fetchPlan->days = $variation_data->days;
            $fetchPlan->meals = $variation_data->meals;
            $fetchPlan->snacks = $variation_data->snacks;
            $fetchPlan->start_date_order = date('d-F-Y',strtotime($fetchPlan->start_date));
        }
        // echo '<pre>';print_r($fetchPlan);exit;
        if($resumePlan)
        {
            //notification
            DB::table('notifications')->insert([
                'user_id'=>$request['user_id'],
                'status'=>1,
                'type'=>3,
                'message'=> "You have resumed your subscription successfully",
                'title'=>'Resume Subscription',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
            //send push notification to message
            $data = array (
                'msg_title' => 'Resume Subscription',
                'msg_body' => "You have resumed your subscription successfully",
                'msg_type' => '3',
            );
            $token = ApiToken::getDeviceToken($request['user_id']);
            $dev_token = array();
            $i=0;
            foreach($token as $devicetoken)
            {
                $dev_token[$i] = $devicetoken['device_token'];
                $i++;
            }
            $send_notification = PushNotification::android($data,$dev_token);
            //send email to user
            $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
            $to_email = $userData->email;
            Mail::to($to_email)->send(new ResumeSubscriptionMail($fetchPlan));
            return $this->sendResponse([], 'Plan resumed successfully');
        }else {
            return $this->sendError([], 'Unable to resume plan');
        }
    }

    public function pauseSubscription(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
            'subscription_id'   => 'required',
            'start_date'        => 'required',
            'end_date'          => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $from = $request->start_date;
        $to = $request->end_date;
        //update meal count
        $countBetweenDates = DB::table('users_meals')->select('date','quantity')->where(['user_id'=>$request->user_id])
                            ->whereBetween('date', [$from, $to])->pluck('quantity')->toArray();
        $sum = array_sum($countBetweenDates);
        $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$request->subscription_id])
                            ->pluck('quantity')->toArray();
        $sumFinal = array_sum($fetchUserMeals);
        $updateMealCount = $sumFinal - $sum;
        DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$request->subscription_id])
                                ->update(['meal_count'=>$updateMealCount]);


        $pausePlan = FreshlyAuth::pauseSubscription($request->all());
        if($pausePlan)
        {
            //notification
            DB::table('notifications')->insert([
                'user_id'=>$request['user_id'],
                'status'=>1,
                'type'=>3,
                'message'=> "You have paused your subscription from ". $request['start_date'] ." to ". $request['end_date'],
                'title'=>'Pause Subscription',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
            //send push notification to message
            $data = array (
                'msg_title' => 'Pause Subscription',
                'msg_body' => "You have paused your subscription from ". $request['start_date'] ." to ". $request['end_date'],
                'msg_type' => '3',
            );
            $token = ApiToken::getDeviceToken($request['user_id']);
            $dev_token = array();
            $i=0;
            foreach($token as $devicetoken)
            {
                $dev_token[$i] = $devicetoken['device_token'];
                $i++;
            }
            $send_notification = PushNotification::android($data,$dev_token);
            DB::table('user_meal_plans')->where(['user_id'=>$request->user_id,'id'=>$request->subscription_id])
                ->update(['pause_start_date'=>$request->start_date,'pause_end_date'=>$request->end_date]);
            // updating remaining meal in user meal plan table
            $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$request->subscription_id])->first();
            $mealCount = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$request->subscription_id])
                            ->value('meal_count');
            $updateCount = $fetchPlan->total_meal - $mealCount;
            $updateCount = DB::table('user_meal_plans')->where(['id'=>$request->subscription_id])
                            ->update(['remaining_meal'=>$updateCount]);
            //send email to user
            $pauseData = [
                'startDate'     => date('d M,Y', strtotime($request->start_date)),
                'endDate'     => date('d M,Y', strtotime($request->end_date)),
            ];
            $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
            $to_email = $userData->email;
            Mail::to($to_email)->send(new PauseSubscriptionMail($pauseData));
            return $this->sendResponse([], 'Plan paused successfully');
        }else {
            return $this->sendError([], 'Unable to pause plan');
        }
    }

    public function resubscribePlan(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
            'subscription_id'   => 'required',
            'start_date'        => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $request->start_date = date('Y-m-d', strtotime($request->start_date));
        $activeSubscription = FreshlyAuth::runningSubscription($request->user_id,$request->start_date);
        // echo '<pre>';print_r($activeSubscription);exit;
        if($activeSubscription == "1"){
            return $this->sendError([], "You already have a running subscription.Can't resubscribe!");
        }
        $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
        if(empty($bagRefund)){
            DB::table('bag_refund')->where(['user_id'=>$request->user_id])->insert(['user_id'=>$request->user_id,'status'=>0]);
        }
        $date = date('Y-m-d', strtotime('+3 days'));
        if($request->start_date < $date)
        {
            return $this->sendError([], 'Please select a date of after 72 hours');
        }
        $getGender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        // $getPlanData = DB::table('user_meal_plans')->where(['user_id'=>$data['user_id'], 'id'=>$data['subscription_id']])->first();
        // $finalDate = date('Y-m-d', strtotime('+8 week', strtotime($getPlanData->start_date)));
        $fetchCartId = DB::table('user_meal_plans')->where(['id'=>$request->subscription_id])->orderBy('id', 'DESC')->first();
        // echo "<pre>";print_r($fetchCartId);exit;;
        $resubscribePlan = FreshlyAuth::resubscribePlan($request->all());
        $getSummary = UserMealPlans::getOrderSummary($request->user_id,$fetchCartId->cart_id,$getGender);
        if(!empty($getSummary)){
            $getList = [];
            foreach ($getSummary as $value) {
                $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
                if($bagRefund){
                    if ($bagRefund->status == 1 || $bagRefund->status == 2) {
                        $bag_deposit = 0;
                    }else if ($bagRefund->status == 0) {
                        $bag_deposit = 100;
                    }
                }else {
                    $bag_deposit = 100;
                }
                $offerData = UserMealPlans::getOfferData($request->user_id,$getSummary['id']);
                $offer_data = "";
                if ($offerData) {
                        $offer_id           = $offerData->offer_id;
                        $coupon_code        = $offerData->coupon_code;
                        $discounted_amount  = $offerData->discount;
                }else {
                    $offer_id           = "";
                    $coupon_code        = "";
                    $discounted_amount  = "";
                }
                $vat = $getSummary['price'] * (0.05);
                $total = $vat + $getSummary['price'] + $bag_deposit;
                $getAddress = DB::table('cart_summary')->where(['user_id'=>$request->user_id,'id'=>$getSummary['id']])->value('order_address');
                if($getAddress){
                    $address = Address::getAddressById($getAddress);
                    $address = $address['house_no']. " " .$address['area']. " " .$address['landmark'];
                }else {
                    $address = Address::getDefaultAddress($request->user_id);
                    $address = $address['house_no']. " " .$address['area']. " " .$address['landmark'];
                }
                if($getAddress){
                    $addressId = $getAddress;
                }else {
                    $addressId = Address::getDefaultAddress($request->user_id);
                    $addressId = $addressId['id'];
                }
                if(!empty($offerData->discount)){
                    $discount = (string)$offerData->discount;
                }else {
                    $discount = "0";
                }
                $grandTotal = $total - $discount;
                $getList['cart_id']             = (string)$getSummary['id'];
                $getList['user_id']             = (string)$getSummary['user_id'];
                $getList['categoryName']        = (string)$getSummary['title'];
                $getList['image']               = (string)$getSummary['image'];
                $getList['meal_plan_id']        = (string)$getSummary['meal_plan_id'];
                $getList['weeks']               = (string)$getSummary['weeks']. " Weeks";
                $getList['days']                = (string)$getSummary['days']. " Days";
                $getList['main_meal']           = (string)$getSummary['meals']. " Meal";
                $getList['snack']               = (string)$getSummary['snacks']. " Snack";
                $getList['start_date']          = (string)$getSummary['start_date'];
                $getList['order_amount']        = (string)$getSummary['price'];
                $getList['vat']                 = (string)$vat;
                $getList['bag_deposit']         = (string)$bag_deposit;
                $getList['discount']            = (string)(-($discount));
                $getList['grand_total']         = (string)$grandTotal;
                $getList['name']                = (string)$getSummary['fname']." ";
                $getList['email']               = (string)$getSummary['email'];
                $getList['order_number']        = (string)$getSummary['order_number'];
                $getList['date']                = $getSummary['created_at'];
                $getList['address']             = (string)$address;
                $getList['address_id']          = (string)$addressId;
                $getList['mobile_number']       = (string)$getSummary['mobile_number'];
                $getList['country_code']        = (string)$getSummary['country_code'];
                $getList['offer_id']            = $offer_id;
                $getList['coupon_code']         = $coupon_code;
                $getList['discounted_amount']   = $discounted_amount;
            }
            $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
            if(empty($bagRefund)){
                DB::table('bag_refund')->where(['user_id'=>$request->user_id])->insert(['user_id'=>$request->user_id,'status'=>0]);
            }
            return $this->sendResponse($getList, 'Meal Plan added to cart successfully');
        }else {
            return $this->sendError([], 'Unable to add plan to cart!');
        }
    }

    public function transactionHistory(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $history = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id,'status'=>'1'])->get()->toArray();
        // echo "<pre>";print_r($history);exit;;
        $transactionHistory = [];
        $i=0;
        foreach ($history as $value) {
            $categoryName = DB::table('meal_plan_types')->where(['id'=>$value->meal_plan_id])->value('title');
            $date = date('d-M',strtotime($value->start_date));
            $transactionHistory[$i]['unread_count']     = (string)FreshlyAuth::getUnreadCount($request->user_id);
            $transactionHistory[$i]['subscription_id']  = (string)$value->id;
            $transactionHistory[$i]['user_id']          = (string)$value->user_id;
            $transactionHistory[$i]['categoryName']     = (string)$categoryName;
            $transactionHistory[$i]['price']            = (string)number_format((float)$value->grand_total, 2, '.', '');
            $transactionHistory[$i]['message']          = "You paid money to Freshly Meals for ". $categoryName;
            $transactionHistory[$i]['date']             = $date;
            $i++;
        }
        if($transactionHistory)
        {
            return $this->sendResponse($transactionHistory, 'Transaction history shown successfully');
        }else {
            return $this->sendError([], 'Unable to show transaction history!');
        }
    }

    public function notificationsList(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $updateReadCount = DB::table('notifications')->where(['user_id'=>$request->user_id])->update(['status'=>'0']);
        $notifications = DB::table('notifications')->where(['user_id'=>$request->user_id])->orderBy('created_at','DESC')->get()->toArray();
            $notificationsList = [];
        $i=0;
        foreach ($notifications as $value) {
            $date = date('d-M-Y', strtotime($value->created_at));
            $time = date('h:i A', strtotime($value->created_at));
            $notificationsList[] = [
                    'id'            =>  (string)$value->id,
                    'title'         =>  $value->title,
                    'message'       =>  $value->message,
                    'date'          =>  $date,
                    'time'          =>  $time,
            ];
        }
        if($notificationsList)
        {
            return $this->sendResponse($notificationsList, 'Notifications List shown successfully');
        }else {
            return $this->sendResponse([], 'Unable to show Notifications List!');
        }
    }

    public function pastOrders(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $orders = DB::table('users_meals')->where(['user_id'=>$request->user_id])
                                          ->whereDate('users_meals.date', '<=', date('Y-m-d'))
                                          ->get()->toArray();
        // echo '<pre>';print_r($orders);exit;
        $ordersList = [];
        $i=0;
        foreach ($orders as $value) {
            $mealData = DB::table('meals')->where(['id'=>$value->meal_id])->first();
            $planData = DB::table('meal_plan_types')->where(['id'=>$mealData->meal_plan_id])->first();
            $mealNames = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('food_item_meal.meal_id','=',$value->meal_id)
                    ->pluck('food_items.title');
            $mealNames = json_decode(json_encode($mealNames), true);
            $finalName = implode(' + ',$mealNames);
            $planId = $value->plan_id;
            $subsData = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
            $addressId = $subsData->order_address;
            $location = DB::table('user_address')->where(['id'=>$addressId])->value('select_location');
            $date = date('M d,Y',strtotime($value->date));
            $ordersList[$i]['id']           = (string)$value->id;
            $ordersList[$i]['plan_name']    = (string)$planData->title;
            $ordersList[$i]['location']     = (string)$location;
            $ordersList[$i]['price']        = (string)$subsData->grand_total;
            $ordersList[$i]['meal_name']    = (string)$finalName;
            $ordersList[$i]['date']         = (string)$date;
            $ordersList[$i]['id']           = (string)$value->id;
            $i++;
        }
        if($orders)
        {
            return $this->sendResponse($ordersList, 'Notifications List shown successfully');
        }else {
            return $this->sendError([], 'Unable to show Notifications List!');
        }
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
        'path' => Paginator::resolveCurrentPath(),
        'pageName' => 'page',
        ]);
    }
    public function adminNotifications(Request $request)
    {
        $notifications = DB::table('notifications')->orderBy('created_at','DESC')->get()->toArray();
        // echo '<pre>';print_r($notifications);exit;
        $notificationsList = [];
        $i=0;
        foreach ($notifications as $value) {
            $user = DB::table('freshly_users')->where(['user_id'=>$value->user_id])->first();
            if($user){
                $date = date('d-M,Y', strtotime($value->created_at));
                $time = date('h:i A', strtotime($value->created_at));
                $notificationsList[] = [
                        'user_id'       =>  $value->user_id,
                        'name'          =>  $user->fname.' '.$user->lname,
                        'title'         =>  $value->title,
                        'message'       =>  $value->message,
                        'date'          =>  $date,
                        'time'          =>  $time,
                ];
            }

        }
        $notificationsFinal = $this->paginate($notificationsList);
        if($notificationsFinal)
        {
            return $this->sendResponse($notificationsFinal, 'Notifications List shown successfully');
        }else {
            return $this->sendError([], 'Unable to show Notifications List!');
        }
    }

    public function getCustomMeal(Request $request){
        $validatedData = Validator::make($request->all(), [
            'fname'           => 'required',
            'mobile_number'           => 'required',
            'email'           => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $data = [];
        return $this->sendResponse($data, 'Email sent successfully');
    }

    public function getFaqsNew(Request $request){
        $allFaqs = DB::table('faqs')->get()->toArray();
        $finalFaq = [];

        foreach ($allFaqs as $value) {
            $queData = DB::table('faqs')->where(['category'=>$value->category])->get()->toArray();
            $questionData = [];
            $i=0;
            foreach ($queData as $que) {
                $questionData[$i]['question'] = (string)$que->subject;
                $questionData[$i]['answer']   = (string)$que->message;
                $questionData[$i]['type']     = (string)$que->type;
                $questionData[$i]['images']   = self::getAllergens($value->category,$que);
                $i++;
            }
            if($value->category == "subscriptions"){
                $value->category = "Subscriptions";
            }
            if($value->category == "pricing"){
                $value->category = "Pricing, Payments & Refunds";
            }
            if($value->category == "food_allergies"){
                $value->category = "Food Allergies & Preferences";
            }
            if($value->category == "weight_manage"){
                $value->category = "Weight Management Claims";
            }
            if($value->category == "promotions"){
                $value->category = "Promotions & Deals";
            }
            $finalFaq[] = [
                'unread_count'      => (string)FreshlyAuth::getUnreadCount($request->user_id),
                'name'              => $value->category,
                'questtion_data'    => $questionData
            ];
        }
        $faqs = self::super_unique($finalFaq, 'name');
        // echo '<pre>';print_r($faqs);
        // exit;
        return $this->sendResponse($faqs, 'Faqs successfully');
    }

    public static function getAllergens($category,$data){
        $allergens = DB::table('allergens_faqs')->get()->toArray();
        $allergens_array = [];
        $without_allergens_array = [];
        foreach ($allergens as $value1) {
            $allergens_array[] = (string)$value1->image;
        }
        if($category === 'food_allergies' && $data->type === 'allergen_images'){
            return $data->images = $allergens_array;
            // echo '<pre>';print_r($data);
        }else{
            return $data->images = $without_allergens_array;
        }
    }

    public function deleteUser($userId){
        $email = Str::random(10).'@gmail.com';
        $delete_user = DB::table('freshly_users')->where(['user_id'=>$userId])->update(['status'=>0,'email'=>$email]);
        return $this->sendResponse([$delete_user], 'User has been Deleted');
    }

    public function getFaqsNewTest(Request $request){
        $allFaqs = [
            [
                'name' => 'Subscriptions',
                'questtion_data' => [
                    [
                        'question'  => 'How do I place an order for my meal plan?',
                        'answer'    => 'You can place your meal plan order via our app. You are responsible for checking and confirming your order details (i.e., meal plan selected, food selections based on your taste and preferences, allergies, delivery dates,timings, delivery locations etc.)',
                        'type'  => 'none',
                    ],
                    [

                        'question'  => 'What subscriptions can I avail?',
                        'answer'    => 'You can place your meal plan order via our app. You are responsible for checking and confirming your order details (i.e., meal plan selected, food selections based on your taste and preferences, allergies, delivery dates, timings, delivery locations etc.).. ',
                        'type'  => 'chat',
                    ],
                ],
            ],
            [
                'name' => 'Pricing, Payments & Refunds',
                'questtion_data' => [
                    [
                    'question'  => 'What’s the cost of the plan?',
                    'answer'    => 'Pricing is based on the plan you select, the number of meals you opt for, and the number of deliveries you require each week. Freshly Meals app will give you a complete order summary along with 5% VAT, delivery charges, discount offers and any other charges as applicable. All Our subscriptions (whether daily, weekly, or monthly) incur advance payments. ',
                    'type'  => 'email',
                    ],
                    [

                        'question'  => 'How do I pay?',
                        'answer'    => 'We accept online payments via credit / debit card. Payment must be made in full, 48 working hours before the chosen delivery date. Deliveries will not be processed unless the payment is received in full. In case a payment is declined either by your bank, credit/debit card or other payment modes, we won’t be able to dispatch the delivery. ',
                        'type'  => 'none',
                    ],
                ],
            ],
        ];
        return $this->sendResponse($allFaqs, 'Faqs successfully');
    }

    public function changeDefaultAddress(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
            'address_id'    => 'required',
            'is_edit_meal'  => '',
            'date'          => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        // $request->date = "2022-07025 00:00:00.000";
        $request->date = date('Y-m-d',strtotime($request->date));
        $request->date = $request->date." 00:00:00";
        // echo '<pre>';print_r($request->date);exit;

        if(isset($request->is_edit_meal) && isset($request->date) && $request->is_edit_meal == "1"){
            $updateMealAddress = DB::table('users_meals')->where(['user_id'=>$request->user_id])->whereDate('date','=',$request->date)
                                    ->update(['address_id'=>$request->address_id]);
            if($updateMealAddress){
                return $this->sendResponse([], 'Meal Address Updated successfully');
            }else{
                return $this->sendError([], 'Meal Address Not updated');
            }
            // echo '<pre>';print_r($updateMealAddress);exit;
        }else{
            $updateDefaultAllZero = Address::where('user_id',$request->user_id)->update(['default_address'=>0]);
            $updateAddressDefault = Address::where(['id'=>$request->address_id,'user_id'=>$request->user_id])->update(['default_address'=>1]);
            if($updateAddressDefault){
                return $this->sendResponse([], 'Updated successfully');
            }else{
                return $this->sendError([], 'Not updated');
            }
        }

    }

    public function getSelectedAddressId(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getDefaultAddress = (string)Address::where(['user_id'=>$request->user_id,'default_address'=>1])->value('id');
        if($getDefaultAddress){
            return $this->sendResponse($getDefaultAddress, 'Address Id');
        }else{
            return $this->sendError([], 'Error');
        }
    }

    public function deleteNotification(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'           => 'required',
            'notification_id'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error = array();
            $validation_error['errors'] = implode('|| ', $validatedData->messages()->all());
            return response()->json(['data' => $validation_error]);
        }
        $deleteNotification = DB::table('notifications')->where(['user_id'=>$request->user_id, 'id'=>$request->notification_id])->delete();
        if($deleteNotification)
        {
            return response(['status' => 'success', 'message' => 'Notification Deleted Successfully.', 'data' => []]);
        }else{
            return response()->json(['message'=>'Unable Delete Notification','status'=>'fail','data'=>[]]);
        }
    }

    public static function updateAdminUser(Request $request,$userId){
        // echo '<pre>';print_r($userId);exit;
        foreach ($request->allergies as $key => $value) {
            $allergens[] = $value['name'];
        }
        $updateUserData = [
            'mobile_number'     => $request->mobile_number,
            'gender'            => $request->gender,
            'dob'               => $request->dob,
            'height'            => $request->height,
            'weight'            => $request->weight,
            'blood_group'       => $request->blood_group,
            'med_conditions'    => $request->med_conditions,
            'cardio'            => $request->cardio,
            'allergies'         => implode(',',$allergens),
        ];
        $user = DB::table('freshly_users')->where('user_id',$userId)->update($updateUserData);
        $plan = DB::table('user_meal_plans')->where('user_id',$userId)->orderBy('id','DESC')->update(['book_nutritionist'=>$request->book_nutritionist,'cutlery'=>$request->cutlery]);
        if($user || $plan){
            return response(['status' => 'success', 'message' => 'User Updated Successfully.', 'data' => []]);
        }else{
            return response()->json(['message'=>'Unable Update User','status'=>'fail','data'=>[]]);
        }
    }

    public function callback(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'               => 'required',
            'cart_id'               => 'required',
            'transaction_reference' => 'required',
            'is_pending'            => 'required',
            'is_on_hold'            => 'required',
            'cart_amount'           => 'required',
            'card_scheme'           => 'required',
            'card_type'             => 'required',
            'payment_description'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error = array();
            $validation_error['errors'] = implode('|| ', $validatedData->messages()->all());
            return response()->json(['data' => $validation_error]);
        }
        $data = [
            'user_id'               => $request->user_id,
            'cart_id'               => $request->cart_id,
            'transaction_reference' => $request->transaction_reference,
            'is_pending'            => $request->is_pending,
            'is_on_hold'            => $request->is_on_hold,
            'cart_amount'           => $request->cart_amount,
            'card_scheme'           => $request->card_scheme,
            'card_type'             => $request->card_type,
            'payment_description'   => $request->payment_description,
            'created_at'            => date('Y-m-d h:i:s'),
            'updated_at'            => date('Y-m-d h:i:s'),
        ];
        $insert = PaymentDetails::insert($data);
        if($insert){
            return response(['status' => 'success', 'message' => 'Callback Successful.', 'data' => []]);
        }else{
            return response(['status' => 'fail', 'message' => 'Callback Not Successful.', 'data' => []]);
        }
        // echo '<pre>';print_r($request->all());exit;

    }

    public static function addNotes($userId,$requestNotes){
        // echo '<pre>';print_r($userId);exit;
        if($requestNotes){
            if($userId){
                $notes = FreshlyAuth::where(['user_id'=>$userId])->update(['notes'=>$requestNotes]);
                if($notes){
                    return response(['status' => 'success', 'message' => 'Notes Added.', 'data' => []]);
                }else{
                    return response(['status' => 'fail', 'message' => 'Notes Not Added.', 'data' => []]);
                }
            }else{
                return response(['status' => 'fail', 'message' => 'Failed', 'data' => []]);
            }
        }else{
            return response(['status' => 'fail', 'message' => 'Please enter some notes before submitting!!', 'data' => []]);
        }

    }

    public static function getNotes($userId){
        // echo '<pre>';print_r($userId);exit;
        if($userId){
            $notes = FreshlyAuth::where(['user_id'=>$userId])->value('notes');
            if($notes){
                return response(['status' => 'success', 'message' => 'Notes Got.', 'data' => [$notes]]);
            }else{
                return response(['status' => 'fail', 'message' => 'Notes Empty.', 'data' => []]);
            }
        }else{
            return response(['status' => 'fail', 'message' => 'Failed', 'data' => []]);
        }
    }
}
