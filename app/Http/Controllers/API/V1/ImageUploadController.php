<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImageUpload;
use Validator;
use URL;

class ImageUploadController extends BaseController
{
    public function uploadImage(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'image'  => 'required',
            'type'   => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error, 401);
        }

        if($request->hasfile('image'))
        {
            //Upload Image In DestinationPath
            $file = $request->file('image');
            $filename = uniqid().$file->getClientOriginalName(); 
            $iparr=  $file->getClientOriginalName();
            $split = explode(".", $iparr);
            $name = $split[0];
            $destinationPath = public_path('/images/user');
            $file->move($destinationPath, $filename);

            // //Insert Image Data
            $image_data = new ImageUpload();
            $image_data->user_id      = NULL;
            $image_data->image        = $filename;
            $image_data->type         = $request->type;
            $image_data->created_at   = date('Y-m-d h:i:s');
            $image_data->save();

            if(!empty($image_data)){
                $image = array(
                    'image'     => $filename,
                    'type'      => $request->type,
                    'filename'  => $name,
                );
                return $this->sendResponse([$image], 'Image uploaded Successfully!');
            }else{
                return $this->sendError([], 'Image upload failed');
            }
        }
        else{
            return $this->sendError([], 'Image upload failed');
        }
    }
}
