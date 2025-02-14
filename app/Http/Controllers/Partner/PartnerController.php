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
    /*
    //not used
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
    */

    public function getPartnerAddress(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'id' => 'required',
            'local_city' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 300, 'message' => $validate->errors()
            ]);
        }

        DB::table('partner')->where('id', $request->id)->update([
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
            'local_city' => $request->local_city,
            'pincode' => $request->pincode,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);


        return response()->json([
            'status' => 200, 'message' => 'success'
        ]);
    }


    public function getPartnerShop(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'shop_name' => 'required',
            'speciality' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
            'gst_number' => 'required|unique:partner',
            'aadhar_number' => 'required|unique:partner',
            'aadhar_front' => 'required|mimes:png,jpg',
            'aadhar_back' => 'required|mimes:png,jpg',
            'shop_image' => 'required|mimes:png,jpg',
            'id' => 'required'

        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 300, 'message' => $validate->errors()
            ]);
        }
        $name_gen = hexdec(uniqid());
        $shop_img = $request->file('shop_image');
        $img_ext = strtolower($shop_img->getClientOriginalExtension());
        $img_name = $name_gen . '.' . $img_ext;
        $uplocation = 'images/shop_images/';
        $upl = 'public/images/shop_images/';
        $shop_last_img = $uplocation . $img_name;
        $shop_img->move($upl, $img_name);

        $name_gen = hexdec(uniqid());
        $aadhar_front_img = $request->file('aadhar_front');
        $img_ext = strtolower($aadhar_front_img->getClientOriginalExtension());
        $img_name = $name_gen . '.' . $img_ext;
        $uplocation = 'images/partner_document/';
        $upl = 'public/images/partner_document/';
        $aad_fro_last_img = $uplocation . $img_name;
        $aadhar_front_img->move($upl, $img_name);

        $name_gen = hexdec(uniqid());
        $aadhar_back_img = $request->file('aadhar_back');
        $img_ext = strtolower($shop_img->getClientOriginalExtension());
        $img_name = $name_gen . '.' . $img_ext;
        $uplocation = 'images/partner_document/';
        $upl = 'public/images/partner_document/';
        $aad_back_last_img = $uplocation . $img_name;
        $aadhar_back_img->move($upl, $img_name);

        DB::table('partner')->where('id', $request->id)->update([
            'shop_name' => $request->shop_name,
            'speciality' => $request->speciality,
            'open_time' => $request->open_time,
            'close_time' => $request->close_time,
            'shop_image' => $shop_last_img,
            'gst_number' => $request->gst_number,
            'aadhar_number' => $request->aadhar_number,
            'aadhar_front' => $aad_fro_last_img,
            'aadhar_back' => $aad_back_last_img
        ]);


        return response()->json([
            'status' => 200, 'message' => 'success'
        ]);
    }



    // sign and login

    public function CheckAndsendOtpToEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',

        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors());
        }
        $mobileExists =  DB::table('partner')->where('email', $request->email)->first();
        // return response()->json($mobileExists);
        if ($mobileExists) {
            //    DB::table('otp_table')->insert([
            //        'phone'=>$request->phone,
            //        'otp'=>123,
            //        'created_at'=>Carbon::now(),
            //        'updated_at'=>Carbon::now()->addMinute(10)
            //    ]);
            //    return 'otp sent';

            $data = $this->sendOtpEmail($request);
            return $data;
        } else {

            // $this->createNewUser($request);
            return response()->json([
                'status' => 202,
                'message' => 'email does not exist'
            ]);
        }
    }

    public function createNewUser(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:partner',
            'mobile' => 'required|max:10|unique:partner',
            //    'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['status' => 300, 'data' => $validate->errors()]);
        }

        DB::table('partner')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'available' => 0,
            'admin_verified' => 0
            //   'password' => Hash::make($request->password)
        ]);

        $this->sendOtpEmail($request);
        return response()->json(['status' => 200, 'message' => 'customer created and otp sent']);
    }

    public function sendOtpEmail(Request $request)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("abishek.ard@gmail.com", "Comida");
        $email->setSubject("Comida otp for Login");
        $email->addTo($request->email, "Dear Customer");
        //  $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $digits = 4;
        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $email->addContent(
            "text/html",
            "<strong>$otp is your otp for verfication at Comida</strong>"
        );
        $sendgrid = new \SendGrid("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");
        try {
            $response = $sendgrid->send($email);
            //  print $response->statusCode() . "\n";
            //  print_r($response->headers());
            //  print $response->body() . "\n";
            //  print getenv('SENDGRID_API_KEY').'apple';

            DB::table('partner')->where('email', $request->email)->update(['password' => Hash::make($otp)]);
            return response()->json([
                'status' => 200,
                'message' => $response->statusCode() . " mail sent"
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }



    public function storeFcmToken(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
            'fcm' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        $data = DB::table('partner')->where('id', $request->id)->update([
            'fcm' => $request->fcm
        ]);


        return response()->json([
            'status' => 300,
            'message' => 'successful'
        ]);
    }
}
