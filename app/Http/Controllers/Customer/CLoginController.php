<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class CLoginController extends Controller
{


    //not used
    public function loginWithOtp(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'otp' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors());
        }

        $mobileExists =  DB::table('users')->where('email', $request->email)->first();
        if ($mobileExists) {

            $databaseTime = Carbon::parse($mobileExists->updated_at);
            // $currentTime =$mobileExists->created_at;
            $endTime = Carbon::now();
            if ($databaseTime->lt($endTime) && $mobileExists->otp == $request->otp) {

                $this->login();
                //  return response()->json(['status'=>200,'message'=>'verified']);
            } else {
                return response()->json(['status' => 200, 'message' => 'wrong otp']);
                // return response()->json([$databaseTime,$endTime]);
            }
            // return response()->json($mobileExists->created_at);
        } else {
            return response()->json(['status' => 200, 'message' => 'wrong otp']);
        }
    }



    public function CheckAndsendOtpToEmail(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',

        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors());
        }
        $mobileExists =  DB::table('users')->where('email', $request->email)->first();
        // return response()->json($mobileExists);
        if ($mobileExists) {
            //    DB::table('otp_table')->insert([
            //        'phone'=>$request->phone,
            //        'otp'=>123,
            //        'created_at'=>Carbon::now(),
            //        'updated_at'=>Carbon::now()->addMinute(10)
            //    ]);
            //    return 'otp sent';

           $data=$this->sendOtpEmail($request);
           return $data;

        } else {

           // $this->createNewUser($request);
            return response()->json([
                'status'=>202,
                'message'=>'email does not exist'
            ]);
        }
    }

    public function createNewUser(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'mobile' => 'required|max:10|unique:users',
        //    'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json(['status'=>300,'data'=>$validate->errors()]);
        }

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
         //   'password' => Hash::make($request->password)
        ]);

        $this->sendOtpEmail($request);
        return response()->json(['status' => 200, 'message' => 'customer created and otp sent']);
    }

    public function sendOtpEmail(Request $request)
    {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("abishek@androasu.in", "Comida");
        $email->setSubject("Comida otp for Login");
        $email->addTo($request->email, "Dear Customer");
        //  $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $digits = 4;
        $otp= rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $email->addContent(
            "text/html",
            "<strong>$otp is your otp for verfication at Comida</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            //  print $response->statusCode() . "\n";
            //  print_r($response->headers());
            //  print $response->body() . "\n";
            //  print getenv('SENDGRID_API_KEY').'apple';

            DB::table('users')->where('email',$request->email)->
            update(['password'=>Hash::make($otp)]);
            return response()->json([
                'status' => 200,
                'message' => $response->statusCode() . " mail sent"
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }

}
