<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PInfoController extends Controller
{


    // public function __construct()
    // {
    //     Auth::shouldUse('partner');
    //     $this->middleware('auth');
    // }
    public function showPartner($id)
    {
         $data = DB::table('partner')->where('id',$id)
         ->select('name','address','email','mobile','profile_image'
         ,'shop_name','shop_image','open_time','close_time',
         'available','rating','aadhar_number','gst_number','aadhar_front',
         'aadhar_back','speciality')->get();

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
            //server
            unlink('public/'.$old_img[0]->shop_image);
            //local
          //  unlink($old_img[0]->shop_image);

            $new_img = $request->file('shop_image');
            $img_name = hexdec(uniqid()).'.'.'jpg';
            $up_location = 'images/shop_images/';
            $upl = 'public/images/shop_images/';
            $new_img->move($upl,$img_name);




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
            unlink('public/'.$old_img[0]->profile_image);

            $new_img = $request->file('profile_image');
            $img_name = hexdec(uniqid()).'.'.'jpg';
            $up_location = 'images/partner_profile_image/';
            $upl='public/images/partner_profile_image/';
            $new_img->move($upl,$img_name);




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
        if($request->available==0||$request->available==1)
        {
            DB::table('partner')->where('id',$request->id)
            ->update(['available'=>$request->available]);
        }
        if($request->speciality)
        {
            DB::table('partner')->where('id',$request->id)
            ->update(['speciality'=>$request->speciality]);
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

    public function getDeliveryPartner(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id'=>'required'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'messager'=>$validate->errors()
            ]);
        }

        $data=DB::table('delivery_partner')->where('partner_id',$request->id)
        ->select('name','id','mobile','profile_image','aadhar_number','available')->get();

        return response()->json([
            'status'=>200,
            'data'=>$data
        ]);
    }

    public function addDeliveryPartner(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'mobile'=>'required',
            'partner_id'=>'required'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'messager'=>$validate->errors()
            ]);
        }

        $temp=DB::table('delivery_partner')->where('mobile',$request->mobile)->select('name')->get();
        if(sizeof($temp)==0)
        {
            return response()->json([
                'status'=>350,
                'data'=>'no delivery partner found'
            ]);
        }
        DB::table('delivery_partner')->where('mobile',$request->mobile)->update([
              'partner_id'=>$request->partner_id
        ]);

        return response()->json([
            'status'=>200,
            'data'=>'delivery partner registered'
        ]);
    }

    public function removeDeliveryPartner(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'delivery_partner_id'=>'required',

        ]);

        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'messager'=>$validate->errors()
            ]);
        }


        DB::table('delivery_partner')->where('id',$request->delivery_partner_id)
        ->update(['partner_id'=>0]);

        return response()->json([
            'status'=>200,
            'data'=>'delivery partner removed'
        ]);
    }
}
