<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PartnerAvailabilityController extends Controller
{
    public function isPartnerAvailableToTakeOrder(Request $request)
    {
          $validate = Validator::make($request->all(),[
                "partner_id" => "required",
                "address_id"=>"required"
          ]);

          if($validate->fails())
          {
              return response()->json([
                  "status"=>300,
                  "message"=>$validate->errors()
              ]);
          }

          $customerAddress = DB::table('customeraddresstable')->where('id',$request->address_id)->select('local_city')->first();

          $data = DB::table('partner')->where("id",$request->partner_id)->select("available","local_city")->first();

          $addressCheck=0;
          if($customerAddress->local_city == $data->local_city)
          {
               $addressCheck=1;
          }
          else{
              $addressCheck=0;
          }
          return response()->json([
              "status"=>200,
              "available"=>$data->available,
              "address_check"=>$addressCheck
          ]);
    }
}
