<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function storeAddress(Request $request)
    {
          $validate = Validator::make($request->all(),[
                'address'=>'required',
                'state'=>'required',
                'city'=>'required',
                'landmark'=>'required',
                'locality'=>'required',
                'latitude'=>'required',
                'longitude'=>'required',
                'pincode'=>'required|max:6|min:6',
                'address_type'=>'required',
                'local_city'=>'required'
          ]);
          if($validate->fails())
          {
              return response()->json(['status'=>300,'message'=>$validate->errors()]);
          }
          DB::table('customeraddresstable')->insert([
              'address'=>$request->address,
              'state'=>$request->state,
              'city'=>$request->city,
              'local_city'=>$request->local_city,
              'landmark'=>$request->landmark,
              'pincode'=>$request->pincode,
              'locality'=>$request->locality,
              'latitude'=>$request->latitude,
              'longitude'=>$request->longitude,
              'address_type'=>$request->address_type,
              'user_id'=>Auth::user()->id,
              'created_at'=>Carbon::now()
          ]);

          return response()->json(['status'=>200,'message'=>'address saved']);
    }
}
