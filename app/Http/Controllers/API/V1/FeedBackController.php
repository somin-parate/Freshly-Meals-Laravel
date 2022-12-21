<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Faqs;
use App\Models\ApiToken;
use App\Models\PushNotification;
use Illuminate\Http\Request;
use Validator;
use Hash;
use URL;
use DB;
use Log;
use Password;

class FeedBackController extends BaseController
{
    public function storeFeedbacks(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id'   => 'required',
            'type'      => '',
            'subject'   => 'required',
            'message'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $storeData = Feedback::storeDatas($request->all());
        if ($storeData) {
            if ($request->type == 'feedback') {
                return $this->sendResponse([], 'Thank you.We received your feedback!');
            } else if ($request->type == 'bug') {
                return $this->sendResponse([], 'Thank you.We received your bug!');
            } else if ($request->type == 'refund') {
                return $this->sendResponse([], 'Thank you.Bag refunded successfully!');
            }
        } else {
            return $this->sendError([], 'Sorry we cannot process you request!');
        }
    }

    public function getFaqs(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'category'  => '',
        ]);
        if ($validatedData->fails()) {
            $validation_error['status']  = 'fail';
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $getFaqs = DB::table('faqs')->where(['category' => $request->category])->get()->toArray();
        $finalFaqs = [];
        $i = 0;
        foreach ($getFaqs as $value) {
            $finalFaqs[$i]['id']        = (string)$value->id;
            $finalFaqs[$i]['subject']   = (string)$value->subject;
            $finalFaqs[$i]['message']   = (string)$value->message;
            $finalFaqs[$i]['type']      = (string)$value->type;
            $i++;
        }
        if ($finalFaqs) {
            return $this->sendResponse($finalFaqs, 'shown successfully!');
        } else {
            return $this->sendError([], 'Sorry we cannot process you request');
        }
    }

    public function getFeedbackList()
    {
        $feedback = DB::table('feedbacks')->where(['type' => 'feedback'])->latest()->paginate(10);
        foreach ($feedback as $value) {
            $user = DB::table('freshly_users')->where(['user_id' => $value->user_id])->value('fname');
            $value->name      = $user;
            $value->date      = date("F jS, Y", strtotime($value->created_at));
        }
        return $this->sendResponse($feedback, 'Feedback list');
    }

    public function getBugsList()
    {
        $feedback = DB::table('feedbacks')->where(['type' => 'bug'])->latest()->paginate(10);
        foreach ($feedback as $value) {
            $user = DB::table('freshly_users')->where(['user_id' => $value->user_id])->value('fname');
            $value->date      = date("F jS, Y", strtotime($value->created_at));
            $value->name      = $user;
        }
        return $this->sendResponse($feedback, 'Bugs list');
    }

    public function bugByUserId($userId)
    {
        echo "test";exit;
        $feedback = DB::table('feedbacks')->where(['type' => 'bug', 'user_id' => $userId])->latest()->paginate(10);
        foreach ($feedback as $value) {
            $user = DB::table('freshly_users')->where(['user_id' => $value->user_id])->value('fname');
            $date = $value->created_at;
            $date = explode(' ', $date);
            $date = $date[0];
            $date = date('M d', strtotime($date));
            $value->name      = $user;
            $value->date      = $date;
        }
        return $this->sendResponse($feedback, 'Bugs list by user.');
    }

    public function getRefundsList()
    {
        $feedback = DB::table('bag_refund')->where(['status' => 1])->latest()->paginate(10);
        foreach ($feedback as $value) {
            $user = DB::table('freshly_users')->where(['user_id' => $value->user_id])->value('fname');
            $value->date      = date("F jS, Y", strtotime($value->created_at));
            $value->name      = $user;
        }
        return $this->sendResponse($feedback, 'Refunds list');
    }

    public function markPaidRefund($id)
    {
        $user_id = DB::table('user_meal_plans')->where(['id' => $id])->value('user_id');
        $paid = DB::table('bag_refund')->where(['id' => $id])->update(['status' => 0]);
        if ($paid) {
            //notification
            DB::table('notifications')->insert([
                'user_id' => $user_id,
                'status'=>1,
                'type' => 7,
                'message' => "Your bag refund is paid",
                'title' => 'Bag Refund',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
            //send push notification to message
            $data = array(
                'msg_title' => 'Bag Refund',
                'msg_body' => "Your bag refund is paid",
                'msg_type' => '7',
            );
            $token = ApiToken::getDeviceToken($user_id);
            $dev_token = array();
            $i = 0;
            foreach ($token as $devicetoken) {
                $dev_token[$i] = $devicetoken['device_token'];
                $i++;
            }
            $send_notification = PushNotification::android($data, $dev_token);
            return $this->sendResponse($paid, 'Refund Successfull');
        }
    }

    public function bankPayment($id)
    {
        $user_id = DB::table('user_meal_plans')->where(['id' => $id])->value('user_id');
        $pay = DB::table('user_meal_plans')->where(['id' => $id])->update(['status' => 1]);
        if ($pay) {
            //notification
            DB::table('notifications')->insert([
                'user_id' => $user_id,
                'status'=>1,
                'type' => 8,
                'message' => "Your bank payment has been approved by Freshly Meals",
                'title' => 'Bank Payment Approval',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
            //send push notification to message
            $data = array(
                'msg_title' => 'Bank Payment Approval',
                'msg_body' => "Your bank payment has been approved by Freshly Meals",
                'msg_type' => '8',
            );
            $token = ApiToken::getDeviceToken($user_id);
            $dev_token = array();
            $i = 0;
            foreach ($token as $devicetoken) {
                $dev_token[$i] = $devicetoken['device_token'];
                $i++;
            }
            $send_notification = PushNotification::android($data, $dev_token);
            return $this->sendResponse($pay, 'Payment Successfull');
        }
    }

    public function onlinePayment($id){
        $user = DB::table('user_meal_plans')->where(['id' => $id])->first();
        // echo '<pre>';print_r($user);exit;
        $pay = DB::table('payment_details')->where(['cart_id' => $user->cart_id])->update(['is_pending' => 'false','is_on_hold' => 'false']);
        if ($pay) {
            //notification
            DB::table('notifications')->insert([
                'user_id' => $user->user_id,
                'status'=>1,
                'type' =>20,
                'message' => "Your online payment has been approved by Freshly Meals",
                'title' => 'Online Payment Approval',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s')
            ]);
            //send push notification to message
            $data = array(
                'msg_title' => 'Online Payment Approval',
                'msg_body' => "Your online payment has been approved by Freshly Meals",
                'msg_type' => '20',
            );
            $token = ApiToken::getDeviceToken($user->user_id);
            $dev_token = array();
            $i = 0;
            foreach ($token as $devicetoken) {
                $dev_token[$i] = $devicetoken['device_token'];
                $i++;
            }
            $send_notification = PushNotification::android($data, $dev_token);
            return $this->sendResponse($pay, 'Payment Successfull');
        }
    }

    public function getFaqsList()
    {
        $paginateFaqs = DB::table('faqs')->latest()->paginate(10);
        $arrayFaqs = DB::table('faqs')->latest()->get()->toArray();
        foreach ($arrayFaqs as $value) {
            if($value->category == "subscriptions"){
                $value->category_name = "Subscriptions";
            }
            if($value->category == "pricing"){
                $value->category_name = "Pricing, Payments & Refunds";
            }
            if($value->category == "food_allergies"){
                $value->category_name = "Food Allergies & Preferences";
            }
            if($value->category == "weight_manage"){
                $value->category_name = "Weight Management Claims";
            }
            if($value->category == "promotions"){
                $value->category_name = "Promotions & Deals";
            }
            $value->category = $value->category;
        }
        // echo '<pre>';print_r($arrayFaqs);exit;
        return $this->sendResponse(['paginateFaqs' => $paginateFaqs, 'arrayFaqs'=> $arrayFaqs], 'Faqs list');
    }

    public function getHelpSupportList()
    {
        $helps = DB::table('faqs')->where(['category' => 'help_support'])->latest()->paginate(10);
        return $this->sendResponse($helps, 'Help Support list');
    }

    public function getGeneralIssuesList()
    {
        $issues = DB::table('faqs')->where(['category' => 'general_issues'])->latest()->paginate(10);
        return $this->sendResponse($issues, 'General Issues list');
    }

    public function storeFaqs(Request $request)
    {
        // echo '<pre>';print_r($request->all());exit;
        $rules = [
            'category'    =>  'required',
            'subject'    =>  'required',
            'message'    =>  'required',
            'type'    =>  'required',
        ];

        $customMessages = [
            'category.required'      => 'Category is required!',
            'subject.required'      => 'Subject is required!',
            'message.required'      => 'Message is required!',
            'type.required'      => 'Type is required!',
        ];

        $this->validate($request, $rules, $customMessages);
        $faqs = [
            'category'  => $request->category,
            'subject'  => $request->subject,
            'message'  => $request->message,
            'type'  => $request->type,
        ];
        $faqsInsertion = DB::table('faqs')->where(['category' => $request->category])->insert($faqs);
        return $this->sendResponse($faqsInsertion, 'Faqs Added Successfully');
    }

    public function editFaqs(Request $request, $id)
    {
        // echo ($data);exit;
        $rules = [
            'category'  =>  '',
            'subject'   =>  '',
            'message'   =>  '',
            'type'      =>  '',
        ];

        $this->validate($request, $rules);
        $faqs = [
            'category'  => $request->category,
            'subject'   => $request->subject,
            'message'   => $request->message,
            'type'      => $request->type,
        ];

        $data = DB::table('faqs')->where(['id' => $id])->get();
        $data = DB::table('faqs')->where(['id' => $id])->update($faqs);
        return $this->sendResponse($data, 'Faqs Updated Successfully');
    }

    public function deleteFaqs($id)
    {
        // echo '<pre>';print_r($id);exit;
        $faqs = DB::table('faqs')->where(['id' => $id])->delete();
        return $this->sendResponse([], 'Faqs has been Deleted');
    }

    public function viewFeedback($id)
    {
        $feeds = DB::table('feedbacks')->where(['id' => $id])->first();
        foreach ($feeds as $value) {
            $data = DB::table('freshly_users')->where(['user_id' => $feeds->user_id])->first();
            $date = date('M d H:ia', strtotime($feeds->created_at));
            $feeds->email = $data->email;
            $feeds->date  = $date;
        }
        return $this->sendResponse($feeds, 'Feedback view successfully');
    }

    public function feedbackByUserId($userId)
    {
        $feedback = DB::table('feedbacks')->where(['type' => 'feedback', 'user_id' => $userId])->latest()->paginate(10);
        foreach ($feedback as $value) {
            $user = DB::table('freshly_users')->where(['user_id' => $value->user_id])->value('fname');
            $value->name      = $user;
            $value->date      = date("F jS, Y", strtotime($value->created_at));
        }
        return $this->sendResponse($feedback, 'Feedback By User successfully');
    }

    public function viewBug($id)
    {
        $feeds = DB::table('feedbacks')->where(['id' => $id])->first();
        foreach ($feeds as $value) {
            $data = DB::table('freshly_users')->where(['user_id' => $feeds->user_id])->first();
            $date = date('M d H:ia', strtotime($feeds->created_at));
            $feeds->email = $data->email;
            $feeds->date  = $date;
        }
        return $this->sendResponse($feeds, 'Feedback view successfully');
    }

    public function viewBagRefund($id)
    {
        $feeds = DB::table('bag_refund')->where(['id' => $id])->first();
        foreach ($feeds as $value) {
            $data = DB::table('freshly_users')->where(['user_id' => $feeds->user_id])->first();
            $date = date('M d H:ia', strtotime($feeds->created_at));
            $feeds->email = $data->email;
            $feeds->date  = $date;
        }
        return $this->sendResponse($feeds, 'Feedback view successfully');
    }
}
