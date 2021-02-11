<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DInfoController extends Controller
{
    public function __construct()
    {
        Auth::shouldUse('delivery');
        $this->middleware('auth');
    }

    public function showProfile(Request $request)
    {
        $validate= Validator::make($request->all(),[
            'id'=>'required'
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'message'=>$validate->errors()
            ]);
        }

        $data=DB::table('delivery_partner')->where('id',$request->id)->select('name','email',
        'profile_image','available','aadhar_number','mobile','dob'
        )->get();

        return response()->json([
            'status'=>200,
            'data'=>$data
        ]);
    }
}
