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
         $data = DB::table('customer_order_table')->where('partner_id',$id)
         ->where('status','1')->get();

         $temp = json_decode($data);
         for ($i = 0; $i < sizeof($temp); $i++) {
             $dataItem = DB::table('customer_order_item')->where('order_id', $temp[$i]->order_id)->select('item_image')->first();
             $temp[$i]->image = $dataItem->item_image;
         }

         return response()->json([
             'status'=>200,
             'data'=>array_reverse($temp)
         ]);

    }
    public function inProgressOrder($id)
    {
        $data = DB::table('customer_order_table')->where('partner_id',$id)
         ->whereIn('status',[2,3])->get();

         $temp = json_decode($data);
         for ($i = 0; $i < sizeof($temp); $i++) {
             $dataItem = DB::table('customer_order_item')->where('order_id', $temp[$i]->order_id)->select('item_image')->first();
             $temp[$i]->image = $dataItem->item_image;
         }

         return response()->json([
             'status'=>200,
             'data'=>array_reverse($temp)
         ]);
    }
    public function completedOrder($id)
    {
        $data = DB::table('customer_order_table')->where('partner_id',$id)
        ->where('status','4')->get();

        $temp = json_decode($data);
        for ($i = 0; $i < sizeof($temp); $i++) {
            $dataItem = DB::table('customer_order_item')->where('order_id', $temp[$i]->order_id)->select('item_image')->first();
            $temp[$i]->image = $dataItem->item_image;
        }

        return response()->json([
            'status'=>200,
            'data'=>array_reverse($temp)
        ]);
    }

    public function orderDetail($orderId)
    {
        $orderData=DB::table('customer_order_table')->where('order_id',$orderId)->get();
       // return response()->json($orderData);
        $data = DB::table('customer_order_item')->where('order_id',$orderId)
        ->get();

        return response()->json([
            'status'=>200,
            'customer_id'=>$orderData[0]->user_id,
            'delivery_address'=>$orderData[0]->delivered_address,
            'created_at'=>$orderData[0]->created_at,
            'data'=>$data
        ]);
    }
    public function queueOrder(Request $request)
    {
        $validate= Validator::make($request->all(),[
            'order_id'=>'required'
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'message'=>$validate->errors()
            ]);
        }
          DB::table('customer_order_table')->where('order_id',$request->order_id)->update([
              'status'=>2
          ]);

          return response()->json([
            'status'=>200,
            'data'=>'order queued'
        ]);

    }
    public function dispatchOrder(Request $request)
    {


        $validate= Validator::make($request->all(),[
            'order_id'=>'required'
        ]);
        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'message'=>$validate->errors()
            ]);
        }
        DB::table('customer_order_table')->where('order_id',$request->order_id)->update([
            'status'=>3
        ]);

        return response()->json([
          'status'=>200,
          'data'=>'order dispatched'
      ]);
    }
}
