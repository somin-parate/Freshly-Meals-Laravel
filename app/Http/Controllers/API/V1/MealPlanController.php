<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\UserMealPlans;
use App\Models\ApiToken;
use App\Models\PushNotification;
use App\Models\FreshlyAuth;
use App\Models\Offer;
use App\Models\Address;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlanSubscriptionMail;
use App\Mail\SelectedMealsMail;
use App\Mail\EditedMealsMail;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Mail\DeleteMealMail;
use Validator;
use Hash;
use URL;
use stdClass;
use DB;
use Log;
use Password;
use Carbon\Carbon;

class MealPlanController extends BaseController
{
    public function getMealCategory(Request $request){
        $meal_category = Meal::getMealPlans();
        $getGender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $userPlanData = UserMealPlans::where('user_id',$request->user_id)->orderBy('id','DESC')->first();
        if($userPlanData){
            $endDate = date('d-m-Y',strtotime($userPlanData->end_date));
            if($userPlanData->remaining_meal == "0"){
                $allMealCompleted = "complete";
            }else{
                $allMealCompleted = "not_complete";
            }
        }else{
            $allMealCompleted = "";
            $endDate = "";
        }
        if(!empty($meal_category)){
            $getList = [];
            $i=0;
            foreach($meal_category as $category){
                $where = [
                    'plan_id' =>$category->id,
                    'weeks'=>1,
                    'days'=>3,
                    'meals'=>1,
                    'snacks'=>0,
                    'gender'=>$getGender
                ];
                $price = DB::table('plan_variations')->select('price')->where($where)->value('price');
                if(!$price){
                    $price = "0";
                }
                $getList[$i]['meal_plan_id']            = (string)$category->id;
                $getList[$i]['category']                = $category->title;
                $getList[$i]['sub_title']                = $category->sub_title;
                $getList[$i]['image']                   = $category->image;
                $getList[$i]['cover_image']             = $category->cover_image;
                $getList[$i]['is_customized']           = $category->is_customized;
                // $getList[$i]['short_description']       = $category->short_description;
                // $getList[$i]['long_description']        = $category->long_description;
                $getList[$i]['price']                   = (string)$price;
                $getList[$i]['subscription_expired']    = Offer::subscriptionExpired($request->user_id);
                $getList[$i]['end_date']                = $endDate;
                $getList[$i]['all_meals_completed']     = $allMealCompleted;
                $i++;
            }
            return $this->sendResponse($getList, 'Meal Plans Shown Successfully!');
        }else{
            return $this->sendError([], 'Unable to Show Meal Plans');
        }
    }

    public function getMealCategoryData(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'               => 'required|exists:freshly_users',
            'meal_plan_id'          => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getMealCategory = Meal::getMealPlanById($request->meal_plan_id);
        // echo '<pre>';print_r($getMealCategory);exit;
        $getGender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $getMealCategories = [];
        $i=0;
        foreach ($getMealCategory as $category) {
            $userPlanData = UserMealPlans::where('user_id',$request->user_id)->orderBy('id','DESC')->first();
            if($userPlanData){
                $endDate = date('d-m-Y',strtotime($userPlanData->end_date));
                if($userPlanData->remaining_meal == "0"){
                    $allMealCompleted = "complete";
                }else{
                    $allMealCompleted = "not_complete";
                }
            }else{
                $allMealCompleted = "";
                $endDate = "";
            }
            $coverImage = DB::table('plan_images')->where(['plan_id'=>$category->id])->pluck('image');
            $finalImage = json_decode($coverImage,true);
            $where = [
                'plan_id' =>$category->id,
                'weeks'=>1,
                'days'=>3,
                'meals'=>1,
                'snacks'=>0,
                'gender'=>$getGender
            ];
            $price = DB::table('plan_variations')->select('price')->where($where)->value('price');
            if(!$price){
                $price = "0";
            }
            $variation_data = Meal::getVariationData($request->meal_plan_id,$getGender);
            // echo '<pre>';print_r($variation_data);
            $finalData = [];
            foreach ($variation_data as $value) {
                $finalData[] = [
                    'variation_id'=>$value->variation_id,
                    'price'=>(string)$value->price,
                ];
            }
            $getMealCategories[$i]['meal_plan_id']        = (string)$category->id;
            $getMealCategories[$i]['category']            = $category->title;
            $getMealCategories[$i]['image']               = $category->image;
            $getMealCategories[$i]['cover_image']         = $finalImage;
            $getMealCategories[$i]['short_description']   = $category->short_description;
            $getMealCategories[$i]['long_description']    = $category->long_description;
            $getMealCategories[$i]['price']               = (string)$price;
            $getMealCategories[$i]['variation_data']      = $finalData;
            $getMealCategories[$i]['end_date']            = $endDate;
            $getMealCategories[$i]['all_meals_completed'] = $allMealCompleted;
        }
        // exit;
        // Log::Info($getMealCategories);
        if($getMealCategories){
            return $this->sendResponse($getMealCategories, 'Meal Plan Selected Successfully');
        }else {
            return $this->sendError([], 'Unable To select Meal Plan');
        }
    }

    public function addPlanToCart(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'               => 'required|exists:freshly_users',
            'meal_plan_id'          => 'required',
            'variation_id'          => 'required',
            'book_nutritionist'     => '',
            'add_cutlery'           => '',
            'start_date'            => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $date = date('Y-m-d', strtotime('+3 days'));
        $requestStart_date = date('Y-m-d', strtotime($request->start_date));
        if($requestStart_date < $date)
        {
            return $this->sendError([], 'Please select a date of after 72 hours');
        }
        $add_to_cart = UserMealPlans::saveMealsToCart($request->all());
        $cartId = [
            'cart_id'   => (string)$add_to_cart,
        ];
            // echo '<pre>';print_r($add_to_cart);exit;
        if(!empty($add_to_cart))
        {
            $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
            if(empty($bagRefund)){
                DB::table('bag_refund')->where(['user_id'=>$request->user_id])->insert(['user_id'=>$request->user_id,'status'=>0]);
            }
            return $this->sendResponse([$cartId], 'Meal Plan added to cart Successfully');
        }else {
            return $this->sendError([], 'Unable to add meal plan to cart');
        }
    }

