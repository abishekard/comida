<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// customer


//Route::post('/createCustomer', 'Customer\CreateCustomer@create');
Route::post('/customer/create/address','Customer\AddressController@storeAddress');

Route::post('/customer/placeOrder', 'Customer\PlaceOrderController@PlaceOrder');
Route::post('/customer/myOrders/{id}', 'Customer\PlaceOrderController@getOrders');
Route::post('/customer/myOrderDetail/{orderId}', 'Customer\PlaceOrderController@getOrderDetails');

Route::post('/customer/login/send/otp', 'Customer\CLoginController@CheckAndsendOtpToEmail');
Route::post('/customer/login/with/otp', 'AuthController@login');
Route::post('/customer/create/new', 'Customer\CLoginController@createNewUser');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::get('customer/show/profile/{id}','Customer\CInfoController@showProfile');
Route::post('customer/edit/profile','Customer\CInfoController@editProfile');
Route::get('customer/show/address/{id}','Customer\CInfoController@showAddress');
Route::post('customer/edit/address','Customer\CInfoController@editAddress');
Route::post('customer/delete/address/{id}','Customer\CInfoController@deleteAddress');

Route::post('customer/send/email','Customer\CEmailController@emailOtpVerify');
Route::post('customer/product/all','Customer\HomePageController@getAllProductList');
Route::post('customer/product/category/all/{partner_id}','Customer\HomePageController@getAllProductCategory');
Route::post('customer/restaurent/all','Customer\HomePageController@getAllRestaurent');
Route::post('customer/partner/info/{id}','Customer\HomePageController@getPartnerInfo');


// partner

Route::post('partner/create/product','Partner\productController@createProduct');
Route::post('partner/edit/product','Partner\productController@editProduct');
Route::post('partner/delete/product/{id}','Partner\productController@deleteProduct');

Route::post('/createPartner', 'Partner\PartnerController@createPartner');
Route::post('/partnerAddress', 'Partner\PartnerController@getPartnerAddress');
Route::post('/partnerShop', 'Partner\PartnerController@getPartnerShop');
Route::post('partner/login', 'Partner\loginController@login');
Route::get('partner/showPartner/{id}', 'Partner\PInfoController@showPartner');
Route::post('partner/editPartner', 'Partner\PInfoController@editPartner');
Route::get('partner/order/new/{id}', 'Partner\POrderController@newOrder');
Route::get('partner/order/cancelled/{id}', 'Partner\POrderController@cancelledOrder');
Route::get('partner/order/completed/{id}', 'Partner\POrderController@completedOrder');
Route::get('partner/order/detail/{id}', 'Partner\POrderController@orderDetail');
