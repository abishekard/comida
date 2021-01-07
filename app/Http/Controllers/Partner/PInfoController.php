<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PInfoController extends Controller
{


    public function __construct()
    {
        Auth::shouldUse('partner');
        $this->middleware('auth');
    }
    public function showPartner($id)
    {
         $data = DB::table('partner')->where('id',$id)
         ->select('name','address','email','mobile','profile_image'
         ,'shop_name','shop_image','open_time','close_time',
         'available','rating')->get();

         return response()->json(['status'=>200,
         'data'=>$data
         ]);
    }

    public function editPartner(Request $request)
    {

        $validate = Validator::make($request->all(),[
            'id'=>'required'
       ]);
       if($validate->fails())
       {
           return response()->json([
               'status'=>300,
               'message'=>$validate->errors()
           ]);
       }


       if($request->shop_name)
       {
           DB::table('partner')->where('id',$request->id)
           ->update(['shop_name'=>$request->shop_name]);
       }


        if($request->shop_image)
        {
           $validate = Validator::make($request->all(),[
                'shop_image'=>'mimes:png,jpg'
            ]);
            if($validate->fails())
            {
                return response()->json([
                    'status'=>300,
                    'message'=>$validate->errors()
                ]);
            }
            $old_img = DB::table('partner')->where('id',$request->id)
            ->select('shop_image')->get();


           // return response()->json([$old_img]);
            if($old_img[0]->shop_image != null)
            unlink($old_img[0]->shop_image);

            $new_img = $request->file('shop_image');
            $img_name = hexdec(uniqid()).'.'.'jpg';
            $up_location = 'images/shop_images/';
            $new_img->move($up_location,$img_name);




            DB::table('partner')->where('id',$request->id)
            ->update(['shop_image'=>$up_location.$img_name]);


        }




        if($request->profile_image)
        {
            $validate = Validator::make($request->all(),[
                'profile_image'=>'mimes:png,jpg'
            ]);
            if($validate->fails())
            {
                return response()->json([
                    'status'=>300,
                    'message'=>$validate->errors()
                ]);
            }
            $old_img = DB::table('partner')->where('id',$request->id)
            ->select('profile_image')->get();


           // return response()->json([$old_img]);
            if($old_img[0]->profile_image != null)
            unlink($old_img[0]->profile_image);

            $new_img = $request->file('profile_image');
            $img_name = hexdec(uniqid()).'.'.'jpg';
            $up_location = 'images/partner_profile_image/';
            $new_img->move($up_location,$img_name);




            DB::table('partner')->where('id',$request->id)
            ->update(['profile_image'=>$up_location.$img_name]);

        }


        if($request->open_time)
        {
            DB::table('partner')->where('id',$request->id)
            ->update(['open_time'=>$request->open_time]);
        }
        if($request->close_time)
        {
            DB::table('partner')->where('id',$request->id)
            ->update(['close_time'=>$request->close_time]);
        }
        if($request->available)
        {
            DB::table('partner')->where('id',$request->id)
            ->update(['available'=>$request->available]);
        }


        if($request->address)
        {
            $validate = Validator::make($request->all(),[
                'address'=>'required',
                'city'=>'required',
                'pincode'=>'required',
                'lat'=>'required',
                'long'=>'required'
            ]);

            if($validate->fails())
            {
                return response()->json([
                    'status'=>300,
                    'message'=>$validate->errors()
                ]);
            }

            DB::table('partner')->where('id',$request->id)
            ->update(['address'=>$request->address,
            'city'=>$request->city,
            'pincode'=>$request->pincode,
            'lat'=>$request->lat,
            'long'=>$request->long
            ]);
        }

        return response()->json([
            'status'=>200,
            'message'=>'updated'
        ]);
    }
}
