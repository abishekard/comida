<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class productController extends Controller
{
    public function createProduct(Request $request)
    {
           $validate = Validator::make($request->all(),[
                 'item_name'=>'required',
                 'item_image'=>'required|mimes:png,jpg',
                 'item_price'=>'required',
                 'veg_non_veg'=>'required',
                 'type'=>'required',
                 'discount'=>'required'
           ]);
           if($validate->fails())
           {
               return response()->json(['status'=>300,
               'message'=>$validate->errors()]);
           }

           $name_gen = hexdec(uniqid());
           $item_img = $request->file('item_image');
           $img_ext = strtolower($item_img->getClientOriginalExtension());
           $img_name = $name_gen.'.'.$img_ext;
           $uplocation = 'images/product_image/';
           $last_img = $uplocation.$img_name;

           DB::table('product_table')->insert([
                 'partner_id'=>1,
                 'item_name'=>$request->item_name,
                 'item_image'=>$last_img,
                 'price'=>$request->item_price,
                 'veg_non_veg'=>$request->veg_non_veg,
                 'type'=>$request->type,
                 'discount'=>$request->discount,
                 'created_at'=>Carbon::now()
           ]);
           $item_img->move($uplocation,$img_name);

           return response()->json(['status'=>200,
               'message'=>'product created']);

    }
}
