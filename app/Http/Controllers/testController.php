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

        foreach ($dateList as $temp) {
            $count = DB::table('customer_order_table')->where('date', $temp)->where('payment_method', 'online')->count();
            array_push($dateCount, ['date' => $temp, 'count' => $count]);
        }
        return response()->json([
         //   'data' => $dateList,
            'count' => $dateCount,
            'week'=>Carbon::now()->weekOfMonth,
            'month'=>Carbon::now()->month,
            'year'=>Carbon::now()->year,
            'date'=>Carbon::now()->toDateString(),
            'current_milli'=>round(microtime(true) * 100)
        ]);
    }
}
