<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyTestMail;

class EmailController extends Controller
{
    public function sendEmail() {
        
        $to_email = "bhumil.luckimedia@gmail.com";

        Mail::to($to_email)->send(new MyTestMail);

        return "<p> Success! Your E-mail has been sent.</p>";
    }
}