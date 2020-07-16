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

Route::post('register', 'UserController@register')->middleware('cors');
Route::post('login', 'UserController@authenticate')->middleware('cors');

Route::group(['middleware' => ['jwt.verify','cors']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');
    
    Route::prefix('category')->group(function () {
        Route::post('create', 'CategoryController@create_category');
        Route::put('edit', 'CategoryController@edit_category');
        Route::delete('delete', 'CategoryController@delete_category');
    });

    Route::prefix('product')->group(function () {
        Route::post('create', 'ProductsController@create_product');
        Route::put('edit', 'ProductsController@edit_product');
        Route::delete('delete', 'ProductsController@delete_product');
        Route::get('create_fifty', 'ProductsController@create_fifty_products');
        Route::get('export', 'ProductsController@export_products');
    });
});
