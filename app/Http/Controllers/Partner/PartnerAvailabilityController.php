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
                "partner_id" => "required"
          ]);

          if($validate->fails())
          {
              return response()->json([
                  "status"=>300,
                  "message"=>$validate->errors()
              ]);
          }

          $data = DB::table('partner')->where("id",$request->partner_id)->select("available","local_city")->first();

          return response()->json([
              "status"=>200,
              "available"=>$data->available,
              "city"=>$data->local_city
          ]);
    }
}
