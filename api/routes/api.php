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

Route::get('books', 'App\Http\Controllers\API\BookController@index');
Route::post('register', 'App\Http\Controllers\API\RegisterController@register');

Route::middleware('auth:api')->group( function () {
	Route::resource('books', 'App\Http\Controllers\API\BookController', ['except' => ['index']]);
});