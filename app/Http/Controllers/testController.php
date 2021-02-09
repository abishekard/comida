<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\assertNotNull;

class testController extends Controller
{
    public function test()
    {

        $dateCount = array();
        $dateList = DB::table('customer_order_table')->whereNotNull('date')->groupBy('date')->pluck('date');
        $total=DB::table('customer_order_table')->where('order_id','539')->select('total_price')->first()->total_price;
        foreach ($dateList as $temp) {
            $count = DB::table('customer_order_table')->where('date', $temp)->where('payment_method', 'online')->count();
            array_push($dateCount, ['date' => $temp, 'count' => $count]);
        }
     /*   return response()->json([
         //   'data' => $dateList,
            'count' => $dateCount,
            'week'=>Carbon::now()->weekOfMonth,
            'month'=>Carbon::now()->month,
            'year'=>Carbon::now()->year,
            'date'=>Carbon::now()->toDateString(),
            'current_milli'=>round(microtime(true) * 100)
        ]);   */
        return response()->json([
            'total'=>$total,
            'new Total'=>number_format($total)+100
        ]);
    }

    public function test1()
    {
        $weekNow=Carbon::now()->weekOfMonth;
        $monthNow=Carbon::now()->month;
        $yearNow=Carbon::now()->year;
        $today = Carbon::now()->toDateString();

        $currentWeekData=DB::table('customer_order_table')->where('partner_id','1')->where('payment_method','online')
        ->where('week',$weekNow)->where('month',$monthNow)->where('year','0')->pluck('total_price');

        $weekTotal=0;
        foreach($currentWeekData as $value)
        {
            $weekTotal=$weekTotal+number_format($value);
        }


        $currentmonthData=DB::table('customer_order_table')->where('partner_id','1')->where('payment_method','online')
        ->where('month',$monthNow)->where('year','0')->pluck('total_price');

        $monthTotal=0;
        foreach($currentmonthData as $value)
        {
            $monthTotal=$monthTotal+number_format($value);
        }
        return response()->json([
           'week'=>$currentWeekData,
           'weekTotal'=>$weekTotal,
           'weekSize'=>sizeof($currentWeekData),
           'Month'=>$currentmonthData,
           'monthTotal'=>$monthTotal,
           'monthSize'=>sizeof($currentmonthData)
        ]);
    }
}
