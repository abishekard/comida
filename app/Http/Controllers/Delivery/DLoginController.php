<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;

class DLoginController extends Controller
{
    public function __construct()
    {

        Auth::shouldUse('delivery');
    }

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                abort(403, 'unAuthorized');
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

        $data = DB::table('delivery_partner')->where('email', $request->email)
            ->select(
                'id',
                'name',
                'email',
                'mobile',
                'profile_image',
                'address',
                'pincode'
            )->get();

        return response()->json([
            'token_type' => 'bearer',
            'access_token' => $token,
            'data' =>$data
            ,
        ]);
    }
}
