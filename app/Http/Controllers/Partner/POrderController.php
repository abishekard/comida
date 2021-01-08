<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POrderController extends Controller
{
    public function newOrder($id)
    {
         $data = DB::table('customer_order_table')->where('partner_id',$id)
         ->where('status','new')->get();

         return response()->json([
             'status'=>200,
             'data'=>$data
         ]);

    }
    public function cancelledOrder($id)
    {
        $data = DB::table('customer_order_table')->where('partner_id',$id)
         ->where('status','cancelled')->get();

         return response()->json([
             'status'=>200,
             'data'=>$data
         ]);
    }
    public function completedOrder($id)
    {
        $data = DB::table('customer_order_table')->where('partner_id',$id)
        ->where('status','completed')->get();

        return response()->json([
            'status'=>200,
            'data'=>$data
        ]);
    }

    public function orderDetail($orderId)
    {
        $data = DB::table('customer_order_item')->where('order_id',$orderId)
        ->get();

        return response()->json([
            'status'=>200,
            'data'=>$data
        ]);
    }
    public function cancelledOrderDetail($orderId)
    {

    }
    public function completedOrderDetail($orderId)
    {

    }
}
