<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateCustomer extends Controller
{
    public function create(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|unique:users',
            'mobile'=>'required|max:10|unique:users',
            'password'=>'required'
        ]);
        if($validate->fails())
        {
            return response()->json($validate->errors());
        }

        DB::table('users')->insert([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'password'=>Hash::make($request->password)
        ]);

        return response()->json(['status'=>200,'message'=>'customer created']);
    }
}