    public function orderSummary(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'   => 'required',
            'cart_id'           => 'required',
            // 'subscription_id'   => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getGender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $getMode = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id,'id'=>$request->subscription_id])
                    ->value('payment_mode');
        $getSummary = UserMealPlans::getOrderSummary($request->user_id,$request->cart_id,$getGender);
        // echo '<pre>';print_r($getSummary);exit;
        $coupon_type = "";
        if(!empty($getSummary)){
            $getList = [];
            foreach ($getSummary as $value) {
                $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
                if($bagRefund){
                    if ($bagRefund->status == 1 || $bagRefund->status == 2) {
                        $bag_deposit = "0.00";
                    }else if ($bagRefund->status == 0) {
                        $bag_deposit = "100.00";
                    }
                }else {
                    $bag_deposit = "100.00";
                }
                $offerData = UserMealPlans::getOfferData($request->user_id,$getSummary['id']);
                // echo '<pre>';print_r($offerData);exit;
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
                    $location_tag = $address['location_tag'];
                    $address = $address['house_no']. " " .$address['area']. " " .$address['landmark']. " " .$address['current_location'];
                }else {
                    $address = Address::getDefaultAddress($request->user_id);
                    $location_tag = $address['location_tag'];
                    $address = $address['house_no']. " " .$address['area']. " " .$address['landmark']. " " .$address['current_location'];
                }
                if($getAddress){
                    $addressId = $getAddress;
                }else {
                    $addressId = Address::getDefaultAddress($request->user_id);
                    $addressId = $addressId['id'];
                }
                if(!empty($offerData->discount)){
                    if($offerData->coupon_type === '1'){
                        $discount = (string)$offerData->discount/100*$total;
                    }else{
                        $discount = (string)$offerData->discount;
                    }
                    $coupon_type = (string)$offerData->coupon_type;
                }else {
                    $discount = "0";
                }
                if($getSummary['weeks'] > '1'){
                    $f_week = $getSummary['weeks']. " Weeks";
                }else{
                    $f_week = $getSummary['weeks']. " Week";
                }
                if($getSummary['days'] > '1'){
                    $f_day = $getSummary['days']. " Days";
                }else{
                    $f_day = $getSummary['days']. " Day";
                }
                if($getSummary['meals'] > '1'){
                    $f_meal = $getSummary['meals']. " Meals";
                }else{
                    $f_meal = $getSummary['meals']. " Meal";
                }
                if($getSummary['snacks'] > '1'){
                    $f_snack = $getSummary['snacks']. " Snacks";
                }else{
                    $f_snack = $getSummary['snacks']. " Snack";
                }
                $grandTotal = $total - $discount;
                // echo '<pre>';print_r($discount);exit;
                $getList['unread_count']        = (string)FreshlyAuth::getUnreadCount($request->user_id);
                $getList['cart_id']             = (string)$getSummary['id'];
                $getList['user_id']             = (string)$getSummary['user_id'];
                $getList['categoryName']        = (string)$getSummary['title'];
                $getList['image']               = (string)$getSummary['image'];
                $getList['meal_plan_id']        = (string)$getSummary['meal_plan_id'];
                $getList['weeks']               = (string)$f_week;
                $getList['days']                = (string)$f_day;
                $getList['main_meal']           = (string)$f_meal;
                $getList['snack']               = (string)$f_snack;
                $getList['start_date']          = (string)date('d-m-Y',strtotime($getSummary['start_date']));
                $getList['order_amount']        = number_format((float)($getSummary['price']), 2, '.', '');
                $getList['vat']                 = number_format((float)($vat), 2, '.', '');
                $getList['bag_deposit']         = (string)$bag_deposit;
                $getList['discount']            = number_format(-(float)($discount), 2, '.', '');
                $getList['coupon_type']            = (string)($coupon_type);
                $getList['grand_total']         = number_format((float)($grandTotal), 2, '.', '');
                $getList['name']                = (string)$getSummary['fname']." ";
                $getList['email']               = (string)$getSummary['email'];
                $getList['order_number']        = (string)$getSummary['order_number'];
                $getList['date']                = $getSummary['created_at'];
                $getList['address']             = (string)$address;
                $getList['location_tag']        = (string)$location_tag;
                $getList['address_id']          = (string)$addressId;
                $getList['mobile_number']       = (string)$getSummary['country_code'].'-'.$getSummary['mobile_number'];
                $getList['country_code']        = (string)$getSummary['country_code'];
                $getList['offer_id']            = $offer_id;
                $getList['coupon_code']         = $coupon_code;
                $getList['discounted_amount']   = $discounted_amount;
                $getList['payment_mode']        = (string)$getMode;
            }
            return $this->sendResponse([$getList], 'Order Summary Shown Successfully');
        }else {
            return $this->sendError([], 'Order Summary List Empty!');
        }
    }

    public function changeAddress(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
            'cart_id'       => 'required',
            'address_id'    => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $update = DB::table('cart_summary')
                    ->where(['user_id'=>$request->user_id,'id'=>$request->cart_id])
                    ->update(['order_address' => $request->address_id]);
        $fetchCart = DB::table('cart_summary')->where(['user_id'=>$request->user_id,'id'=>$request->cart_id])->first();
        if($fetchCart){
            $fetchAddress = [
                'user_id'       => $request->user_id,
                'cart_id'       => $request->cart_id,
                'address_id'    => $fetchCart->order_address,
            ];
            return $this->sendResponse([$fetchAddress], 'Address changed successfully');
        }else {
            return $this->sendError([], 'Unable to change address!');
        }
    }

    public function couponApply(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'    => 'required',
            'cart_id'    => 'required',
            'offer_id'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getGender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $getCoupon = Offer::getCoupon($request->offer_id);
        // echo '<pre>';print_r($getCoupon->type);exit;
        $getCartData = UserMealPlans::getOrderSummary($request->user_id,$request->cart_id,$getGender);
        // echo '<pre>';print_r($getCartData);exit;
        foreach ($getCartData as $value) {
            $vat = $getCartData['price'] * (0.05);
            $total = $vat + $getCartData['price'] + 100;
            $type = Offer::getTypeById($request->offer_id);
            $amount = Offer::getAmountById($request->offer_id);
            $offerExpired = Offer::nb_mois($request->offer_id);
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
            $offerUsed = Offer::isOfferUsed($request->user_id,$request->offer_id,$getCartData['meal_plan_id'],$request->cart_id);
            if ($offerUsed == "1") {
                return $this->sendError([], 'Sorry you cannot use this offer again!');
            }
            $getList['offer_id']            = (string)$getCoupon->id;
            $getList['coupon_code']         = (string)$getCoupon->coupon_code;
            $getList['amount']              = (string)$getCoupon->amount;
            $getList['discount']            = "-AED ".$discount;
        }
        //change offer
        $changeOffer = DB::table('apply_coupon')->where(['user_id'=>$request->user_id,'offer_id'=>$request->offer_id])->first();
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

    public function payNow(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required|exists:freshly_users',
            'cart_id'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $gender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $getSummary = UserMealPlans::getOrderSummary($request->user_id,$request->cart_id,$gender);
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
                    $address = $address['house_no']. " " .$address['area']. " " .$address['landmark']. " " .$address['current_location'];
                }else {
                    $address = Address::getDefaultAddress($request->user_id);
                    $address = $address['house_no']. " " .$address['area']. " " .$address['landmark']. " " .$address['current_location'];
                }
                if($getAddress){
                    $addressId = $getAddress;
                }else {
                    $addressId = Address::getDefaultAddress($request->user_id);
                    $addressId = $addressId['id'];
                }
                if(!empty($offerData->discount)){
                    if($offerData->coupon_type === '1'){
                        $discount = (string)$offerData->discount/100*$total;
                    }else{
                        $discount = (string)$offerData->discount;
                    }
                    $coupon_type = (string)$offerData->coupon_type;
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
                $getList['order_amount']        = number_format((float)($getSummary['price']), 2, '.', '');
                $getList['vat']                 = (string)$vat;
                $getList['bag_deposit']         = (string)$bag_deposit;
                $getList['discount']            = number_format(-(float)($discount), 2, '.', '');
                // $getList['coupon_type']            = (string)($coupon_type);
                $getList['grand_total']         = (string)$grandTotal;
                $getList['name']                = (string)$getSummary['fname']." ";
                $getList['email']               = (string)$getSummary['email'];
                $getList['order_number']        = (string)$getSummary['order_number'];
                $getList['date']                = $getSummary['created_at'];
                $getList['address']             = (string)$address;
                $getList['address_id']          = (string)$addressId;
                $getList['mobile_number']       = (string)$getSummary['country_code'].'-'.$getSummary['mobile_number'];
                $getList['country_code']       = (string)$getSummary['country_code'];
                $getList['offer_id']            = $offer_id;
                $getList['coupon_code']         = $coupon_code;
                $getList['discounted_amount']   = $discounted_amount;
            }
            $updateValues = [
                'offer_id'              => $getList['offer_id'],
                'order_address'         => $getList['address_id'],
                'coupon_code'           => $getList['coupon_code'],
                'discounted_amount'     => $getList['discounted_amount'],
                'grand_total'           => $getList['grand_total'],
            ];
            DB::table('cart_summary')->where(['user_id'=>$request->user_id,'id'=>$getList['cart_id']])->update($updateValues);
            return $this->sendResponse([
                'cart_id'     => $getList['cart_id'],
                'order_total' => number_format((float)($getList['grand_total']), 2, '.', '')
            ], 'Order Summary Updated Successfully');
        }else {
            return $this->sendError([], 'Unable to update order summary!');
        }
    }

    public function finalPayPlan(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'          => 'required',
            'cart_id'          => 'required',
            'payment_mode'     => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getOrderData = DB::table('cart_summary')->where(['user_id'=>$request->user_id])->orderBy('id', 'DESC')->first();
        $activeSubscription = FreshlyAuth::runningSubscription($request->user_id,$getOrderData->start_date);
        // echo '<pre>';print_r($activeSubscription);exit;
        if($activeSubscription == "1"){
            return $this->sendError([], "You already have a running subscription.Can't subscribe!");
        }
        $payFinalPlan = UserMealPlans::payOrder($request->all());
        $fetchPlan = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id,'cart_id'=>$request->cart_id])->orderBy('id','DESC')->first();
        $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
        foreach ($fetchPlan as $value) {
            $address = Address::getAddressById($fetchPlan->order_address);
            $location_tag = $address['location_tag'];
            $date = date('d M,y', strtotime($fetchPlan->start_date));
            $variation_data = DB::table('plan_variations')->where(['variation_id'=>$fetchPlan->variation_id])->first();
            $planData = DB::table('meal_plan_types')->where(['id'=>$fetchPlan->meal_plan_id])->first();
            $fetchPlan->meal_name = $planData->title;
            $fetchPlan->image = $planData->image;
            $fetchPlan->short_description = $planData->short_description;
            $fetchPlan->long_description = $planData->long_description;
            $fetchPlan->date = $date;
            $fetchPlan->name = $userData->fname.' '.$userData->lname;
            $fetchPlan->order_number = $fetchPlan->order_number;
            $fetchPlan->email = $userData->email;
            $fetchPlan->mobile_number = $userData->country_code.'-'.$userData->mobile_number;
            $fetchPlan->country_code = $userData->country_code;
            $fetchPlan->weeks = $variation_data->weeks;
            $fetchPlan->days = $variation_data->days;
            $fetchPlan->meals = $variation_data->meals;
            $fetchPlan->snacks = $variation_data->snacks;
            $fetchPlan->location_tag = $location_tag;
            $fetchPlan->order_date = date('M d,Y',strtotime($fetchPlan->created_at));
            $fetchPlan->start_date_order = date('d-F-Y',strtotime($fetchPlan->start_date));
        }
        // echo '<pre>';print_r($fetchPlan);exit;
        if($fetchPlan){
            $categoryName = DB::table('meal_plan_types')->where(['id'=>$fetchPlan->meal_plan_id])->value('title');
            //insert notification
            DB::table('notifications')->insert([
                                'user_id'=>$request['user_id'],
                                'status'=>1,
                                'type'=>1,
                                'message'=> "You paid money to Freshly Meals for ". $categoryName,
                                'title'=>'Subscription',
                                'created_at' => date('Y-m-d h:i:s'),
                                'updated_at' => date('Y-m-d h:i:s')
                            ]);
            //email to user
            $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
            $to_email = $userData->email;
            Mail::to($to_email)->send(new PlanSubscriptionMail($fetchPlan));
            //send push notification to message
            $data = array (
                'msg_title' => 'Plan Subscription',
                'msg_body' => 'You Paid for'. $fetchPlan->meal_name .'Subscription',
                'msg_type' => '1',
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

            $variation = $fetchPlan->variation_id;
            $week  = (int)substr($variation, 0, 1);
            $day   = (int)substr($variation, 1, 1);
            $meal  = (int)substr($variation, 2, 1);
            $snack = (int)substr($variation, 3, 1);
            $totalMealsCount = ($meal + $snack) * $day * $week;
            //insert remaining meal
            DB::table('user_meal_plans')->where(['id'=>$fetchPlan->id])
                                        ->update(['total_meal'=>$totalMealsCount,'remaining_meal'=>$totalMealsCount]);
            $bagRefund = DB::table('bag_refund')->where(['user_id'=>$request->user_id])->first();
            if($bagRefund){
                if($bagRefund->status == 0){
                    //status 2 is when user haven't applied for bag refund so nxt time we will not charge bag deposit
                    DB::table('bag_refund')->where(['user_id'=>$request->user_id])->update(['status'=>2]);
                }
            }
            return $this->sendResponse([$fetchPlan], 'Order placed successfully');
        }else {
            return $this->sendError([], 'Unable to place order!');
        }
    }

    public function MealsList(Request $request, $mealData = [])
    {
        // Log::info($request->all());
        $request->date =  date("Y-m-d", strtotime($request->date));
        $validatedData = Validator::make($request->all(), [
            'user_id'          => 'required',
            'date'             => 'required',
            'category'         => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $mealPlan = DB::table('user_meal_plans')->select('meal_plan_id', 'variation_id', 'start_date')->where(['user_id'=>$request->user_id])->first();
        if(!$mealPlan){
            return response()->json([
                'status'  => 'fail',
                'message' => 'User don\'t have any plan',
                'data'    => [],
            ]);
        }
        $condition = array();
        $condition['meal_plan_id'] = $mealPlan->meal_plan_id;
        if ($request->category == '') {
             $mealList = Meal::with(['macros', 'allergens'])->where($condition)
                        ->whereDate('meal_begin_at', '=', $request->date)
                        ->get();
        } else {
            $condition['meal_type'] = $request->category;
            $mealList = Meal::with(['macros', 'allergens'])->where($condition)
                        ->whereDate('meal_begin_at', '=', $request->date)
                        ->get();
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
            $mealNames = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('food_item_meal.meal_id','=',$meal->id)
                    ->pluck('food_items.title');
            $mealNames = json_decode(json_encode($mealNames), true);
            $finalName = implode(' + ',$mealNames);
            $quantity = DB::table('users_meals')->where(['user_id'=>$request->user_id,'meal_id'=>$meal->id])
                        ->whereDate('date', '=', $request->date)->value('quantity');
            if($quantity){
                $quantityFinal = $quantity;
            }else {
                $quantityFinal = "0";
            }
            $mealData[] = [
                'id'           => (string)$meal->id,
                'name'         => $finalName,
                'image'        => $meal->image,
                'category'     => $meal->meal_type,
                'date'         => $meal->meal_begin_at,
                // 'is_meal'      => UserMealPlans::isMealSelected($request->user_id, $request->date),
                'is_meal'      => $this->mealInfo($meal->id),
                'meal_info'      =>  $meal->meal_info,
                'quantity'       => $quantityFinal,
                'macros'       => $macroData,
                'allergens'    => $allergenData,
            ];
        }
        $mealCount = $this->getMealCountCopy($request->user_id, $mealPlan->variation_id, $mealPlan->start_date, $request->date);
        $finalMeal = [
            'is_subscribed'    => "1",
            'available_meals'  => $mealCount['meals'],
            'available_sancks' => $mealCount['snacks'],
            'total_meals'      => $mealCount['total'],
            'meal_data'        => $mealData
        ];
        Log::Info($finalMeal);

        return $this->sendResponse([$finalMeal], 'Meal List Successfully');
    }

    public function MealsListAdmin(Request $request, $mealData = [])
    {
        // Log::info($request->all());
        $request->date =  date("Y-m-d", strtotime($request->date . " +1 day"));
        // echo '<pre>';print_r($request->all());exit;
        $validatedData = Validator::make($request->all(), [
            'date'      => '',
            'plan_id' => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $condition = array();
        if ($request->plan_id && $request->date == '') {
            $mealList = Meal::with(['macros', 'allergens'])
                        ->where(['meal_plan_id'=>$request->plan_id])
                        ->get();
        } elseif($request->date && $request->plan_id == '') {
            $mealList = Meal::with(['macros', 'allergens'])
                        ->whereDate('meal_begin_at', '=', $request->date)
                        ->get();
        }elseif($request->plan_id && $request->date){
            $mealList = Meal::with(['macros', 'allergens'])
                        ->whereDate('meal_begin_at', '=', $request->date)
                        ->where(['meal_plan_id'=>$request->plan_id])
                        ->get();
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
            $mealNames = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('food_item_meal.meal_id','=',$meal->id)
                    ->pluck('food_items.title');
            $mealNames = json_decode(json_encode($mealNames), true);
            $finalName = implode(' + ',$mealNames);
            $quantity = DB::table('users_meals')->where(['meal_id'=>$meal->id])
                        ->whereDate('date', '=', $request->date)->value('quantity');
            if($quantity){
                $quantityFinal = $quantity;
            }else {
                $quantityFinal = "0";
            }
            $mealPlanType = DB::table('meal_plan_types')->where('id',$meal->meal_plan_id)->value('title');
            $mealData[] = [
                'id'           => (string)$meal->id,
                'name'         => $finalName,
                'image'        => $meal->image,
                'meal_type'     => $meal->meal_type,
                'date'         => $meal->meal_begin_at,
                // 'is_meal'      => UserMealPlans::isMealSelected($request->user_id, $request->date),
                'is_meal'      => $this->mealInfo($meal->id),
                'plan_name'=> $mealPlanType,
                'quantity'       => $quantityFinal,
                'macros'       => $macroData,
                'allergens'    => $allergenData,
            ];
        }
        // $mealCount = $this->getMealCountCopyAdmin($mealPlan->variation_id, $mealPlan->start_date, $request->date);
        $finalMealAdmin = $this->paginate($mealData);
        // echo '<pre>';print_r($finalMealAdmin);exit;
        return $this->sendResponse([$finalMealAdmin], 'Meal List Successfully');
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

    function mealInfo($mealId){
        $mealInfoData = DB::table('meals')->where(['id'=>$mealId])->value('meal_info');
        if($mealInfoData == 'Meal'){
            $mealInfo = '1';
        }else{
            $mealInfo = '0';
        }
        return $mealInfo;
    }

    public function MealsListCopy(Request $request, $mealData = [])
    {
        Log::info($request->all());
        $request->date =  date("Y-m-d", strtotime($request->date));
        // echo '<pre>';print_r($request->date);exit;
        $validatedData = Validator::make($request->all(), [
            'user_id'          => 'required',
            'date'             => 'required',
            'category'         => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        // check whether the request date is between pause plan dates
        $pauseDate = UserMealPlans::isPlanPaused($request->user_id,$request->date);
        // echo '<pre>';print_r($pauseDate);exit;
        if($pauseDate){
            $planData = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
            $sDate = date('d M,Y',strtotime($planData->pause_start_date));
            $eDate = date('d M,Y',strtotime($planData->pause_end_date));
            if($request->date >= $planData->pause_start_date && $request->date <= $planData->pause_end_date){
                return $this->sendError([], 'Sorry you can\'t select meals between '.$sDate. ' to '.$eDate);
            }
        }

        $mealPlan = DB::table('user_meal_plans')->select('meal_plan_id', 'variation_id', 'start_date','remaining_meal')
                                                ->where(['user_id'=>$request->user_id])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
        if(!$mealPlan){
            return $this->sendError([], 'User don\'t have any plan');
        }
        $gender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $startDate = UserMealPlans::where('user_id',$request->user_id)->orderBy('id','DESC')->value('start_date');
        $currentDate = date('Y-m-d');
        // $currentDate = "2022-07-16";
        if($currentDate >= $startDate){
            $start_date = date('Y-m-d', strtotime($currentDate . " +3 days"));
            $day = date('D', strtotime($start_date));
            if($day == "Sun"){
                $f_start_date = date('Y-m-d', strtotime($start_date . " +1 days"));
            }else{
                $f_start_date = date('Y-m-d', strtotime($currentDate . " +3 days"));
            }
        }else{
            $day = date('D', strtotime($startDate));
            if($day == "Sun"){
                $f_start_date = date('Y-m-d', strtotime($startDate . " +1 days"));
            }else{
                $f_start_date = $startDate;
            }
        }

        $condition = array();
        $condition['meal_plan_id'] = $mealPlan->meal_plan_id;
        if ($request->category == '') {
            $mealList =Meal::query()
                        // ->with(['macros', 'allergens'])
                        ->with(['macros' => function($query) use ($gender) {
                            $query->where('gender', '=', $gender);
                        }, 'allergens'])
                        ->where($condition)
                        ->orderByRaw("FIELD(meal_info , 'Meal', 'Snack') ASC")
                        ->whereDate('meal_begin_at', '=', $request->date)
                        ->get();
            // echo '<pre>';print_r($mealList->toArray());exit;
        } else {
            $condition['meal_type'] = $request->category;
            $mealList = Meal::query()
                        // ->with(['macros', 'allergens'])
                        ->with(['macros' => function($query) use ($gender) {
                            $query->where('gender', '=', $gender);
                        }, 'allergens'])
                        ->where($condition)
                        ->orderByRaw("FIELD(meal_info , 'Meal', 'Snack') ASC")
                        ->whereDate('meal_begin_at', '=', $request->date)
                        ->get();
        }
        // echo '<pre>';print_r($mealList->toArray());exit;
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
            $mealNames = DB::table('meals')->select('food_items.title')
                    ->join('food_item_meal','food_item_meal.meal_id','=','meals.id')
                    ->join('food_items','food_items.id','=','food_item_meal.food_item_id')
                    ->where('food_item_meal.meal_id','=',$meal->id)
                    ->pluck('food_items.title');
            $mealNames = json_decode(json_encode($mealNames), true);
            $finalName = implode(' + ',$mealNames);
            // echo '<pre>';print_r($finalName);
            $mealData[] = [
                'id'                => (string)$meal->id,
                'name'              => $finalName,
                'image'             => $meal->image,
                'category'          => $meal->meal_type,
                'date'              => $meal->meal_begin_at,
                // 'is_meal'      => UserMealPlans::isMealSelected($request->user_id, $request->date),
                'is_paused'         => UserMealPlans::isPlanPaused($request->user_id,$request->date),
                'is_meal'           => (string)$this->mealInfo($meal->id),
                'macros'            => $macroData,
                'allergens'         => $allergenData,
            ];
        }
        // exit;
        $mealCount = $this->getMealCountCopy($request->user_id, $mealPlan->variation_id, $mealPlan->start_date, $request->date);
        $getDefaultAddress = Address::where(['user_id'=>$request->user_id,'default_address'=>1])->first();
        $isMealSelectedOnDate = FreshlyAuth::isMealSelectedOnDate($request->user_id,$request->date);
        // echo '<pre>';print_r($isMealSelectedOnDate);exit;
        $finalMeal = [
            'unread_count'              => (string)FreshlyAuth::getUnreadCount($request->user_id),
            'is_subscribed'             => FreshlyAuth::isSubscribed($request->user_id),
            'is_payment_done'           => FreshlyAuth::isPaymentDone($request->user_id),
            'is_pending'                => FreshlyAuth::isPaymentPending($request->user_id),
            'is_meal_selected_on_date'  => FreshlyAuth::isMealSelectedOnDate($request->user_id,$request->date),
            'available_meals'           => $mealCount['meals'],
            'available_sancks'          => $mealCount['snacks'],
            'total_meals'               => $mealCount['total'],
            'remaining_meals'           => $mealPlan->remaining_meal,
            'subscription_expired'      => Offer::subscriptionExpired($request->user_id),
            'is_paused'                 => UserMealPlans::isPlanPausedCurrentDate($request->user_id),
            'start_date'                => $f_start_date,
            // 'start_date'            => date('d-m-Y',strtotime(UserMealPlans::where('user_id',$request->user_id)->orderBy('id','DESC')->value('start_date'))),
            'current_address'           => $getDefaultAddress->current_location,
            'current_address_id'        => (string)$getDefaultAddress->id,
            'meal_data'                 => $mealData,
        ];

        return $this->sendResponse([$finalMeal], 'Meal List Successfully');
    }

    function getMealCountCopy($userId, $variation, $planStartDate, $postDate){
        $from = date('Y-m-d', strtotime($planStartDate));
        $to   = date('Y-m-d', strtotime("+3 months", strtotime($planStartDate)));

        $week  = (int)substr($variation, 0, 1);
        $day   = (int)substr($variation, 1, 1);
        $meal  = (int)substr($variation, 2, 1);
        $snack = (int)substr($variation, 3, 1);
        // echo '<pre>';print_r($meal);exit;

        $totalMealsCount = ($meal + $snack) * $day * $week;
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
            'meals'     => (string) $totalMeals,
            'snacks'    => (string) $totalSnack,
            'selected'  => (string) $selectedTotalMeals,
            'total'     => (string) $totalMealsCount,
        ];

        return $mealCount;
    }

    function getMealCount($userId, $variation, $planStartDate, $postDate){
        $from = date('Y-m-d', strtotime($planStartDate));
        $to   = date('Y-m-d', strtotime("+3 months", strtotime($planStartDate)));

        $week  = (int)substr($variation, 0, 1);
        $day   = (int)substr($variation, 1, 1);
        $meal  = (int)substr($variation, 2, 1);
        $snack = (int)substr($variation, 3, 1);

        $totalMealsCount = ($meal + $snack) * $day;
        $totalSnack = $snack * 1;
        $totalMeals = $meal * 1;

        $selectedMeals = DB::table('users_meals')
                ->join('meals','meals.id','=','users_meals.meal_id')
                ->where(['users_meals.user_id'=> $userId, 'meals.meal_info'=> 0])
                ->whereDate('users_meals.date', '=', $postDate)
                ->sum('users_meals.quantity');

        $selectedSnacks = DB::table('users_meals')
            ->join('meals','meals.id','=','users_meals.meal_id')
            ->where(['users_meals.user_id'=> $userId, 'meals.meal_info'=> 1])
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

    public function selectMeals(Request $request)
    {
        Log::info($request->all());
        $request->date = date("Y-m-d",strtotime($request->date));
        try {
            $validatedData = Validator::make($request->all(), [
                'user_id'       => 'required',
                'date'          => 'required',
                'meal_data'     => 'required',
                'address_id'    => 'required',
            ]);
            if ($validatedData->fails()) {
                $validation_error['status']  = 'fail';
                $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
                $validation_error['data']    = [];
                return response()->json($validation_error);
            }
            $alreadyMealSelect = DB::table('users_meals')
                                    ->where(['user_id'=>$request->user_id])
                                    ->whereDate('date', '=', $request->date)
                                    ->get()->toArray();
            if(!empty($alreadyMealSelect)){
                return $this->sendError([], 'You have already selected meals for this day!');
            }
            $date = date('Y-m-d', strtotime('+3 days'));
            if($request->date < $date){
                return $this->sendError([], 'Please select a date of after 72 hours');
            }
            $planData = DB::table('user_meal_plans')->where(['user_id'=>$request['user_id']])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
            $userMealCount = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planData->id])
                                                      ->first();
            if($planData->remaining_meal <= 0){
                return $this->sendError([], 'Sorry you have reached your meal selection limit');
            }
            $planId = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id])
                        ->orderBy('user_meal_plans.start_date', 'DESC')->value('id');

            Log::info($planId);

            $savedMeals           = Meal::saveUserMeals($request->all());
            $savePreparationMeals = Meal::savePreparationMeals($request->all());

            if($savedMeals && $savePreparationMeals){
                //insert plan id
                DB::table('users_meals')->where(['user_id'=>$request->user_id])->whereDate('date', '=', $request->date)->update(['plan_id'=>$planId]);
                //calculate total selected meal count
                $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                                      ->pluck('quantity')->toArray();
                $sum = array_sum($fetchUserMeals);
                DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                        ->update(['meal_count'=>$sum]);
                //notification
                DB::table('notifications')->insert([
                    'user_id'=>$request['user_id'],
                    'status'=>1,
                    'type'=>2,
                    'message'=> "You have selected meals for the date ". $request->date,
                    'title'=>'Meal Selection',
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
                //updating total meal and remaining meal in user meal plan table
                $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
                $mealCount = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                                     ->value('meal_count');
                $updateCount = $fetchPlan->total_meal - $mealCount;
                $updateCount = DB::table('user_meal_plans')->where(['id'=>$planId])->update(['remaining_meal'=>$updateCount]);
                //send email to user
                $meals = Meal::myMeals($request->user_id,$planId,$request->date);
                $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
                $to_email = $userData->email;
                Mail::to($to_email)->send(new SelectedMealsMail($meals));

                //send push notification to message
                $data = array (
                    'msg_title' => 'Meal Selection',
                    'msg_body' => "You have selected meals for the date ". $request->date,
                    'msg_type' => '2',
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

                return $this->sendResponse([], 'Meals selected Successfully');
            }
            else{
                return $this->sendError([], 'Unable to save meals');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function editMeals(Request $request){
        Log::Info($request->all());
        try {
            $request->date = date("Y-m-d",strtotime($request->date));
            $validatedData = Validator::make($request->all(), [
                'user_id'       => 'required',
                'date'          => 'required',
                'meal_data'     => 'required',
                'address_id'    => 'required',
            ]);
            if ($validatedData->fails()) {
                $validation_error['status']  = 'fail';
                $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
                $validation_error['data']    = [];
                return response()->json($validation_error);
            }
            $date = date('Y-m-d', strtotime('+3 days'));
            if($request->date < $date){
                return $this->sendError([], 'Please select a date of after 72 hours');
            }
            $planId = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id])
                                                   ->orderBy('user_meal_plans.start_date', 'DESC')
                                                   ->value('id');

            $editedMeals           = Meal::editUserMeals($request->all());
            $editPreparationMeals = Meal::editPreparationMeals($request->all());

            if($editedMeals && $editPreparationMeals){
                //notification
                DB::table('notifications')->insert([
                    'user_id'=>$request['user_id'],
                    'status'=>1,
                    'type'=>5,
                    'message'=> "You have edited meal for the date ". $request['date'],
                    'title'=>'Edit Meal',
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
                //send push notification to message
                $data = array (
                    'msg_title' => 'Edit Meal',
                    'msg_body' => "You have edited meal for the date ". $request['date'],
                    'msg_type' => '5',
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
                //insert plan id
                DB::table('users_meals')->where(['user_id'=>$request->user_id])->whereDate('date', '=', $request->date)->update(['plan_id'=>$planId]);
                //calculate total selected meal count
                $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                    ->pluck('quantity')->toArray();
                $sum = array_sum($fetchUserMeals);
                DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                        ->update(['meal_count'=>$sum]);
                //send email to user
                $meals = Meal::myMeals($request->user_id,$planId,$request->date);
                $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();

                $to_email = $userData->email;
                Mail::to($to_email)->send(new EditedMealsMail($meals));
                // updating remaining meal in user meal plan table
                $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
                $mealCount = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                                     ->value('meal_count');
                $updateCount = $fetchPlan->total_meal - $mealCount;
                $updateCount = DB::table('user_meal_plans')->where(['id'=>$planId])->update(['remaining_meal'=>$updateCount]);
                return $this->sendResponse([], 'Meals updated Successfully');
            }
            else{
                return $this->sendError([], 'Unable to update meals');
            }

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function mealOrderSummary(Request $request, $mealData = [])
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
            'date'          => 'required',
            'meal_id'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $totalMeals = Meal::selectMealsList($request->user_id);
        $yourSelectedMeals = explode(',', $request->meal_id);
        $yourSelectedMeals = count($yourSelectedMeals);
        $availableMeals = $totalMeals - $yourSelectedMeals;
        if($yourSelectedMeals > $totalMeals)
        {
            return $this->sendError([], 'You cannot select more than ' .$totalMeals. ' meals');
        }
        $mealOrderSummary = [
            'unread_count'      => (string)FreshlyAuth::getUnreadCount($request->user_id),
            'availableMeals'    => (string)$availableMeals,
            'yourSelectedMeals' => (string)$yourSelectedMeals,
            'totalMeals'        => (string)$totalMeals,
        ];
        if($mealOrderSummary)
        {
            return $this->sendResponse($mealOrderSummary, 'Meals selected Successfully');
        }
        else{
            return $this->sendError([], 'Unable to save meals');
        }
    }

    public function myPlans(Request $request, $mealData = [])
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        // $date = "2022-06-23 01:01:01";
        $date = date('Y-m-d'). " 00:00:00";
        $myPlans = DB::table('users_meals')->where(['user_id'=>$request->user_id])
                    ->whereDate('date','>=',$date)
                    ->orderBy('date','ASC')->get()->toArray();
        // echo '<pre>';print_r($myPlans);exit;
        $finalMealList = [];
        $i=0;
        foreach ($myPlans as $value) {
            $mealData = Meal::myPlanData($value->user_id,$value->date);
            $date = date('d-m-Y', strtotime($value->date));
            $finalMealList[$i]['date']          = $date;
            $finalMealList[$i]['unread_count']  = (string)FreshlyAuth::getUnreadCount($request->user_id);
            $finalMealList[$i]['meal_data']     = $mealData;
            $i++;
        }
        $unique_arr = self::super_unique($finalMealList, 'date');
        // $sortedArr = collect($unique_arr)->sortBy('date')->all();
        if($myPlans){
            return $this->sendResponse($unique_arr, 'My plans shown Successfully');
        }
        else{
            return $this->sendResponse([], 'My plans is empty!');
        }
    }

    function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
        $reference_array = array();

        foreach($array as $key => $row) {
            $reference_array[$key] = $row[$column];
        }

        array_multisort($reference_array, $direction, $array);
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

    public function myPlanPerDate(Request $request, $mealData = [])
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
            'date'          => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $request->date = date('Y-m-d',strtotime($request->date));
        $mealPlan = DB::table('user_meal_plans')->select('meal_plan_id', 'variation_id', 'start_date','remaining_meal')
                                                ->where(['user_id'=>$request->user_id])
                                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                                ->first();
        if(!$mealPlan){
            return response()->json([
                'status'  => 'fail',
                'message' => 'User don\'t have any plan',
                'data'    => [],
            ]);
        }
        $gender = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->value('gender');
        $condition = array();
        $condition['meal_plan_id'] = $mealPlan->meal_plan_id;
        if ($request->category == '') {
            $mealList =Meal::query()
                        ->with(['macros' => function($query) use ($gender) {
                            $query->where('gender', '=', $gender);
                        }, 'allergens'])
                        ->where($condition)
                        ->orderByRaw("FIELD(meal_info , 'Meal', 'Snack') ASC")
                        ->whereDate('meal_begin_at', '=', $request->date)
                        // ->whereDate('meal_begin_at', '<', date('Y-m-d'))
                        ->get();
        } else {
            $condition['meal_type'] = $request->category;
            $mealList = Meal::query()
                        ->with(['macros' => function($query) use ($gender) {
                            $query->where('gender', '=', $gender);
                        }, 'allergens'])
                        ->where($condition)
                        ->orderByRaw("FIELD(meal_info , 'Meal', 'Snack') ASC")
                        ->whereDate('meal_begin_at', '=', $request->date)
                        // ->whereDate('meal_begin_at', '<', date('Y-m-d'))
                        ->get();
        }
        // echo '<pre>';print_r($mealList->toArray());exit;
        if(isset($mealList) && count($mealList) > 0){
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

                $quantity = DB::table('users_meals')->where(['user_id'=>$request->user_id,'meal_id'=>$meal->id])
                                ->whereDate('date', '=', $request->date)
                                ->value('quantity');
                                // echo '<pre>';print_r($addressId);
                if($quantity){
                    $quantityFinal = $quantity;
                }else {
                    $quantityFinal = "0";
                }
                $getMealDataArray = DB::table('meals')->where(['id'=>$meal->id])->get()->toArray();
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
                $mealBeginDate = date('d-m-Y',strtotime($meal->meal_begin_at));
                $mealData[] = [
                    'id'           => (string)$meal->id,
                    'name'         => $finalName,
                    'image'        => $meal->image,
                    'category'     => $meal->meal_type,
                    'date'         => $mealBeginDate,
                    'is_selected'  => UserMealPlans::isMealSelected($request->user_id, $request->date, $meal->id),
                    'quantity'     => (string)$quantityFinal,
                    'is_meal'      => (string)$this->mealInfo($meal->id),
                    'macros'       => $macroData,
                    'allergens'    => $allergenData,
                ];
            }
            $address_id = DB::table('users_meals')->where(['user_id'=>$request->user_id])
                         ->whereDate('date', '=', $request->date)
                         ->value('address_id');
            // echo '<pre>';print_r($address);
            // exit;
            $getDefaultAddress = Address::where(['user_id'=>$request->user_id,'id'=>$address_id])->first();
            $mealCount = $this->getMealCountCopy($request->user_id, $mealPlan->variation_id, $mealPlan->start_date, $request->date);

            $finalMeal = [
                'unread_count'          => (string)FreshlyAuth::getUnreadCount($request->user_id),
                'is_subscribed'         => FreshlyAuth::isSubscribed($request->user_id),
                'available_meals'       => $mealCount['meals'],
                'available_sancks'      => $mealCount['snacks'],
                'total_meals'           => $mealCount['total'],
                'remaining_meals'       => $mealPlan->remaining_meal,
                'delivery_address'      => (string)$getDefaultAddress->current_location,
                'delivery_address_id'   => (string)$getDefaultAddress->id,
                'date'                  => $meal->meal_begin_at,
                'meal_data'             => $mealData
            ];
        }


        if(count($mealList)>0)
        {
            return $this->sendResponse($finalMeal, 'Meals per date shown Successfully');
        }
        else{
            return $this->sendError([], 'Unable to show meals per date');
        }
    }

    public function deleteMeal(Request $request)
    {
        try {
            $request->date = date('Y-m-d',strtotime($request->date));
            $validatedData = Validator::make($request->all(), [
                'user_id'       => 'required',
                'date'          => 'required',
            ]);
            if ($validatedData->fails()) {
                $validation_error['status']  = 'fail';
                $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
                $validation_error['data']    = [];
                return response()->json($validation_error);
            }
            $planId = DB::table('user_meal_plans')->where(['user_id'=>$request->user_id])
                                ->orderBy('user_meal_plans.start_date', 'DESC')
                                ->value('id');
            //update meal count
            $date = $request->date. " 00:00:00";
            $countPerDate = DB::table('users_meals')->where(['user_id'=>$request->user_id])
                            ->whereDate('date', '=', $date)
                            ->pluck('quantity')->toArray();
            $sum = array_sum($countPerDate);
            $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                ->pluck('quantity')->toArray();
            $sumFinal = array_sum($fetchUserMeals);
            $updateMealCount = $sumFinal - $sum;
            // echo '<pre>';print_r($updateMealCount);exit;
            DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                                    ->update(['meal_count'=>$updateMealCount]);
            $deleteMeal = DB::table('users_meals')->where(['user_id'=>$request->user_id])
                            ->whereDate('date', '=', $request->date)
                            ->delete();
            if($deleteMeal){
                //notification
                DB::table('notifications')->insert([
                    'user_id'=>$request['user_id'],
                    'status'=>1,
                    'type'=>6,
                    'message'=> "You have deleted meal for the date ". $request['date'],
                    'title'=>'Delete Meal',
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
                //send push notification to message
                $data = array (
                    'msg_title' => 'Delete Meal',
                    'msg_body' => "You have deleted meal for the date ". $request['date'],
                    'msg_type' => '6',
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

                $date = date('Y-m-d', strtotime('-1 day', strtotime($request->date)));
                DB::table('kithen_user_meals')->where(['user_id'=>$request->user_id,'preparation_at'=>$date])->delete();
                // updating remaining meal in user meal plan table
                $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
                $mealCount = DB::table('users_meals')->where(['user_id'=>$request->user_id,'plan_id'=>$planId])
                            ->value('meal_count');
                $updateCount = $fetchPlan->total_meal - $mealCount;
                $updateCount = DB::table('user_meal_plans')->where(['id'=>$planId])->update(['remaining_meal'=>$updateCount]);

                //send email to user
                $cancelDate = date('d M,Y', strtotime($request->date));
                $userData = DB::table('freshly_users')->where(['user_id'=>$request->user_id])->first();
                $to_email = $userData->email;
                Mail::to($to_email)->send(new DeleteMealMail($cancelDate));
                return $this->sendResponse([], 'Meals deleted Successfully');
            }
            else{
                return $this->sendError([], 'Unable to delete meals');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function runningPlanSDate(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id'       => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $startDate = UserMealPlans::where('user_id',$request->user_id)->orderBy('id','DESC')->value('start_date');
        $currentDate = date('Y-m-d');
        // $currentDate = "2022-06-19";
        if($currentDate >= $startDate){
            $start_date = date('Y-m-d', strtotime($currentDate . " +3 days"));
            $day = date('D', strtotime($start_date));
            if($day == "Sun"){
                $f_start_date = date('Y-m-d', strtotime($start_date . " +1 days"));
            }else{
                $f_start_date = date('Y-m-d', strtotime($currentDate . " +3 days"));
            }
        }else{
            $day = date('D', strtotime($startDate));
            if($day == "Sun"){
                $f_start_date = date('Y-m-d', strtotime($startDate . " +1 days"));
            }else{
                $f_start_date = $startDate;
            }
        }
        // $sDate = date('Y-m-d',strtotime(UserMealPlans::where('user_id',$request->user_id)->orderBy('id','DESC')->value('start_date')));
        return $this->sendResponse($f_start_date, 'Meals S date Successfully');
    }

    function saveMealAdmin(Request $request){
        $requestData = $request->all();
        $userId = $requestData[0]['user_id'];
        $date = $requestData[0]['date'];

        $finalDate = Carbon::parse($date);
        $fdate = $finalDate->format('Y-m-d h:i:s');
        // echo '<pre>';print_r($requestData);exit;
        Log::info($date);
        Log::info($finalDate);
        Log::info($fdate);
        $alreadyMealSelect = DB::table('users_meals')
                                ->where(['user_id'=>$userId])
                                ->whereDate('date', '=', $date)
                                ->get()->toArray();
        if(!empty($alreadyMealSelect)){
            return $this->sendError([], 'You have already selected meals for this day!');
        }
        $date3DayAgo = date('Y-m-d', strtotime('+3 days'));
        if($date < $date3DayAgo){
            return $this->sendError([], 'Please select a date of after 72 hours');
        }
        $planData = DB::table('user_meal_plans')->where(['user_id'=>$userId])->orderBy('user_meal_plans.id', 'DESC')->first();
        $userMealCount = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planData->id])->first();
        if($userMealCount){
            if($planData->remaining_meal <= "0"){
                return $this->sendError([], 'Sorry you have reached your meal selection limit');
            }
        }
        $planId = DB::table('user_meal_plans')->where(['user_id'=>$userId])->orderBy('user_meal_plans.start_date', 'DESC')->value('id');
        $getMealByDate = DB::table('users_meals')->where(['user_id'=>$userId,'date'=>$date])->get()->toArray();
        // echo '<pre>';print_r($getMealByDate);exit;
        if($getMealByDate){
            foreach ($requestData as $value) {
                // echo '<pre>';print_r($value);
                $meal_id = $value['id'];
                $quantity = $value['quantity'];
                if($quantity > 0){
                    $getMealById = DB::table('users_meals')->where(['user_id'=>$userId,'date'=>$date,'meal_id'=>$meal_id])->first();
                    if($getMealById){
                        Log::info($fdate);
                        $update = DB::table('users_meals')->where(['user_id'=>$userId,'meal_id'=>$meal_id,'plan_id'=>$planId,'date'=>$date])->update(['quantity'=>$quantity]);
                        $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId])->pluck('quantity')->toArray();
                        $sum = array_sum($fetchUserMeals);
                        DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId,'date'=>$date])->update(['meal_count'=>$sum]);
                        //updating total meal and remaining meal in user meal plan table
                        $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
                        $mealCount = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId,'date'=>$date])->value('meal_count');
                        $updateCount = $fetchPlan->total_meal - $mealCount;
                        $updateCount = DB::table('user_meal_plans')->where(['id'=>$planId])->update(['remaining_meal'=>$updateCount]);
                    }else{
                        $insert = DB::table('users_meals')->where(['user_id'=>$userId,'meal_id'=>$meal_id,'plan_id'=>$planId,'date'=>$fdate])
                                    ->insert(['quantity'=>$quantity,'user_id'=>$userId,'meal_id'=>$meal_id,'plan_id'=>$planId,
                                    'date'=>$date, 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')]);
                        $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId])->pluck('quantity')->toArray();
                        $sum = array_sum($fetchUserMeals);
                        DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId])->update(['meal_count'=>$sum]);
                        //updating total meal and remaining meal in user meal plan table
                        $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
                        $mealCount = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId,'date'=>$date])->value('meal_count');
                        $updateCount = $fetchPlan->total_meal - $mealCount;
                        $updateCount = DB::table('user_meal_plans')->where(['id'=>$planId])->update(['remaining_meal'=>$updateCount]);
                    }
                }
            }
        }else{
            foreach ($requestData as $value) {
                // echo '<pre>';print_r($fdate);exit;
                $meal_id = $value['id'];
                $quantity = $value['quantity'];
                if($quantity > 0){
                    $insert = DB::table('users_meals')->where(['user_id'=>$userId,'meal_id'=>$meal_id,'plan_id'=>$planId,'date'=>$fdate])
                                ->updateOrInsert(['quantity'=>$quantity,'user_id'=>$userId,'meal_id'=>$meal_id,'plan_id'=>$planId,
                                'date'=>$date, 'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')]);
                    $fetchUserMeals = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId])->pluck('quantity')->toArray();
                    $sum = array_sum($fetchUserMeals);
                    DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId])->update(['meal_count'=>$sum]);
                    //updating total meal and remaining meal in user meal plan table
                    $fetchPlan = DB::table('user_meal_plans')->where(['id'=>$planId])->first();
                    $mealCount = DB::table('users_meals')->where(['user_id'=>$userId,'plan_id'=>$planId,'date'=>$date])->value('meal_count');
                    $updateCount = $fetchPlan->total_meal - $mealCount;
                    $updateCount = DB::table('user_meal_plans')->where(['id'=>$planId])->update(['remaining_meal'=>$updateCount]);
                }
            }
        }

        return $this->sendResponse([], 'Meals Updated Successfully');
    }

    public function getAllPlanTypes(){
        $plan =  DB::table('meal_plan_types')->get();
        return self::sendResponse([$plan], 'Meals Updated Successfully');
    }

    public function bookNutritionistChecked($id){
        $book = DB::table('user_meal_plans')->where(['user_id'=>$id])->orderBy('id','DESC')->first();
        if($book){
            DB::table('user_meal_plans')->where(['id'=>$book->id])->update(['book_status'=>1]);
            return $this->sendResponse([], 'Checked Successfully');
        }
    }
}
