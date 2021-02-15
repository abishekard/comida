<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DeliveryController extends Controller
{
    public function currentDeliveryReport(Request $request)
    {

        $validate=Validator::make($request->all(),[
             'delivery_partner_id'=>'required'
        ]);

        if($validate->fails())
        {

            return response()->json([
                'status'=>300,
                'message'=>$validate->errors()
            ]);
        }

        $weekNow = Carbon::now()->weekOfMonth;
        $monthNow = Carbon::now()->month;
        $yearNow = Carbon::now()->year;
        $today = Carbon::now()->toDateString();

        $currentWeekCodData = DB::table('customer_order_table')->where('delivery_partner_id',$request->delivery_partner_id)->where('payment_method', 'cod')
            ->where('week', $weekNow)->where('month', $monthNow)->where('year', $yearNow)->pluck('total_price');
        $currentWeekOnlineData = DB::table('customer_order_table')->where('delivery_partner_id',$request->delivery_partner_id)->where('payment_method', 'online')
            ->where('week', $weekNow)->where('month', $monthNow)->where('year',  $yearNow)->pluck('total_price');
        $currentWeekCodCount = sizeof($currentWeekCodData);
        $currentWeekonlineCount = sizeof($currentWeekOnlineData);
        $currentWeekCodTotal = 0;
        $currentWeekonlineTotal = 0;

        foreach ($currentWeekCodData as $value) {
            $currentWeekCodTotal = $currentWeekCodTotal + number_format($value);
        }
        foreach ($currentWeekOnlineData as $value) {
            $currentWeekonlineTotal = $currentWeekonlineTotal + number_format($value);
        }




        $currentMonthCodData = DB::table('customer_order_table')->where('delivery_partner_id',$request->delivery_partner_id)->where('payment_method', 'cod')
            ->where('month', $monthNow)->where('year',  $yearNow)->pluck('total_price');
        $currentMonthOnlineData = DB::table('customer_order_table')->where('delivery_partner_id',$request->delivery_partner_id)->where('payment_method', 'online')
            ->where('month', $monthNow)->where('year', $yearNow)->pluck('total_price');
        $currentMonthCodCount = sizeof($currentMonthCodData);
        $currentMonthonlineCount = sizeof($currentMonthOnlineData);
        $currentMonthCodTotal = 0;
        $currentMonthonlineTotal = 0;

        foreach ($currentMonthCodData as $value) {
            $currentMonthCodTotal = $currentMonthCodTotal + number_format($value);
        }
        foreach ($currentMonthOnlineData as $value) {
            $currentMonthonlineTotal = $currentMonthonlineTotal + number_format($value);
        }


        /*
        $currentYearCodData=DB::table('customer_order_table')->where('partner_id','1')->where('payment_method','cod')
        ->where('year','0')->pluck('total_price');
        $currentYearOnlineData=DB::table('customer_order_table')->where('partner_id','1')->where('payment_method','online')
        ->where('year','0')->pluck('total_price');
        $currentYearCodCount=sizeof($currentYearCodData);
        $currentYearonlineCount=sizeof($currentYearOnlineData);
        $currentYearCodTotal=0;
        $currentYearonlineTotal=0;

        foreach($currentYearCodData as $value)
        {
            $currentYearCodTotal=$currentYearCodTotal+number_format($value);
        }
        foreach($currentYearOnlineData as $value)
        {
            $currentYearonlineTotal=$currentYearonlineTotal+number_format($value);
        }
*/

        $todayCodData = DB::table('customer_order_table')->where('delivery_partner_id',$request->delivery_partner_id)->where('payment_method', 'cod')
            ->where('date',$today)->pluck('total_price');
        $todayOnlineData = DB::table('customer_order_table')->where('delivery_partner_id',$request->delivery_partner_id)->where('payment_method', 'online')
            ->where('date',$today)->pluck('total_price');
        $todayCodCount = sizeof($todayCodData);
        $todayonlineCount = sizeof($todayOnlineData);
        $todayCodTotal = 0;
        $todayonlineTotal = 0;

        foreach ($todayCodData as $value) {
            $todayCodTotal = $todayCodTotal + number_format($value);
        }
        foreach ($todayOnlineData as $value) {
            $todayonlineTotal = $todayonlineTotal + number_format($value);
        }

        return response()->json([
            'week_cod_count' => $currentWeekCodCount,
            'week_online_count' => $currentWeekonlineCount,
          //  'week_cod_total' => $currentWeekCodTotal,
         //   'week_online_total' => $currentWeekonlineTotal,

            'month_cod_count' => $currentMonthCodCount,
            'month_online_count' => $currentMonthonlineCount,
         //   'month_cod_total' => $currentMonthCodTotal,
         //   'month_online_total' => $currentMonthonlineTotal,

            'today_cod_count' => $todayCodCount,
            'today_online_count' => $todayonlineCount,
         //   'today_cod_total' => $todayCodTotal,
         //   'today_online_total' => $todayonlineTotal,
        ]);
    }
}
