<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
   
class ImageController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $imageName = rand(10,10000000).time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('testimages'), $imageName);
        return response()->json(['success'=>'You have successfully upload file.']);
    }
}