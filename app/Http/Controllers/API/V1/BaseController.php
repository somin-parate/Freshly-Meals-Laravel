<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param $result
     * @param $message
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'message' => $errorMessages,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }


    /**
     * return Unauthorized response.
     *
     * @param $error
     * @param  int  $code
     *
     * @return \Illuminate\Http\Response
     */
    public function unauthorizedResponse($error = 'Forbidden', $code = 403)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }

    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function covertTime($start, $end){
        return (date('h.iA', strtotime($start))).' - '.(date('h.iA', strtotime($end)));
    }

    function synkDynamicPrices($prices, $id, $method){
        foreach ( json_decode($prices) as $price ) {
            $planId  = $id;
            $week  = (int)$price->week;
            $day   = (int)$price->day;
            $meal  = (int)$price->meal;
            $snack = $price->snack;
            $gender = $price->gender;
            $price1 = (float)$price->price;
            $snack = ($snack == 'nosnack') ? 0 : (int)$snack;
            $variationId = $week.$day.$meal.$snack;

            $priceData = [];
            $priceData = [
                'plan_id'      => $planId,
                'weeks'        => $week,
                'days'         => $day,
                'meals'        => $meal,
                'snacks'       => $snack,
                'gender'       => $gender,
                'variation_id' => $variationId,
            ];

            $priceExists = DB::table('plan_variations')->where($priceData)->first();
            if(!$priceExists){
                $priceData['price'] = $price1;
                $this->insertVariation($priceData);
            }
        }
        return;
    }

    function updateVariration($data, $id){
        return DB::table('plan_variations')->where(['id' => $id])->update($data);
    }

    function insertVariation($data){
        return DB::table('plan_variations')->insert($data);
    }

    function deleteVariation($id){
        return DB::table('plan_variations')->where(['plan_id' => $id])->delete();
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

    public function itemsByTitle(Request $request){
        $categories = $request->categories;
        // echo '<pre>';print_r($users);exit;
        $like = $request->keyword;
        if($like == ''){
            // echo 'test';exit;
            $final = $categories;
        }else{
            $result = array_filter($categories, function ($item) use ($like) {
                if (stripos($item['fname'], $like) !== false) {
                    return true;
                }elseif (stripos($item['email'], $like) !== false) {
                    return true;
                }elseif (stripos($item['gender'], $like) !== false) {
                    return true;
                }elseif (stripos($item['lname'], $like) !== false) {
                    return true;
                }
                return false;
            });

            $final = array_values($result);
        }
        $freshlyItems = $this->paginate($final);
        // echo '<pre>';print_r($freshlyItems);exit;
        return $this->sendResponse($freshlyItems, 'Freshly Items');
    }

    public function searchCities(Request $request){
        $cities = $request->cities;
        $like = $request->keyword;
        if($like == ''){
            // echo 'test';exit;
            $final = $cities;
        }else{
            $result = array_filter($cities, function ($item) use ($like) {
                if (stripos($item['city'], $like) !== false) {
                    return true;
                }elseif (stripos($item['code'], $like) !== false) {
                    return true;
                }
                return false;
            });

            $final = array_values($result);
        }
        $freshlyItems = $this->paginate($final);
        return $this->sendResponse($freshlyItems, 'Freshly Items');
    }

    public function searchAllergens(Request $request){
        $categories = $request->categories;
        $like = $request->keyword;
        if($like == ''){
            // echo 'test';exit;
            $final = $categories;
        }else{
            $result = array_filter($categories, function ($item) use ($like) {
                if (stripos($item['title'], $like) !== false) {
                    return true;
                }
                return false;
            });

            $final = array_values($result);
        }
        $freshlyItems = $this->paginate($final);
        return $this->sendResponse($freshlyItems, 'Freshly Items');
    }
    
    public function searchPlanTypes(Request $request){
        $plans = $request->plans;
        $like = $request->keyword;
        if($like == ''){
            // echo 'test';exit;
            $final = $plans;
        }else{
            $result = array_filter($plans, function ($item) use ($like) {
                if (stripos($item['title'], $like) !== false) {
                    return true;
                }
                return false;
            });

            $final = array_values($result);
        }
        $freshlyItems = $this->paginate($final);
        return $this->sendResponse($freshlyItems, 'Freshly Items');
    }

    public function searchFoods(Request $request){
        $offers = $request->offers;
        $like = $request->keyword;
        if($like == ''){
            // echo 'test';exit;
            $final = $offers;
        }else{
            $result = array_filter($offers, function ($item) use ($like) {
                if (stripos($item['title'], $like) !== false) {
                    return true;
                }
                return false;
            });

            $final = array_values($result);
        }
        $freshlyItems = $this->paginate($final);
        return $this->sendResponse($freshlyItems, 'Freshly Items');
    }

    public function searchMeals(Request $request){
        $plans = $request->plans;
        $like = $request->keyword;
        if($like == ''){
            // echo 'test';exit;
            $final = $plans;
        }else{
            $result = array_filter($plans, function ($item) use ($like) {
                if (stripos($item['name'], $like) !== false) {
                    return true;
                }elseif (stripos($item['meal_type'], $like) !== false) {
                    return true;
                }
                return false;
            });

            $final = array_values($result);
        }
        $freshlyItems = $this->paginate($final);
        return $this->sendResponse($freshlyItems, 'Freshly Items');
    }
}
