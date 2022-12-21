<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class FreshlyAuthController extends Controller
{
    public function viewOtp($user_id)
    {  
        // $userId = base64_decode($user_id);
        $view_otp = DB::table('verify_otp')->where(['user_id' => $user_id])->value('otp');
        return view('common.myTestMail', compact('view_otp'));
    } 
}
