<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class POrderController extends Controller
{
    public function newOrder($id)
    {
        $data = DB::table('customer_order_table')->where('partner_id', $id)
            ->where('status', '1')->get();

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
        $data = DB::table('customer_order_table')->where('partner_id', $id)
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
        $data = DB::table('customer_order_table')->where('partner_id', $id)
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

        return response()->json([
            'status' => 200,
            'customer_id' => $orderData[0]->user_id,
            'delivery_address' => $orderData[0]->delivered_address,
            'created_at' => $orderData[0]->created_at,
            'data' => $data
        ]);
    }
    public function queueOrder(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }
        DB::table('customer_order_table')->where('order_id', $request->order_id)->update([
            'status' => 2
        ]);

        return response()->json([
            'status' => 200,
            'data' => 'order queued'
        ]);
    }
    public function dispatchOrder(Request $request)
    {


        $validate = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }
        DB::table('customer_order_table')->where('order_id', $request->order_id)->update([
            'status' => 3
        ]);
        $userId = DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('user_id')->first();
        $customerInfo = DB::table('users')->where('id', $userId->user_id)->select('fcm', 'name')->first();

        $fcmToken = $customerInfo->fcm;
        $title = 'Out For Delivery';
        $body = 'Dear ' . $customerInfo->name . ' Your order with order-Id #'
            . $request->order_id . ' is Out for delivery. Your order will be at your door-step  in few minutes';

        $this->sendOrderNotification($title, $body, [$fcmToken]);
        return response()->json([
            'status' => 200,
            'data' => 'order dispatched'
        ]);
    }

    public function orderDelivered(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }
        DB::table('customer_order_table')->where('order_id', $request->order_id)->update([
            'status' => 4
        ]);

        $total = DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('total_price')->first()->total_price;
        $partnerId =    DB::table('customer_order_table')->where('order_id', $request->order_id)
            ->select('partner_id')->first()->partner_id;
        $partnerAmount = DB::table('partner')->where('id',$partnerId)
            ->select('account_balance')->first()->account_balance;
        DB::table('partner')->where('id',$partnerId)->update([
            'account_balance'=>number_format($partnerAmount)+number_format($total)
        ]);
        return response()->json([
            'status' => 200,
            'data' => 'order delivered'
        ]);
    }


    public function sendOrderNotification($title, $body, $fcmToken)
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
