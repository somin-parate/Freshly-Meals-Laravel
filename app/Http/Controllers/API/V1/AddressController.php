<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Timeslot;
use App\Models\City;
use App\Models\Offer;
use App\Models\UserMealPlans;
use Illuminate\Support\Facades\Mail;
use App\Models\FreshlyAuth;
use App\Models\ApiToken;
use App\Models\PushNotification;
use App\Mail\BagRefund;
use Illuminate\Http\Request;
use Validator;
use Hash;
use URL;
use DB;
use Log;
use Password;

class AddressController extends BaseController
{
    public function loadAddress($userId){
        $address = Address::select('id', 'current_location')->where('user_id',$userId)->get();
        return $this->sendResponse($address, 'address list');
    }

    public function saveUserAddress(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'               => 'required',
            'current_location'      => 'required',
            'house_no'              => 'required',
            'area'                  => 'required',
            'landmark'              => '',
            'location_tag'          => 'required',
            'select_city'           => 'required',
            'timeslot_by_emirate'   => '',
            'lat'                   => 'required',
            'long'                  => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        if($request->timeslot_by_emirate == ""){
            return $this->sendError([], 'Please select Time Slot!');
        }

        $user_address = Address::addAdress($request->all());
        if(!empty($user_address))
        {
            $user_address = [
                'user_id'               => (string)$user_address->user_id,
                'current_location'      => (string)$user_address->current_location,
                'house_no'              => (string)$user_address->house_no,
                'area'                  => (string)$user_address->area,
                'landmark'              => (string)$user_address->landmark,
                'location_tag'          => (string)$user_address->location_tag,
                'select_location'       => (string)$request->select_city,
                'timeslot_by_emirate'   => (string)$user_address->timeslot_by_emirate,
                'lat'                   => (string)$user_address->lat,
                'long'                  => (string)$user_address->long,
                'id'                    => (string)$user_address->id,
                'time_slot_id'          => (string)$user_address->time_slot_id,
                'city_id'               => (string)$user_address->city_id,
                'default_address'       => '1',
                'address_status'        => '1',
            ];

            //address insert in cart table
            // DB::table('cart_summary')->(['user_id'=>$user_address->user_id])->insert(['address'=>$user_address->id]);
            return $this->sendResponse($user_address, 'Address Saved Successfully');
        }else {
            return $this->sendError([], 'Registered Fail');
        }
    }

    public function editAddress(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'address_id'            => 'required',
            'user_id'               => 'required',
            'current_location'      => 'required',
            'house_no'              => 'required',
            'area'                  => 'required',
            'landmark'              => '',
            'location_tag'          => 'required',
            'select_city'           => 'required',
            'timeslot_by_emirate'   => '',
            'lat'                   => 'required',
            'long'                  => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        if($request->timeslot_by_emirate == ""){
            return $this->sendError([], 'Please select Time Slot!');
        }

        $user_address = Address::editAddress($request->all());
        if(!empty($user_address))
        {
            $user_address = [
                'user_id'               => (string)$user_address->user_id,
                'current_location'      => (string)$user_address->current_location,
                'house_no'              => (string)$user_address->house_no,
                'area'                  => (string)$user_address->area,
                'landmark'              => (string)$user_address->landmark,
                'location_tag'          => (string)$user_address->location_tag,
                'select_location'       => (string)$user_address->select_location,
                'timeslot_by_emirate'   => (string)$request->timeslot_by_emirate,
                'lat'                   => (string)$user_address->lat,
                'long'                  => (string)$user_address->long,
                'id'                    => (string)$user_address->id,
                'time_slot_id'          => (string)$user_address->time_slot_id,
                'city_id'               => (string)$user_address->city_id,
            ];
            return $this->sendResponse($user_address, 'Address Updated Successfully');
        }else {
            return $this->sendError([], 'Unable to update address');
        }
    }

    public function removeAddress(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'address_id'            => 'required',
            'user_id'               => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $date = date('Y-m-d').' 00:00:00';
        $myMeals = DB::table('users_meals')->where(['user_id'=>$request->user_id])
                    ->where('date','>=',$date)
                    ->orderBy('date','ASC')->get()->toArray();
        foreach ($myMeals as $value) {
            // echo '<pre>';print_r($value);
            if(isset($value->address_id) && $value->address_id === $request->address_id){
                return $this->sendError([], "You cannot delete the selected address as you have this address in upcoming meal");
            }
        }
        // exit;
        $address = Address::findOrFail($request->address_id);
        $firstAddress = Address::where(['user_id'=>$request->user_id])
                        ->where('id','!=',$request->address_id)->first();
        if($address){
            Address::where(['id'=>$firstAddress->id])->update(['default_address'=>1]);
            Address::where(['id'=>$request->address_id])->update(['address_status'=>0]);
            // $address->update(['address_status'=>0]);
            return $this->sendResponse([], 'Address deleted successfully !');
        }else{
            return $this->sendError([], 'Unable to delete address');
        }
    }

