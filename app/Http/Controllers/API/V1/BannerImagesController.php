<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;
use App\Models\BannerImages;
use App\Models\FreshlyAuth;

class BannerImagesController extends BaseController
{
    protected $banner = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BannerImages $banner)
    {
        // $this->middleware('auth:api');
        $this->banner = $banner;
    }

    public function index()
    {
        $banners = $this->banner::latest()->paginate(10);
        return $this->sendResponse($banners, 'Banners list');
    }

    public function store(Request $request)
    {
        $rules = [
            'image'         =>  'required',
        ];

        $customMessages = [
            'image.required' => 'Select Banner image !',
        ];

        $this->validate($request, $rules, $customMessages);

        if ($request->image) {
            $name = time() . '.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
            \Image::make($request->image)->save(public_path('images/api_images/') . $name);
            $request->merge(['image' => $name]);
        }
        $banner = new BannerImages();
        $banner->image        = $request->get('image');
        $banner->save();
        return $this->sendResponse($banner, 'banner Created Successfully');
    }

    public function show($id)
    {
        $banner = BannerImages::find($id);
        return $this->sendResponse($banner, 'Banner Loaded Successfully');
    }

    public function update(Request $request, $id)
    {

        $banner = BannerImages::findOrFail($id);
        $currentImage = $banner->image;
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
        $banner->update($request->all());
        return $this->sendResponse([$banner], 'Banner Updated Successfully');
    }

    public function destroy($id)
    {
        // $this->authorize('isAdmin');
        $banner = BannerImages::findOrFail($id);

        //Delete image if banner deleted
        $currentImage = $banner->image;
        $currentImage = public_path('images/api_images/') . $currentImage;
        if (file_exists($currentImage)) {
            @unlink($currentImage);
        }
        $banner->delete();
        return $this->sendResponse([$banner], 'Banner has been Deleted');
    }

    public function getBannerImages(Request $request){
        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required|exists:freshly_users',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $images = BannerImages::get();
        $fArray = [
            'is_subscribed' => FreshlyAuth::isSubscribed($request->user_id),
            'unread_count'  => (string)FreshlyAuth::getUnreadCount($request->user_id),
            'images'        => $images,
        ];
        return $this->sendResponse($fArray, 'Banner list shown successfully');
    }
}
