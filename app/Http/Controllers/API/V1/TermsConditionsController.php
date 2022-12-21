<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermsConditionsController extends Controller
{
    public function aboutUs()
    {
        return view('common.aboutus');
    }

    public function termsCondition()
    {
        return view('common.termscondition');
    }

    public function privacyPolicy()
    {
        return view('common.privacypolicy');
    }
}
