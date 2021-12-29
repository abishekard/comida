<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class commentAndRatingController extends Controller
{
    public function setComment(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required',
            'partner_id' => 'required',
            'order_id' => 'required|unique:comment_and_rating',
            'rating' => 'required',
            'comment' => 'required',
            'image_1' => 'mimes:png,jpg',
            'image_2' => 'mimes:png,jpg',
            'image_3' => 'mimes:png,jpg',
            'image_4' => 'mimes:png,jpg'

        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'msg' => $validate->errors()
            ]);
        }


        $upl_1 = "";
        $upl_2 = "";
        $upl_3 = "";
        $upl_4 = "";
        $new_img = $request->file('image_1');
        if ($new_img) {
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            $up_location = 'public/images/comment_image/';
            // $up_location = 'images/comment_image/';
            $upl = 'images/comment_image/';
            $new_img->move($up_location, $img_name);
            $upl_1 = $upl . $img_name;
        }

        $new_img = $request->file('image_2');
        if ($new_img) {
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            $up_location = 'public/images/comment_image/';
            //  $up_location = 'images/comment_image/';
            $upl = 'images/comment_image/';
            $new_img->move($up_location, $img_name);
            $upl_2 = $upl . $img_name;
        }

        $new_img = $request->file('image_3');
        if ($new_img) {
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            $up_location = 'public/images/comment_image/';
            //  $up_location = 'images/comment_image/';
            $upl = 'images/comment_image/';
            $new_img->move($up_location, $img_name);
            $upl_3 = $upl . $img_name;
        }

        $new_img = $request->file('image_4');
        if ($new_img) {
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            $up_location = 'public/images/comment_image/';
            //  $up_location = 'images/comment_image/';
            $upl = 'images/comment_image/';
            $new_img->move($up_location, $img_name);
            $upl_4 = $upl . $img_name;
        }

        DB::table('comment_and_rating')->insert([
            'user_id' => $request->user_id,
            'partner_id' => $request->partner_id,
            'order_id' => $request->order_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'image1' => $upl_1,
            'image2' => $upl_2,
            'image3' => $upl_3,
            'image4' => $upl_4
        ]);

        DB::table('customer_order_table')->where('order_id', $request->order_id)->update([
            'comment' => 1
        ]);

        return response()->json([
            'status' => 200,
            'msg' => 'successful'
        ]);
    }


    public function getCommentForPartner(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'partner_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'msg' => $validate->errors()
            ]);
        }

        $data = DB::table('comment_and_rating')->where('partner_id', $request->partner_id)->get();

        $temp = json_decode($data);
        for ($i = 0; $i < sizeof($temp); $i++) {
            $user = DB::table('users')->where('id', $temp[$i]->user_id)->select('name', 'profile_image')->first();
            $temp[$i]->customer_name = $user->name;
            $temp[$i]->customer_image = $user->profile_image;
        }

        return response()->json([
            'status' => 200,
            'data' => $temp
        ]);
    }
    public function getCommentForUser(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'msg' => $validate->errors()
            ]);
        }

        $data = DB::table('comment_and_rating')->where('order_id', $request->order_id)->get();

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function testEmail(Request $request)
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
        $sendgrid = new \SendGrid("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");
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";

            DB::table('users')->where('email', $request->email)->update(['otp' => $otp]);
            return response()->json([
                'status' => 200,
                'message' => $response->statusCode() . " mail sent",
                'error' => $response->body()
            ]);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}
