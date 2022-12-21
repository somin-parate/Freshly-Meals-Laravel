<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiToken;
use Validator;

class DeviceTokenController extends Controller
{
    public function deviceRegistration(Request $request)
    {   
        $validatedData = Validator::make($request->all(), [
            'device_token'=> 'required',
            'device_type' => 'required',
            'language'    => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error, 401);
        }
        $device_ext = ApiToken::checkDeviceExist($request->device_token);
        if(!empty($device_ext)){
            $msg['STATUS']  = 'true';
            $msg['status']  = 'success';
            $msg['data'][] = array(
                'device_id'    =>  (string)$device_ext['token_id'],
                'device_token' =>  $device_ext['device_token'],
                'device_type'  =>  $device_ext['device_type'],
                'language'     =>  $device_ext['language'],
            );
            return response()->json($msg);
        }
        else{
            $device_reg = ApiToken::deviceReg($request->all());
            if($device_reg > 0){
                $result = ApiToken::getDeviceData($device_reg);
                $msg['message'] = 'Device Registered Successfully';
                $msg['status']  = 'success';
                $msg['data'][] = array(
                    'device_id'    =>  $result['token_id'],
                    'device_token' =>  $result['device_token'],
                    'device_type'  =>  $result['device_type'],
                    'language'     =>  $result['language'],
                );
                return response()->json($msg);
            }
            else{
                $msg['message'] = 'Device Registered Fail';
                $msg['status']  = 'fail';
                $msg['data'] = [];
                return response()->json($msg);
            }
        }
    }   
}
