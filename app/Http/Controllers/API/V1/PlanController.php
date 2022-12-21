<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class PlanController extends BaseController
{
    protected $plan = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Plan $plan)
    {
        $this->middleware('auth:api');
        $this->plan = $plan;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = $this->plan->latest()->paginate(10);
        $allPlans = $this->plan->latest()->get()->toArray();
        return $this->sendResponse(['allPlans' => $allPlans, 'plans'=> $plans], 'Freshly Users list');
        // return $this->sendResponse($plans, 'Plan list');
    }

    public function loadPlanTypes()
    {
        $plans = $this->plan->select('id', 'title')->get();
        return $this->sendResponse($plans, 'Plan types list');
    }

    public function store(Request $request)
    {
        $rules = [
            'title'             =>  'required',
            'shortcode'         =>  'required',
            'image'             =>  'required',
            'cover_image'       =>  'required',
            'short_description' =>  'required',
            'long_description'  =>  'required',
            'is_customized'     =>  'required',
        ];

        $customMessages = [
            'title.required'            => 'Plan type name is required !',
            'shortcode.required'        => 'Shortcode is required !',
            'image.required'            => 'Select Thumb type image !',
            'cover_image.required'      => 'Select Cover type image !',
            'short_description.required'=> 'Plan type short description is required !',
            'long_description.required' => 'Plan type long description is required !',
            'is_customized.required'    => 'Select Is Customized Plan !',
        ];

        $this->validate($request, $rules, $customMessages);

        // Upload Thumb Image
        if ($request->image) {
            $name = uniqid() . '.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
            \Image::make($request->image)->save(public_path('images/api_images/') . $name);
            $request->merge(['image' => $name]);
        }

        $plan = new Plan();
        $plan->title              = $request->get('title');
        $plan->image              = $request->get('image');
        $plan->cover_image        = 'NULL';
        $plan->short_description  = $request->get('short_description');
        $plan->long_description   = $request->get('long_description');
        $plan->is_customized      = $request->get('is_customized');
        $plan->price              = 0.00;
        $plan->shortcode	      = $request->get('shortcode');
        $plan->save();

        // Upload Multiple Cover Image
        if ($request->cover_image) {
            $planImages = [];
            foreach ($request->cover_image as $planImage) {
                $name1 = uniqid() . '.' . explode('/', explode(':', substr($planImage, 0, strpos($planImage, ';')))[1])[1];
                \Image::make($planImage)->save(public_path('images/plan_images/') . $name1);
                $planImages[] = $name1;
            }

            // store multiple images in database
            foreach ($planImages as $img) {
                $planimg = new PlanImage();
                $planimg->plan_id = $plan->id;
                $planimg->image = $img;
                $planimg->save();
            }
        }

        $this->synkDynamicPrices($request->input('dynamic_prices'), $plan->id, 'POST');
        return $this->sendResponse($plan, 'Plan Type Created Successfully');
    }

    public function show($id)
    {
        $plan = Plan::findOrFail($id);
        $planImages = $plan->planImages;
        $planVariations =  DB::table('plan_variations')->where(['plan_id'=> $id])->get();
        return $this->sendResponse(['plan' => $plan, 'plan_variations' => $planVariations], 'Plan Type Successfully');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title'             =>  'required',
            'shortcode'         =>  'required',
            'short_description' =>  'required',
            'long_description'  =>  'required',
            'is_customized'     =>  'required',
        ];

        $customMessages = [
            'title.required'            => 'Plan type name is required !',
            'shortcode.required'        => 'Shortcode is required !',
            'short_description.required'=> 'Plan type short description is required !',
            'long_description.required' => 'Plan type long description is required !',
            'is_customized.required'    => 'Select Is Customized Plan !',
        ];

        $this->validate($request, $rules, $customMessages);

        $plan = Plan::findOrFail($id);
        $currentImage = $plan->image;
        $currentImageName = explode("/", $currentImage, 2);

        if (($request->image) && ($request->image != $currentImageName[0])) {
            $parsed = $this->get_string_between($request->image, 'data:image/', ';base64');
            $name = uniqid() . Str::random(5) . '.' . $parsed;
            \Image::make($request->image)->save(public_path('images/api_images/') . $name);
            $request->merge(['image' => $name]);
            $currentImage = public_path('images/api_images/') . $currentImage;
            if (file_exists($currentImage)) {
                @unlink($currentImage);
            }
        }

        // Upload Multiple Cover Image
        if ($request->new_cover_images) {
            $planImages = [];
            foreach ($request->new_cover_images as $planImage) {
                $name1 = uniqid() . '.' . explode('/', explode(':', substr($planImage, 0, strpos($planImage, ';')))[1])[1];
                \Image::make($planImage)->save(public_path('images/plan_images/') . $name1);
                $planImages[] = $name1;
            }

            // store multiple images in database
            foreach ($planImages as $img) {
                $planimg = new PlanImage();
                $planimg->plan_id = $id;
                $planimg->image = $img;
                $planimg->save();
            }
        }

        $plan->title             = $request->title;
        $plan->image             = $request->image;
        $plan->cover_image       = $request->cover_image;
        $plan->short_description = $request->short_description;
        $plan->long_description  = $request->long_description;
        $plan->is_customized      = $request->get('is_customized');
        $plan->price             = 0.00;
        $plan->shortcode         = $request->shortcode;
        $plan->save();

        $this->deleteVariation($id);
        $this->synkDynamicPrices($request->input('dynamic_prices'), $id, 'PUT');
        return $this->sendResponse([$plan], 'Plan Type Updated Successfully');
    }

    public function destroy($id)
    {
        $this->authorize('isAdmin');
        $plan = Plan::findOrFail($id);
        $planImages = $plan->planImages;

        foreach ($planImages as $value) {
            $currentImage = public_path('images/plan_images/') . $value->image;
            if (file_exists($currentImage)) {
                @unlink($currentImage);
            }
            $planimage = PlanImage::findOrFail($value->id);
            $planimage = $planimage->delete();
        }

        //Delete image if plan type deleted
        $currentImage = $plan->image;
        $currentImage = public_path('images/api_images/') . $currentImage;
        if (file_exists($currentImage)) {
            @unlink($currentImage);
        }

        // Delete Dynamic Price
        DB::table('plan_variations')->where(['plan_id'=> $id])->delete();
        DB::table('user_meal_plans')->where(['meal_plan_id'=> $id])->delete();
        DB::table('users_meals')->where(['plan_id'=> $id])->delete();

        $plan->delete();
        return $this->sendResponse([$plan], 'Plan Type has been Deleted');
    }


    public function removeCoverImage($id){
        $plan = PlanImage::findOrFail($id);
        //Delete image if plan type deleted
        $currentImage = $plan->image;
        $currentImage = public_path('images/plan_images/') . $currentImage;
        if (file_exists($currentImage)) {
            @unlink($currentImage);
        }
        $plan->delete();
        return $this->sendResponse($plan, 'Plan Image has been Deleted');
    }
}