<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CInfoController extends Controller
{
    public function showProfile($id)
    {
        $data = DB::table('users')->where('id', $id)
            ->select('name', 'email', 'mobile', 'profile_image')->get();

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

        if ($request->name) {
            DB::table('users')->where('id', $request->id)
                ->update(['name' => $request->name]);
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
            $old_img = DB::table('users')->where('id', $request->id)
                ->select('profile_image')->get();


            // return response()->json([$old_img]);
            if ($old_img[0]->profile_image != null)
                unlink($old_img[0]->profile_image);

            $new_img = $request->file('profile_image');
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            $up_location = 'public/images/customer_profile_image/';
            $upl = 'images/customer_profile_image/';
            $new_img->move($up_location, $img_name);




            DB::table('users')->where('id', $request->id)
                ->update(['profile_image' => $upl . $img_name]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'updated'
        ]);
    }



    public function showAddress($id)
    {
        $data = DB::table('customeraddresstable')->where('user_id', $id)
            ->select('id', 'address', 'state', 'city', 'pincode',
             'landmark','locality','latitude','longitude')->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function editAddress(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'landmark' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'locality' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        DB::table('customeraddresstable')
            ->where('id', $request->id)->update([
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'landmark' => $request->landmark,
                'locality' => $request->locality,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'updated_at' => Carbon::now()
            ]);

        return response()->json([
            'status' => 200,
            'message' => 'updated'
        ]);
    }

    public function deleteAddress($id)
    {
        $isDeleted=DB::table('customeraddresstable')->where('id',$id)->delete();
        if($isDeleted)
        {
            return response()->json([
                'status' => 200,
                'message' => 'deleted'
            ]);
        }


    }
}
