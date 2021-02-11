<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return response()->json([
            'status' => 200,
            'customer_id' => $orderData[0]->user_id,
            'delivery_address' => $orderData[0]->delivered_address,
            'created_at' => $orderData[0]->created_at,
            'data' => $data
        ]);
    }
}
