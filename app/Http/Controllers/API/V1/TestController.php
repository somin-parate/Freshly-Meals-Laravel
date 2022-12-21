<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(){
        $client = new \GuzzleHttp\Client();
    
        $response = $client->request('POST', 'https://api.tabby.ai/api/cashier/v1/auth/login', [
        // 'body' => '{
        //     "email": "bhumil.luckimedia@gmail.com",
        //     "password": "luckimedia@12345"
        //     }',
        'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Basic ZHNnV21LSG0zV0Jqa3NBaWRhbWNKTldrSEVESFpwSXk6Q2hpdXVAMTIzNA==',
            'Content-Type' => 'application/json',
        ],
        ]);
        // $jsonData = $response->json();
      	
     	// dd($jsonData);
    
    echo $response->getBody();

    }
}
// <?php

// namespace App\Http\Controllers\API\V1;

// use Illuminate\Support\Facades\Http;
// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

// class TestController extends BaseController
// {
//     public function index()
//     {
//         $response = Http::get('http://jsonplaceholder.typicode.com/posts');
  
//     	$jsonData = $response->json();
      	
//      	dd($jsonData);

//     }
// }