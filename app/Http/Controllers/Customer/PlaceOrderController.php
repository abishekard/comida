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

        ]);
        if ($validate->fails()) {
            return response()->json(['status' => 300, 'message' => $validate->errors()]);
        }
        $pIdArray = explode(',', $request->product_id);
        $quantity = explode(',', $request->quantity);

        //    return response()->json(['pid'=>$pIdArray,'qty'=>$quantity]);
        $orderId = hexdec(uniqid());

        $addressData = DB::table('customeraddresstable')->where('id', $request->address_id)->get();

      //  return response()->json($addressData);





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
            'status' => 0
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

        return response()->json(['status' => 200, 'orderId' => $orderId]);
    }

    public function getOrders($id)
    {
        $data = DB::table('customer_order_table')->where('user_id', $id)
            ->select('order_id', 'customer_address_id', 'status', 'created_at', 'total_price', 'partner_id')
            ->get();

        //  $orderData = DB::table('customer_order_item')->where('order_id',$data[0]->order_id ->get();
        return response()->json($data);
    }

    public function getOrderDetails($orderId)
    {
        $data = DB::table('customer_order_item')->where('order_id', $orderId)
            ->select('discount', 'quantity', 'price', 'created_at', 'item_name', 'item_image', 'price_type')->get();

        return response()->json($data);
    }
}
