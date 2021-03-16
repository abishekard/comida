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
Route::post('/customer/orders/new/{id}', 'Customer\PlaceOrderController@getNewOrders');
Route::post('/customer/orders/completed/{id}', 'Customer\PlaceOrderController@getCompletedOrders');
Route::post('/customer/order/detail/{orderId}', 'Customer\PlaceOrderController@getOrderDetails');

Route::post('/customer/login/send/otp', 'Customer\CLoginController@CheckAndsendOtpToEmail');
Route::post('/customer/login/with/otp', 'AuthController@login');
Route::post('/customer/create/new', 'Customer\CLoginController@createNewUser');
Route::post('/customer/send/notification', 'Customer\CNotificationController@sendNotification');
Route::post('/customer/send/notification/topic', 'Customer\CNotificationController@sendNotificationToTopic');

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
Route::post('customer/store/fcm','Customer\CInfoController@storeFcmToken');


// partner


Route::post('partner/login/send/otp', 'Partner\PartnerController@CheckAndsendOtpToEmail');
Route::post('partner/login/with/otp', 'Partner\loginController@login');
Route::post('partner/create/new', 'Partner\PartnerController@createNewUser');
Route::post('partner/store/fcm','Partner\PartnerController@storeFcmToken');

Route::post('partner/product/create','Partner\productController@createProduct');
Route::post('partner/product/edit','Partner\productController@editProduct');
Route::post('partner/product/delete/{id}','Partner\productController@deleteProduct');
Route::post('partner/product/show','Partner\productController@showProduct');
Route::post('partner/product/detail/show','Partner\productController@getProductDetails');
Route::post('partner/product/change/stock','Partner\productController@changeStock');

//Route::post('partner/create', 'Partner\PartnerController@createPartner');
Route::post('partner/get/address', 'Partner\PartnerController@getPartnerAddress');
Route::post('/partner/get/shop', 'Partner\PartnerController@getPartnerShop');
//Route::post('partner/login', 'Partner\loginController@login');
Route::get('partner/profile/show/{id}', 'Partner\PInfoController@showPartner');
Route::post('partner/profile/edit', 'Partner\PInfoController@editPartner');
Route::post('partner/get/delivery/partner', 'Partner\PInfoController@getDeliveryPartner');
Route::post('partner/add/delivery/partner', 'Partner\PInfoController@addDeliveryPartner');
Route::post('partner/remove/delivery/partner', 'Partner\PInfoController@removeDeliveryPartner');

Route::post('partner/order/new/{id}', 'Partner\POrderController@newOrder');
Route::post('partner/order/progress/{id}', 'Partner\POrderController@inProgressOrder');
Route::post('partner/order/completed/{id}', 'Partner\POrderController@completedOrder');
Route::post('partner/order/detail/{id}', 'Partner\POrderController@orderDetail');
Route::post('partner/order/queue', 'Partner\POrderController@queueOrder');
Route::post('partner/order/dispatch', 'Partner\POrderController@dispatchOrder');
Route::post('partner/sales/current', 'Partner\SalesController@currentSales');
Route::post('partner/available', 'Partner\PartnerAvailabilityController@isPartnerAvailableToTakeOrder');

Route::post('test', 'testController@test1');


// DeliveryPartner

Route::post('delivery/partner/login/send/otp', 'Delivery\DPartnerController@CheckAndsendOtpToEmail');
Route::post('delivery/partner/login/with/otp', 'Delivery\DLoginController@login');
Route::post('delivery/partner/create/new', 'Delivery\DPartnerController@createNewUser');
Route::post('delivery/partner/store/fcm','Delivery\DPartnerController@storeFcmToken');

//Route::post('delivery/partner/create','Delivery\DPartnerController@createPartner');
//Route::post('delivery/partner/login', 'Delivery\DLoginController@login');
Route::post('delivery/partner/get/address', 'Delivery\DPartnerController@getDeliveryPartnerAddress');
Route::post('delivery/partner/show/profile', 'Delivery\DInfoController@showProfile');
Route::post('delivery/partner/edit/profile', 'Delivery\DInfoController@editProfile');
Route::post('delivery/partner/get/personal/info', 'Delivery\DPartnerController@getPersonalInfo');
Route::post('delivery/partner/order/new/{id}', 'Delivery\DOrderController@newOrder');
Route::post('delivery/partner/order/progress/{id}', 'Delivery\DOrderController@inProgressOrder');
Route::post('delivery/partner/order/completed/{id}', 'Delivery\DOrderController@completedOrder');
Route::post('delivery/partner/order/detail/{id}', 'Delivery\DOrderController@orderDetail');
Route::post('delivery/partner/order/delivered', 'Delivery\DOrderController@orderDelivered');
Route::post('delivery/partner/delivery/report', 'Delivery\DeliveryController@currentDeliveryReport');

