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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//category list
Route::get('category/list','Api\CategoryController@list');
//country list
Route::get('country/list','Api\LocationController@countryList');
//state list
Route::get('state/list','Api\LocationController@stateList');
//district list
Route::get('district/list','Api\LocationController@districtList');
//business type list
Route::get('business-type/list','Api\BusinessTypeController@typeList');
//town list
Route::get('town/list','Api\TownController@townList');
//check store mobile number unqiue
Route::get('store/check/mobile-unique','Api\StoreController@mobCheck');
//check store name unqiue
Route::get('store/check/name-unique','Api\StoreController@nameCheck');
//save Store
Route::post('store/save','Api\StoreController@saveStore');
//Store otpcheck 
Route::get('store/otp-verify','Api\StoreController@verifyOtp');
//Login Store
Route::post('store/login','Api\StoreController@loginStore');


// Route::group(['prefix' => 'auth'], function () {

//     Route::post('login', 'AuthController@login');
//     Route::post('signup', 'AuthController@signup');
  
//     Route::group(['middleware' => 'auth:api'], function() {
//         Route::get('logout', 'AuthController@logout');
//         Route::get('user', 'AuthController@user');
//     });
// });
