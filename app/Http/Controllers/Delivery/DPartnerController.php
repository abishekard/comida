<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DPartnerController extends Controller
{
    public function createPartner(Request $request)
    {
         $validate = Validator::make($request->all(),[
             'email'=>'required|unique:delivery_partner',
             'mobile'=>'required|unique:delivery_partner',
             'password'=>'required',
             'name'=>'required'
         ]);

         if($validate->fails())
         {
             return response()->json([
                 'status'=>300,'message'=>$validate->errors()
             ]);
         }

         DB::table('delivery_partner')->insert([
               'email'=>$request->email,
               'password'=>Hash::make($request->password),
               'mobile'=>$request->mobile,
               'name'=>$request->name,
               'created_at'=>Carbon::now()
         ]);
         return response()->json([
             'status'=>200,
             'message'=>'registered'
         ]);
    }
}
