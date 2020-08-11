<?php

use Illuminate\Http\Request;

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
Route::get('health' , function(){
    return \Response::make('message', 200);
});

Route::group(['middleware' => 'cors'], function () {

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/register', 'API\RegisterController@register');
Route::post('v1/login', 'API\RegisterController@login');
Route::post('v1/forgot_password', 'API\UserController@forgot_password');

//Route::resource('v1/reviews', 'API\ReviewController');



Route::group(['middleware' => ['auth:api','checkHeader']], function () {
    Route::post('v1/logout', 'API\RegisterController@logout');
    Route::get('v1/orders/pdf/{id}', 'API\OrderController@savepdf');
    Route::resource('products', 'API\ProductController');
	Route::get('v1/users', 'API\UserController@index');
	Route::get('v1/users/{id}', 'API\UserController@show');

	Route::resource('v1/medicationCategories', 'API\MedicationCategoryController');
	Route::get('v1/countries', 'API\CountryController@index');
	Route::get('v1/cities/{country_id}', 'API\CityController@index');
	Route::resource('v1/medications', 'API\MedicationController');
	Route::resource('v1/subscriptions', 'API\SubscriptionController');
	Route::resource('v1/leads', 'API\LeadController');
	Route::put('v1/orders/update/status', 'API\OrderController@updateStatus');

	Route::resource('v1/orders', 'API\OrderController');
	Route::post('v1/subscribers/save', 'API\LeadController@addSubscriber');
	Route::get('v1/subscribers', 'API\LeadController@getAllSubscribers');
	Route::resource('v1/b2b', 'API\B2BController');

	Route::post('v1/change_password', 'Api\UserController@change_password');
});

});
