<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Mail;
use DB;
use URL;
use Hash;

class ForgotPasswordController extends BaseController
{
    protected function sendResetLinkResponse(Request $request, $response)
    {
        //return response(['message'=> $response]);
        return response(['STATUS'=>'true','message'=>'Please check your email. Password Reset Link has been sent to your registered email address.','response'=>'sucess']);
    }


    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response(['STATUS'=>'false','message'=> $response]);
    }

    public function forgotPassword(Request $request) {
        $credentials = request()->validate(['email' => 'required|email']);

        $user = DB::table('freshly_users')->where(['email'=>$request->email])->first();
        if(!empty($user)){

            $token = Str::random(50);
            $string = Str::random(5);
            DB::table('freshly_users')->where(['email'=>$request->email])->update(['remember_token'=>$token,'password'=>Hash::make($string)]);
            $array = [
                'password'=>$string,
                'email'=>$request->email,
            ];
            $to_email = $request->email;
            Mail::to($to_email)->send(new ForgotPasswordMail($array));

            return $this->sendResponse([], 'A Random Password has been sent to your email id.');
        }

        return $this->sendError([], 'Unable to sent link!');
    }

    public function changePassword()
    {
        $userId = base64_decode($_GET['userid']);
        if(isset($_GET['token'])){
            $token = $_GET['token'];
        }else{
            return URL::to('/');
        }
        return view('common.reset_pass', compact('token', 'userId'));
    }

    public function reset(Request $request) {
        $userPassword = DB::table('users')->where('id', $request->user_id)->first();
        if(!empty($userPassword)){
            $uodatePassword = DB::table('users')->where('id', $request->user_id)->update(['remember_token'=> NULL, 'password'=>Hash::make($request->password)]);
            return redirect()->back()->with('message', 'Password Reset Successfully!');
        }else{
            return URL::to('/');
        }
    }
}