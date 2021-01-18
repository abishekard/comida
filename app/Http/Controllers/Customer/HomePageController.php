<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageController extends Controller
{
    public function getAllProductList()
    {
          $fData=array();
          $data = DB::table('product_table')->get();
          for($i=0;$i<sizeof($data);$i++)
          {
              $partnerData=DB::table('partner')->where('id',$data[$i]->partner_id)->
              select('shop_name','address')->get();
              $childObj=[
                  'id'=>$data[$i]->id,
                  'item_name'=>$data[$i]->item_name,
                  'item_image'=>$data[$i]->item_image,
                  'price'=>$data[$i]->price,
                  'price_type'=>$data[$i]->price_type,
                  'discoutn'=>$data[$i]->discount,
                  'veg_non_veg'=>$data[$i]->veg_non_veg,
                  'type'=>$data[$i]->type,
                  'shop_name'=>$partnerData[0]->shop_name,
                  'address'=>$partnerData[0]->address
              ];
              $fData[$i]=$childObj;
            //  array_merge($fData,$childObj);
          }
          return response()->json(['data'=>$fData]);
    }
}
