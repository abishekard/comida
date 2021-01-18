<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageController extends Controller
{
    public function getAllProductList()
    {
          $data = DB::table('product_table')->get();
          return response()->json(['data'=>$data]);
    }
}
