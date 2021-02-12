<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DOrderController extends Controller
{
    public function newOrder($id)
    {
        $partnerId = DB::table('delivery_partner')->where('id', $id)->select('partner_id')->first();
        if ($partnerId->partner_id == '0') {
            return response()->json([
                'status' => 300,
                'message' => 'no partner registered ' . $partnerId->partner_id
            ]);
        }
        //  return response()->json([$partnerId]);
        $data = DB::table('customer_order_table')->where('partner_id', $partnerId->partner_id)
            ->where('status', '1')->get();
        //  return response()->json([$data]);
        $temp = json_decode($data);
        for ($i = 0; $i < sizeof($temp); $i++) {
            $dataItem = DB::table('customer_order_item')->where('order_id', $temp[$i]->order_id)->select('item_image')->first();
            $temp[$i]->image = $dataItem->item_image;
        }

        return response()->json([
            'status' => 200,
            'data' => array_reverse($temp)
        ]);
    }
    public function inProgressOrder($id)
    {

        $data = DB::table('customer_order_table')->where('delivery_partner_id', $id)
            ->whereIn('status', [2, 3])->get();

        $temp = json_decode($data);
        for ($i = 0; $i < sizeof($temp); $i++) {
            $dataItem = DB::table('customer_order_item')->where('order_id', $temp[$i]->order_id)->select('item_image')->first();
            $temp[$i]->image = $dataItem->item_image;
        }

        return response()->json([
            'status' => 200,
            'data' => array_reverse($temp)
        ]);
    }
    public function completedOrder($id)
    {
        $data = DB::table('customer_order_table')->where('delivery_partner_id', $id)
            ->where('status', '4')->get();

        $temp = json_decode($data);
        for ($i = 0; $i < sizeof($temp); $i++) {
            $dataItem = DB::table('customer_order_item')->where('order_id', $temp[$i]->order_id)->select('item_image')->first();
            $temp[$i]->image = $dataItem->item_image;
        }

        return response()->json([
            'status' => 200,
            'data' => array_reverse($temp)
        ]);
    }

    public function orderDetail($orderId)
    {
        $orderData = DB::table('customer_order_table')->where('order_id', $orderId)->get();
        // return response()->json($orderData);
        $data = DB::table('customer_order_item')->where('order_id', $orderId)
            ->get();
        $lat_lng = explode(' ', $orderData[0]->lat_lng);
        //  return response()->json(['lat'=>$lat_lng[0],'lng'=>$lat_lng[1]]);
        return response()->json([
            'status' => 200,
            'customer_id' => $orderData[0]->user_id,
            'delivery_address' => $orderData[0]->delivered_address,
            'created_at' => $orderData[0]->created_at,
            'latitude' => $lat_lng[0],
            'longitude' => $lat_lng[1],
            'otp' => $orderData[0]->otp,
            'data' => $data
        ]);
    }

    public function orderDelivered(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'order_id' => 'required',
            'otp' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        $otp = DB::table('customer_order_table')->where('order_id', $request->order_id)->select('otp')
            ->first()->otp;


        if ($otp != $request->otp) {
            return response()->json([
                'status' => 350,
                'message' => 'wrong otp'
            ]);
        }

        DB::table('customer_order_table')->where('order_id', $request->order_id)->update([
            'status' => 4
        ]);

        $total = DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('total_price')->first()->total_price;
        $payMethod =  DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('payment_method')->first()->payment_method;
        $partnerId =    DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('partner_id')->first()->partner_id;
        $userId =  DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('user_id')->first()->user_id;
        $partnerAmount = DB::table('partner')->where('id', $partnerId)
            ->select('account_balance')->first()->account_balance;
         if($payMethod=='online')
        DB::table('partner')->where('id', $partnerId)->update([
            'account_balance' => $partnerAmount + $total
        ]);

        // send notification to partner
        $pFcmToken = DB::table('partner')->where('id', $partnerId)->select('fcm')->first()->fcm;
        $pName = DB::table('partner')->where('id', $partnerId)->select('shop_name')->first()->shop_name;
        $pTitle = 'Order Delivered';
        $pBody = 'Dear ' . $pName . ' you order with order-id #' . $request->order_id . ' is successfully delivered.';
        $this->sendNotification($pTitle,$pBody,[$pFcmToken]);

        // send notification to customer
        $cFcmToken = DB::table('users')->where('id', $userId)->select('fcm')->first()->fcm;
        $cName = DB::table('users')->where('id', $userId)->select('name')->first()->name;
        $cTitle = 'Order Delivered';
        $cBody = 'Dear ' . $cName . ' you order with order-id #' . $request->order_id . ' is successfully delivered.';
        $this->sendNotification($cTitle,$cBody,[$cFcmToken]);



        return response()->json([
            'status' => 200,
            'data' => 'order delivered',

        ]);
    }



    public function sendNotification($title, $body, $fcmToken)
    {


        $SERVER_API_KEY = getenv('FCM_API_KEY');

        $data = [
            "registration_ids" => $fcmToken,
            "data" => [
                "title" => $title,
                "body" => $body,
                "image" => ""
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

        //  dd($response);
    }
}