    public function changeAddress(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'address_id'    => 'required',
            'cart_id'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $change = DB::table('cart_summary')->where(['id'=>$request->cart_id,'order_address'=>$request->address_id])
                    ->update(['order_address'=>$request->address_id]);
        if($change){
            return $this->sendResponse([], 'Address changed successfully !');
        }else{
            return $this->sendError([], 'Unable to changed address');
        }
    }

    public function getCities(Request $request)
    {
        try{
            $cities = DB::table('cities')->get();
            if($cities)
            {
                $getList = [];
                $i=0;
                foreach($cities as $category)
                {
                    $getList[$i]['id']              = $category->id;
                    $getList[$i]['city']            = $category->city;
                    $i++;
                }
                return $this->sendResponse($getList, 'Cities List Shown Successfully!');
            }else{
                return $this->sendError([], 'Cities List Empty');
            }
        }
        catch (\Exception $e) {
                return $this->sendError([], 'Unable To Fetch Cities List');
        }
    }

    public function getTimeSlots(Request $request, $result = [])
    {
        $validatedData = Validator::make($request->all(), [
            'city_id' => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $timeslots = City::with('timeslots')->where('id', '=', $request->city_id)->first()->timeslots;
        if(!empty($timeslots)){
            $i=0;
            foreach ($timeslots as $timeslot) {
                $result [] = [
                    'id'            => (string)$i,
                    'city_id'       => $request->city_id,
                    'timeslot_id'   => (string)$timeslot->id,
                    'start_time'    => $timeslot->start_time,
                    'end_time'      => $timeslot->end_time,
                ];
                $i++;
            }
            $result = json_decode(json_encode($result), true);
            array_multisort(array_column($result, 'timeslot_id'), SORT_ASC, $result);

            return $this->sendResponse($result, 'Time Slots Shown Successfully!');
        }
    }

    public function bagRefund(Request $request, $result = [])
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
            // 'full_name'     => 'required',
            'bank_name'     => 'required',
            'account_name'   => 'required',
            // 'bank_address'  => 'required',
            'account_no'    => 'required',
            'iban_no'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $alreadyAsked = DB::table('bag_refund')->where(['user_id'=>$request->user_id,'status'=>1])->first();
        if($alreadyAsked){
            return $this->sendError([], 'Your refund details have been initiated. Kindly get in touch via our chat option for any queries. Thank you.');
        }
        $getPlanData = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id])->first();
        $refundData = [
            'user_id'       => $request->user_id,
            'status'        => 1,
            // 'full_name'     => $request->full_name,
            'bank_name'     => $request->bank_name,
            'account_name'   => $request->account_name,
            // 'bank_address'  => $request->bank_address,
            'account_no'    => $request->account_no,
            'iban_no'       => $request->iban_no,
            'created_at'    => date('Y-m-d')
        ];
        if($getPlanData){
            $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
            if($bagRefund){
                $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->update($refundData);
            }else{
                $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->insert($refundData);
            }
        }else {
            return $this->sendError([], 'You first need to buy plan!');
        }
        if($bagRefund){
            $user = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
            //email
            $to_email = $user->email;
            Mail::to($to_email)->send(new BagRefund($user));
            //notification
            DB::table('notifications')->insert([
                'user_id'=>$request['user_id'],
                'status'=>1,
                'type'=>8,
                'message'=> "Your refund details have been initiated. Kindly get in touch via our chat option for any queries. Thank you.",
                'title'=>'Bank Payment Approval',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
            //send push notification to message
            $data = array (
                'msg_title' => 'Bank Payment Approval',
                'msg_body' => "Your refund details have been initiated. Kindly get in touch via our chat option for any queries. Thank you.",
                'msg_type' => '8',
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
            return $this->sendResponse([], 'Your refund details have been initiated. Kindly get in touch via our chat option for any queries. Thank you.');
        }
    }

    public function myAddress(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $myAddresses = Address::mySavedAddresses($request->user_id);
        if($myAddresses){
            $myAddress = [];
            $i=0;
            foreach ($myAddresses as $value) {
                if($value['default_address'] == "1"){
                    $default = "1";
                }else{
                    $default = "0";
                }
                $myAddress[$i]['unread_count']          = (string)FreshlyAuth::getUnreadCount($request->user_id);
                $myAddress[$i]['address_id']            = (string)$value['id'];
                $myAddress[$i]['user_id']               = (string)$value['user_id'];
                $myAddress[$i]['current_location']      = (string)$value['current_location'];
                $myAddress[$i]['location_tag']          = (string)$value['location_tag'];
                $myAddress[$i]['select_location']       = (string)$value['select_location'];
                $myAddress[$i]['timeslot_by_emirate']   = (string)$value['timeslot_by_emirate'];
                $myAddress[$i]['lat']                   = (string)$value['lat'];
                $myAddress[$i]['long']                  = (string)$value['long'];
                $myAddress[$i]['house_no']              = (string)$value['house_no'];
                $myAddress[$i]['area']                  = (string)$value['area'];
                $myAddress[$i]['landmark']              = (string)$value['landmark'];
                $myAddress[$i]['time_slot_id']          = (string)$value['time_slot_id'];
                $myAddress[$i]['city_id']               = (string)$value['city_id'];
                $myAddress[$i]['is_default']            = $default;
                $i++;
            }
            return $this->sendResponse($myAddress, 'My saved addresses shown succesfully!');
        }else {
            return $this->sendError([], 'Unable To Fetch saved addresses!');
        }
    }

    public function getAddressById(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required',
            'address_id' => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getAddress = Address::getParticularAddress($request->user_id,$request->address_id);
        // echo '<pre>';print_r($getAddress);exit;
        if($getAddress){
            $myAddress = [];
            $i=0;
            foreach ($getAddress as $value) {
                $city = DB::table('cities')->where(['id'=>$value['select_location']])->value('city');
                $myAddress[$i]['unread_count']          = (string)FreshlyAuth::getUnreadCount($request->user_id);
                $myAddress[$i]['address_id']            = (string)$value['id'];
                $myAddress[$i]['user_id']               = (string)$value['user_id'];
                $myAddress[$i]['current_location']      = (string)$value['current_location'];
                $myAddress[$i]['location_tag']          = (string)$value['location_tag'];
                $myAddress[$i]['select_location']       = (string)$value['select_location'];
                $myAddress[$i]['timeslot_by_emirate']   = (string)$value['timeslot_by_emirate'];
                $myAddress[$i]['lat']                   = (string)$value['lat'];
                $myAddress[$i]['long']                  = (string)$value['long'];
                $myAddress[$i]['house_no']              = (string)$value['house_no'];
                $myAddress[$i]['area']                  = (string)$value['area'];
                $myAddress[$i]['landmark']              = (string)$value['landmark'];
                $myAddress[$i]['time_slot_id']          = (string)$value['time_slot_id'];
                $myAddress[$i]['city_id']               = (string)$value['city_id'];
            }
            return $this->sendResponse($myAddress, 'Address shown succesfully!');
        }else {
            return $this->sendError([], 'Unable To Fetch saved addresses!');
        }
    }

    public function enterCouponCode(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
            'cart_id'       => 'required',
            'coupon_code'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getOfferId = Offer::getIdByCode($request->coupon_code);
        $getGender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $getCoupon = Offer::getCoupon($getOfferId);
        // echo '<pre>';print_r($getCoupon);exit;
        $getCartData = UserMealPlans::getOrderSummary($request->user_id,$request->cart_id,$getGender);
        // echo '<pre>';print_r($getCartData);exit;
        foreach ($getCartData as $value) {
            if($request->coupon_code != $getCoupon['coupon_code']){
                return $this->sendError([], 'Sorry the coupon code which you entered is invalid!');
            }
            $vat = $getCartData['price'] * (0.05);
            $total = $vat + $getCartData['price'] + 100;
            $type = Offer::getTypeById($getOfferId);
            $amount = Offer::getAmountById($getOfferId);
            $offerExpired = Offer::nb_mois($getOfferId);
            //offer expired check
            if($offerExpired == "1"){
                if($type == "1"){
                    $discount = ($total*$amount)/100;
                }else if($type == "2"){
                    $discount = $amount;
                }
            }else if($offerExpired == "0"){
                return $this->sendError([], 'Sorry the offer which you are looking for is Expired!');
            }
            //offer already used check
            $offerUsed = Offer::isOfferUsed($request->user_id,$getOfferId,$getCartData['meal_plan_id'],$request->cart_id);
            if ($offerUsed == "1") {
                return $this->sendError([], 'Sorry you cannot use this offer again!');
            }
            $getList['offer_id']            = (string)$getCoupon->id;
            $getList['coupon_code']         = (string)$getCoupon->coupon_code;
            $getList['amount']              = (string)$getCoupon->amount;
            $getList['discount']            = "-AED ".$discount;
        }
        //change offer
        $changeOffer = DB::table('apply_coupon')->where(['user_id'=>$request->user_id,'offer_id'=>$getOfferId])->first();
        // echo '<pre>';print_r($changeOffer);exit;
        $offerData = [
            'user_id'       => $request->user_id,
            'cart_id'       => $request->cart_id,
            'offer_id'      => $getCoupon->id,
            'coupon_code'   => $getCoupon->coupon_code,
            'discount'      => $getCoupon->amount,
            'status'        => 0
        ];
        $insert = DB::table('apply_coupon')->insert($offerData);
        if($getCartData)
        {
            return $this->sendResponse([$getList], 'Coupon Applied Successfully');
        }else {
            return $this->sendError([], 'Unable to apply coupon!');
        }
    }

    function getAddressForAdmin($userId){
        return Address::where('user_id',$userId)->get()->toArray();
    }

    function albums(Request $request,$userId){
        echo '<pre>';print_r($request->all());exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $url = 'https://jsonplaceholder.typicode.com/albums';
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type:application/json'));
        $server_output = curl_exec($ch);
        $customer_Data1 = json_decode($server_output,true);
    }
}