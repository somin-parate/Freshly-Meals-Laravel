<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Offer;

class OfferController extends BaseController
{
    protected $offer = '';

    public function __construct(Offer $offer)
    {
        // $this->middleware('auth:api');
        $this->offer = $offer;
    }

    public function index()
    {
        $offers = $this->offer::latest()->paginate(10);
        $allOffers1 = $this->offer::latest()->get();
        foreach ($allOffers1 as $value) {
            $value['start_date']  = date("d M, Y", strtotime($value['start_date']));
            $value['end_date']  = date("d M, Y", strtotime($value['end_date']));
        }
        $allOffers = $allOffers1->toArray();
        return $this->sendResponse($offers, 'Offers list');
    }

    public function store(Request $request)
    {
        // echo '<pre>';print_r($request->all());exit;
        $request->start_date = date("Y-m-d", strtotime($request->start_date));
        $request->end_date = date("Y-m-d", strtotime($request->end_date));
        $rules = [
            'title'         =>  'required',
            'description'   =>  'required',
            'image'         =>  'required',
            'coupon_code'   =>  'required',
            'coupon_type'   =>  'required',
            'amount'   =>  'required',
        ];

        $customMessages = [
            'title.required'             => 'Offer title is required !',
            'description.required'       => 'Description is required !',
            'image.required'             => 'Select offer image !',
            'coupon_code.required'       => 'Coupon Code is required !',
            'amount.required'       => 'Coupon Amount is required !',
            'coupon_type.required'       => 'Coupon Type is required !',
        ];

        $this->validate($request, $rules, $customMessages);

        if ($request->image) {
            $name = time() . '.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
            \Image::make($request->image)->save(public_path('images/api_images/') . $name);
            $request->merge(['image' => $name]);
        }
        $offer = new Offer();
        $offer->title       = $request->get('title');
        $offer->description = $request->get('description');
        $offer->start_date  = $request->start_date;
        $offer->end_date    = $request->end_date;
        $offer->image       = $request->get('image');
        $offer->coupon_code = $request->get('coupon_code');
        $offer->coupon_type = $request->get('coupon_type');
        $offer->customer_email = $request->get('customer_email');
        $offer->amount      = $request->get('amount');
        $offer->save();
        return $this->sendResponse($offer, 'Offer Created Successfully');
    }

    public function show($id)
    {
        $offer = Offer::find($id);
        return $this->sendResponse($offer, 'Offer Loaded Successfully');
    }

    public function update(Request $request, $id)
    {
        // echo '<pre>';print_r($request->all());exit;
        $rules = [
            'title'         =>  'required',
            'description'   =>  'required',
            'coupon_code'   =>  'required',
            'coupon_type'   =>  'required',
            'amount'   =>  'required',
        ];

        $customMessages = [
            'title.required'             => 'Offer title is required !',
            'description.required'       => 'Description is required !',
            'coupon_code.required'       => 'Coupon Code is required !',
            'coupon_type.required'       => 'Coupon Type is required !',
            'amount.required'       => 'Coupon Amount is required !',
        ];

        $this->validate($request, $rules, $customMessages);

        $offer = Offer::findOrFail($id);
        $currentImage = $offer->image;
        $currentImageName = explode("/", $currentImage, 2);

        if (($request->image) && ($request->image != $currentImageName[0])) {
            $parsed = $this->get_string_between($request->image, 'data:image/', ';base64');
            $name = time() . Str::random(5) . '.' . $parsed;
            \Image::make($request->image)->save(public_path('images/api_images/') . $name);
            $request->merge(['image' => $name]);
            $currentImage = public_path('images/api_images/') . $currentImage;
            if (file_exists($currentImage)) {
                @unlink($currentImage);
            }
        }
        $offer->update($request->all());
        return $this->sendResponse([$offer], 'Offer Updated Successfully');
    }

    public function destroy($id)
    {
        // $this->authorize('isAdmin');
        $offer = Offer::findOrFail($id);

        //Delete image if offer deleted
        $currentImage = $offer->image;
        $currentImage = public_path('images/api_images/') . $currentImage;
        if (file_exists($currentImage)) {
            @unlink($currentImage);
        }
        $offer->delete();
        return $this->sendResponse([$offer], 'Offer has been Deleted');
    }

    public function getOffers(Request $request)
    {
        try {
            $getOffers = Offer::getOffersList();
            if ($getOffers) {
                $getList = [];
                $i = 0;
                foreach ($getOffers as $category) {
                    $getList[$i]['id']              = $category->id;
                    $getList[$i]['title']           = $category->title;
                    $getList[$i]['description']     = $category->description;
                    $getList[$i]['image']           = $category->image;
                    $getList[$i]['coupon_code']     = $category->coupon_code;
                    $i++;
                }
                $msg['message'] = 'Offers List Shown Successfully';
                $msg['status']  = 'success';
                $msg['data']    = $getList;
                return response()->json($msg);
            } else {
                return response()->json(['message' => 'Offers List Empty', 'status' => 'fail', 'data' => $msg]);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unable To Fetch Offers List', 'status' => 'fail', 'data' => []]);
        }
    }
}