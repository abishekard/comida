<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DInfoController extends Controller
{
    public function __construct()
    {
        Auth::shouldUse('delivery');
        $this->middleware('auth');
    }

    public function showProfile(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        $data = DB::table('delivery_partner')->where('id', $request->id)->select(
            'name',
            'email',
            'profile_image',
            'available',
            'aadhar_number',
            'mobile',
            'dob'
        )->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }


    public function editProfile(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        if ($request->profile_image) {
            $validate = Validator::make($request->all(), [
                'profile_image' => 'mimes:png,jpg'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 300,
                    'message' => $validate->errors()
                ]);
            }
            $old_img = DB::table('partner')->where('id', $request->id)
                ->select('profile_image')->get();


            // return response()->json([$old_img]);
            if ($old_img[0]->profile_image != null)
                unlink('public/' . $old_img[0]->profile_image);

            $new_img = $request->file('profile_image');
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            $up_location = 'images/delivery_partner_profile_image/';
            $upl = 'public/images/delivery_partner_profile_image/';
            $new_img->move($upl, $img_name);


            DB::table('delivery_partner')->where('id', $request->id)
                ->update(['profile_image' => $up_location . $img_name]);
        }

        if ($request->available == 0 || $request->available == 1) {
            DB::table('delivery_partner')->where('id', $request->id)
                ->update(['available' => $request->available]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'updated'
        ]);
    }
}
