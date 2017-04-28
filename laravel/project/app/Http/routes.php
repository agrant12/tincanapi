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

// Restful Resources
Route::group(['prefix' => 'api/v1'], function() {
	Route::resource('users', 'UserController');
	Route::resource('rv', 'RVController');
	Route::resource('address', 'AddressController');

	// Add and Edit User Address
	Route::post('/users/{user_id}/address/create', 'AddressController@store');
	Route::post('/users/{user_id}/address/update', 'AddressController@update');

	// Add and Edit RV
	Route::post('/users/{user_id}/rv/create', 'RVController@store');
	Route::post('/users/{user_id}/rv/update/{rv_id}', 'RVController@update');
	Route::post('/users/{user_id}/rv/upload/photo/{rv_id}', 'RVController@rv_photos');

	// Add and Edit Address
	Route::post('/rv/create/address/{rv_id}', 'AddressController@rv_address');
	Route::post('/rv/update/address/{rv_id}', 'AddressController@update_rv_address');
});

// Search for RV's & Users 
Route::post('search/rv', 'SearchController@searchRV');
Route::post('search/users', 'SearchController@searchUser');

// Charge User Credit Card
Route::post('checkout/{user_id}/{rv_id}/{charge}', 'CreditCardController@charge');
