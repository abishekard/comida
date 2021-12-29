<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PlaceOrderController extends Controller
{
    public function PlaceOrder(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required',
            'address_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
            'total_price' => 'required',
            'partner_id' => 'required',
            'payment_method' => 'required',
            'order_id' => 'required'

        ]);
        if ($validate->fails()) {
            return response()->json(['status' => 300, 'message' => $validate->errors()]);
        }
        $pIdArray = explode(',', $request->product_id);
        $quantity = explode(',', $request->quantity);

        //    return response()->json(['pid'=>$pIdArray,'qty'=>$quantity]);
        //  $orderId = round(hexdec(uniqid()) / 100);
        // $orderId = round(microtime(true) * 100);
        $orderId = $request->order_id;

        $addressData = DB::table('customeraddresstable')->where('id', $request->address_id)->get();

        //  return response()->json($addressData);

        $digits = 4;
        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);



        DB::table('customer_order_table')->insert([
            'user_id' => $request->user_id,
            'customer_address_id' => $request->address_id,
            'created_at' => Carbon::now(),
            'total_price' => $request->total_price,
            'order_id' => $orderId,
            'partner_id' => $request->partner_id,
            'address_type' => $addressData[0]->address_type,
            'delivered_address' => $addressData[0]->address,
            'lat_lng' => $addressData[0]->latitude . " " . $addressData[0]->longitude,
            'status' => 1,
            'week' => Carbon::now()->weekOfMonth,
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year,
            'date' => Carbon::now()->toDateString(),
            'otp' => $otp,
            'payment_method' => $request->payment_method
        ]);


        //  return response()->json(DB::table('product_table')->where('id',$pIdArray[0])->select('item_name')->first());
        for ($i = 0; $i < count($pIdArray); $i++) {
            $item_name = DB::table('product_table')->where('id', $pIdArray[$i])->select('item_name')->first();
            $item_image = DB::table('product_table')->where('id', $pIdArray[$i])->select('item_image')->first();
            $price_type = DB::table('product_table')->where('id', $pIdArray[$i])->select('price_type')->first();
            $price = DB::table('product_table')->where('id', $pIdArray[$i])->select('price')->first();
            $discount = DB::table('product_table')->where('id', $pIdArray[$i])->select('discount')->first();
            DB::table('customer_order_item')->insert([
                'order_id' => $orderId,
                'product_id' => $pIdArray[$i],
                'quantity' => $quantity[$i],
                'created_at' => Carbon::now(),
                'item_name' => $item_name->item_name,
                'item_image' => $item_image->item_image,
                'price_type' => $price_type->price_type,
                'price' => $price->price,
                'discount' => $discount->discount
            ]);
        }



        $fcmToken = DB::table('partner')->where('id', $request->partner_id)->pluck('fcm');
        $shop_name = DB::table('partner')->where('id', $request->partner_id)->select('shop_name')->get()[0]->shop_name;
        $title = 'New Order Placed';
        $body = 'Dear ' . $shop_name . ' , ' . 'You have new order #' . $orderId . ' for Rs ' . $request->total_price;
        $this->sendOrderNotification($title, $body, $fcmToken);

        $delFcmToken = DB::table('delivery_partner')->where('partner_id', $request->partner_id)->pluck('fcm');
        $delTitle = "New Order Placed";
        $delBody = "Dear , Delivery Partner. New Order with order-id #" . $orderId . " Placed with Partner Restaurant";
        if (sizeof($delFcmToken) > 0)
            $this->sendOrderNotification($delTitle, $delBody, $delFcmToken);


        $CusFcmToken = DB::table('users')->where('id', $request->user_id)->pluck('fcm');
        $Customer_name = DB::table('users')->where('id', $request->user_id)->select('name')->get()[0]->name;
        $CusTitle = 'Order Confirmed';
        $CusBody = 'Dear ' . $Customer_name . ' , ' . 'Your order with order-Id #' . $orderId . ' has been confirmed.';
        $this->sendConfirmationNotification($CusTitle, $CusBody, $CusFcmToken);



        return response()->json(['status' => 200, 'orderId' => $orderId]);
    }

    public function getNewOrders($id)
    {
        $data = DB::table('customer_order_table')->where('user_id', $id)->whereIn('status', [1, 2, 3])
            ->select(
                'order_id',
                'delivered_address',
                'customer_address_id',
                'status',
                'created_at',
                'total_price',
                'partner_id'
            )
            ->get();

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

    public function getCompletedOrders($id)
    {
        $data = DB::table('customer_order_table')->where('user_id', $id)->where('status', 4)
            ->select('order_id', 'delivered_address', 'customer_address_id', 'status', 'created_at', 'total_price', 'partner_id')
            ->get();
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

    public function getOrderDetails($orderId)
    {
        $mainData = DB::table('customer_order_table')->where('order_id', $orderId)->select(
            'partner_id',
            'total_price',
            'status',
            'address_type',
            'delivered_address',
            'order_id',
            'otp',
            'comment'
        )->get();
        $partnerData = DB::table('partner')->where('id', $mainData[0]->partner_id)->select(
            'shop_name',
            'shop_image',
            'speciality',
            'address'
        )->get();

        $data = DB::table('customer_order_item')->where('order_id', $orderId)
            ->select('discount', 'quantity', 'price', 'created_at', 'item_name', 'item_image', 'price_type')->get();

        // return response()->json($partnerData);

        return response()->json([
            'status' => 200,
            'partner_id' => $mainData[0]->partner_id,
            'status' => $mainData[0]->status,
            'address_type' => $mainData[0]->address_type,
            'otp' => $mainData[0]->otp,
            'delivered_address' => $mainData[0]->delivered_address,
            'order_id' => $mainData[0]->order_id,
            'shop_name' => $partnerData[0]->shop_name,
            'shop_image' => $partnerData[0]->shop_image,
            'speciality' => $partnerData[0]->speciality,
            'shop_address' => $partnerData[0]->address,
            'comment' => $mainData[0]->comment,
            'orders' => $data
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


    public function sendConfirmationNotification($title, $body, $fcmToken)
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
