<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\partnerModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class loginController extends Controller
{
    public function __construct()
    {

     Auth::shouldUse('partner');

    }

    public function login(Request $request){

            $credentials = $request->only('mobile', 'password');
            $token = null;
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    abort(403,'unAuthorized');
                    // return response()->json([
                    //     'response' => 'error',
                    //     'message' => 'invalid_email_or_password',
                    // ]);
                }
            } catch (JWTException $e) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'failed_to_create_token',
                ]);
            }

            $data =DB::table('partner')->where('mobile',$request->mobile)
            ->select('name','email','mobile','shop_name','shop_image',
            'address','pincode')->get();

            return response()->json([
                'response' => 'success',
                'result' => [
                    'type'=>'bearer',
                    'token' => $token,
                    'data' => $data
                ],
            ]);
        }


}
