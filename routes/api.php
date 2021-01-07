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


Route::post('/createCustomer', 'Customer\CreateCustomer@create');
Route::post('/storeAddress','Customer\AddressController@storeAddress');
Route::post('/createProduct','Partner\productController@createProduct');
Route::post('/placeOrder', 'Customer\PlaceOrderController@PlaceOrder');
Route::post('/myOrders/{id}', 'Customer\PlaceOrderController@getOrders');
Route::post('/myOrderDetail/{orderId}', 'Customer\PlaceOrderController@getOrderDetails');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});


// partner

Route::post('/createPartner', 'Partner\PartnerController@createPartner');
Route::post('/partnerAddress', 'Partner\PartnerController@getPartnerAddress');
Route::post('/partnerShop', 'Partner\PartnerController@getPartnerShop');
Route::post('partner/login', 'Partner\loginController@login');
Route::get('partner/showPartner/{id}', 'Partner\PInfoController@showPartner');
Route::post('partner/editPartner', 'Partner\PInfoController@editPartner');
