<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageController extends Controller
{
    public function getAllProductList()
    {
        $fData = array();
        $data = DB::table('product_table')->get();
        for ($i = 0; $i < sizeof($data); $i++) {
            $partnerData = DB::table('partner')->where('id', $data[$i]->partner_id)->select('shop_name', 'address')->get();
            $childObj = [
                'id' => $data[$i]->id,
                'item_name' => $data[$i]->item_name,
                'item_image' => $data[$i]->item_image,
                'price' => $data[$i]->price,
                'price_type' => $data[$i]->price_type,
                'discoutn' => $data[$i]->discount,
                'veg_non_veg' => $data[$i]->veg_non_veg,
                'type' => $data[$i]->type,
                'shop_name' => $partnerData[0]->shop_name,
                'address' => $partnerData[0]->address
            ];
            $fData[$i] = $childObj;
            //  array_merge($fData,$childObj);
        }
        return response()->json(['data' => $fData]);
    }


    public function getAllProductCategory($partner_id)
    {
        $fData = array();
        //  $data = DB::table('product_table')->select('category')->orderBy('category')->get();
        $categories = DB::table('product_table')->where('partner_id', $partner_id)->pluck('category')->unique();
        $partnerData = DB::table('partner')->where('id', $partner_id)->get();
        foreach ($categories as $category) {
            $childData = DB::table('product_table')->where('category', $category)
            ->where('partner_id',$partner_id)->get();
            array_push($fData, ['category_name' => $category, 'category_data' => $childData]);
        }
       // return response()->json(['data'=>$partnerData]);
        return response()->json([
            'partner_id'=>$partnerData[0]->id,
            'shop_name'=>$partnerData[0]->shop_name,
            'address'=>$partnerData[0]->address,
            'shop_image'=>$partnerData[0]->shop_image,
            'speciality'=>$partnerData[0]->speciality,
            'open_time'=>$partnerData[0]->open_time,
            'close_time'=>$partnerData[0]->close_time,
            'rating'=>$partnerData[0]->rating,
            'data' => $fData
            ]);
    }

    public function getAllRestaurent()
    {
        $data = DB::table('partner')->select(['id',
            'shop_name','speciality','shop_image','address','lat','long',
            'close_time','open_time','available','rating'
        ])->get();

        return response()->json(['status'=>200,'data'=>$data]);
    }

    public function getPartnerInfo($id)
    {
        $data = DB::table('partner')->where('id',$id)->
        select(['shop_name','address','shop_image','speciality'])->get();

        return response()->json([
            'status'=>200,
            'data'=>$data[0]
        ]);
    }

}
