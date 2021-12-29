<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $validate = Validator::make(request(['email', 'password']), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors());
        }
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


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



    public function sendOtpToEmail(Request $request)
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

            $this->emailOtpVerify($request);
        } else {

            // $this->createNewUser($request);
            return 'email number not exist';
        }
    }

    public function createNewUser(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'mobile' => 'required|max:10|unique:users',
            'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors());
        }

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password)
        ]);

        $this->sendOtpToEmail($request);
        return response()->json(['status' => 200, 'message' => 'customer created and otp sent']);
    }

    public function emailOtpVerify(Request $request)
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
            "<strong>1234 is your $otp for verfication at Comida</strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";

            DB::table('users')->where('email', $request->email)->update(['otp' => $otp]);
            return response()->json([
                'status' => 200,
                'message' => $response->statusCode() . " mail sent"
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $data = DB::table('users')->where('id', Auth::user()->id)
            ->select(['id', 'name', 'email', 'mobile', 'fcm', 'profile_image'])->get();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'data' => response()->json($data)->original[0]

        ]);
    }
}
