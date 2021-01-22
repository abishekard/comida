<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function createPartner(Request $request)
    {
         $validate = Validator::make($request->all(),[
             'email'=>'required|unique:partner',
             'mobile'=>'required|unique:partner',
             'password'=>'required',
             'name'=>'required'
         ]);

         if($validate->fails())
         {
             return response()->json([
                 'status'=>300,'message'=>$validate->errors()
             ]);
         }

         DB::table('partner')->insert([
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

    public function getPartnerAddress(Request $request)
    {
          $validate = Validator::make($request->all(),[
              'address'=>'required',
              'state'=>'required',
              'city'=>'required',
              'pincode'=>'required',
              'lat'=>'required',
              'long'=>'required',
              'id'=>'required'
          ]);

          if($validate->fails())
          {
              return response()->json([
                  'status'=>300,'message'=>$validate->errors()
              ]);
          }

          DB::table('partner')->where('id',$request->id)->update([
            'address'=>$request->address,
            'state'=>$request->state,
            'city'=>$request->city,
            'pincode'=>$request->pincode,
            'lat'=>$request->lat,
            'long'=>$request->long
        ]);


          return response()->json([
            'status'=>200,'message'=>'success'
        ]);
    }


    public function getPartnerShop(Request $request)
    {
          $validate = Validator::make($request->all(),[
              'shop_name'=>'required',
              'speciality'=>'required',
              'open_time'=>'required',
              'close_time'=>'required',
              'shop_image'=>'required|mimes:png,jpg',
              'id'=>'required'

          ]);

          if($validate->fails())
          {
              return response()->json([
                  'status'=>300,'message'=>$validate->errors()
              ]);
          }
          $name_gen = hexdec(uniqid());
          $shop_img = $request->file('shop_image');
          $img_ext = strtolower($shop_img->getClientOriginalExtension());
          $img_name = $name_gen.'.'.$img_ext;
          $uplocation = 'images/shop_images/';
          $last_img = $uplocation.$img_name;
          $shop_img->move($uplocation,$img_name);

          DB::table('partner')->where('id',$request->id)->update([
              'shop_name'=>$request->shop_name,
              'speciality'=>$request->speciality,
              'open_time'=>$request->open_time,
              'close_time'=>$request->close_time,
              'shop_image'=>$last_img
          ]);


          return response()->json([
            'status'=>200,'message'=>'success'
        ]);
    }
}
