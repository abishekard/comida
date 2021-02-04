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
        $validate = Validator::make($request->all(), [
            'item_name' => 'required',
            'item_image' => 'required|mimes:png,jpg',
            'item_price' => 'required',
            'veg_non_veg' => 'required',
            'category' => 'required',
            'price_type' => 'required',
            'discount' => 'required',
            'partner_id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        $name_gen = hexdec(uniqid());
        $item_img = $request->file('item_image');
        $img_ext = strtolower($item_img->getClientOriginalExtension());
        $img_name = $name_gen . '.' . $img_ext;
      //  $uplocation = 'images/product_image/';
          $uplocation = 'public/images/product_image/';
        $upl = 'images/product_image/';
        $last_img =$upl . $img_name;


        DB::table('product_table')->insert([
            'partner_id' => $request->partner_id,
            'item_name' => $request->item_name,
            'item_image' => $last_img,
            'price' => $request->item_price,
            'veg_non_veg' => $request->veg_non_veg,
            'category' => $request->category,
            'discount' => $request->discount,
            'price_type' => $request->price_type,
            'in_stock'=>1,
            'created_at' => Carbon::now()
        ]);
        $item_img->move($uplocation, $img_name);

        return response()->json([
            'status' => 200,
            'message' => 'product created'
        ]);
    }

    public function editProduct(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 300,
                'message' => $validate->errors()
            ]);
        }

        if ($request->item_name) {
            DB::table('product_table')->where('id', $request->id)
                ->update(['item_name' => $request->item_name]);
        }
        if ($request->price) {
            DB::table('product_table')->where('id', $request->id)
                ->update(['price' => $request->price]);
        }
        if ($request->price_type) {
            DB::table('product_table')->where('id', $request->id)
                ->update(['price_type' => $request->price_type]);
        }
        if ($request->discount) {
            DB::table('product_table')->where('id', $request->id)
                ->update(['discount' => $request->discount]);
        }
        if ($request->veg_non_veg) {
            DB::table('product_table')->where('id', $request->id)
                ->update(['veg_non_veg' => $request->veg_non_veg]);
        }
        if ($request->category) {
            DB::table('product_table')->where('id', $request->id)
                ->update(['category' => $request->category]);
        }
        if ($request->item_image) {
            $validate = Validator::make($request->all(), [
                'item_image' => 'mimes:png,jpg'
            ]);
            if ($validate->fails()) {
                return response()->json([
                    'status' => 300,
                    'message' => $validate->errors()
                ]);
            }





            $new_img = $request->file('item_image');
            $img_name = hexdec(uniqid()) . '.' . 'jpg';
            //$up_location = 'images/product_image/';

            $up_location = 'public/images/product_image/';
            $upl = '/images/product_image/';
            $last_img = env('APP_URL') . $upl . $img_name;

            $new_img->move($up_location, $img_name);

            DB::table('product_table')->where('id', $request->id)
                ->update(['item_image' => $up_location . $img_name]);
        }

        return response()->json([
            'status' => 200,
            'message' => 'updated'
        ]);
    }

    public function deleteProduct($id)
    {
        $res = DB::table('product_table')->where('id', $id)->delete();

        if ($res)
            return response()->json([
                'status' => 200,
                'message' => 'deleted'
            ]);
        else
            return response()->json([
                'status' => 201,
                'message' => 'error'
            ]);
    }

/*    public function showProduct(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'id'=>'required'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status'=>300,
                'message'=>$validate->errors()
            ]);
        }

        $data = DB::table('product_table')->where('partner_id',$request->id)->get();

        return response()->json([
            'status'=>200,
            'data'=>$data
        ]);


    }
*/


    public function showProduct(Request $request)
    {
        $fData = array();
        $categories = DB::table('product_table')->where('partner_id', $request->id)->pluck('category')->unique();
      //  $partnerData = DB::table('partner')->where('id', $$request->id)->get();
        foreach ($categories as $category) {
            $childData = DB::table('product_table')->where('category', $category)
            ->where('partner_id',$request->id)->get();
            array_push($fData, ['category_name' => $category, 'category_data' => $childData]);
        }
       // return response()->json(['data'=>$partnerData]);
        return response()->json([
            'status'=>200,
            'data' => $fData
            ]);
    }


    public function changeStock(Request $request)
    {
          $validate = Validator::make($request->all(),[
              'product_id'=>'required',
              'in_stock'=>'required'
          ]);

          if($validate->fails())
          {
              return response()->json([
                  'status'=>300,
                  'message'=>$validate->errors()
              ]);
          }

          DB::table('product_table')->where('id',$request->product_id)->update([
               'in_stock'=>$request->in_stock
          ]);


          return response()->json([
            'status'=>200,
            'message'=>'stock changed'
        ]);
    }
}
