<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function() {
	return view('welcome');
});

// Register, Login, and Logout
Route::post('auth/register', 'RegisterController@create');
Route::post('auth/login', 'RegisterController@login');
Route::post('auth/logout', 'RegisterController@logout');

// Request Access Token
Route::post('oauth/access_token/{user_id}', 'RegisterController@access_token');

// Email Verification
Route::get('register/verify/{confirmation_code}', 'RegisterController@confirm');

Route::controllers([
	//'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::group(['prefix' => 'api/v1'], function() {
	Route::resource('users', 'UserController');
	Route::resource('vehicle', 'VehicleController');
	Route::resource('address', 'AddressController');
});

// Restful Resources
Route::group(['prefix' => 'api/v1', 'before' => 'oauth', 'middleware' => 'token-owner'], function() {

	// Add and Edit User Address
	Route::post('/users/{user_id}/address/create', 'AddressController@store');
	Route::post('/users/{user_id}/address/update', 'AddressController@update');

	// Add and Edit vehicle
	Route::post('/users/{user_id}/vehicle/create', 'VehicleController@store');
	Route::post('/users/{user_id}/vehicle/update/{vehicle_id}', 'VehicleController@update');
	Route::post('/users/{user_id}/vehicle/upload/photo/{vehicle_id}', 'VehicleController@vehicle_photos');
	Route::post('/vehicle/{user_id}/delete/photo/{photo_id}', 'VehicleController@delete_vehicle_photo');
	Route::post('/vehicle/{user_id}/options/{vehicle_id}', 'VehicleController@options');

	// Add and Edit Address
	Route::post('/users/{user_id}/vehicle/create/address/{vehicle_id}', 'AddressController@vehicle_address');
	Route::post('/users/{user_id}/vehicle/update/address/{vehicle_id}', 'AddressController@update_vehicle_address');

	Route::post('/review/{reviewer_id}/{reviewee_id}', 'UserController@review');

	Route::post('/rental/vehicle/{vehicle_id}/{renter_id}/{rentee_id}', 'VehicleController@rental');
});

// Search for vehicle's & Users 
Route::post('search/vehicle', 'SearchController@searchvehicle');
Route::post('search/users', 'SearchController@searchUser');

// Charge User Credit Card
Route::post('checkout/{user_id}/{vehicle_id}/{charge}', 'CreditCardController@charge');
