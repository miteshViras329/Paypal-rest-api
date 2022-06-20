<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PlanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::group(['prefix' => 'order', 'controller' => CheckoutController::class], function () {
    Route::post('/', 'store')->name('pay');
    Route::get('/createOrder', 'createOrder');
    Route::get('/executeOrder', 'executeOrder');
    Route::get('/cancel', 'cancel');
    Route::get('/success', 'checkOrder');
});

Route::group(['prefix' => 'product', 'controller' => ProductController::class], function () {
    Route::get('/', 'showList');
    Route::get('/show', 'show');
    Route::get('/create', 'createProduct');
    Route::get('/edit', 'updateProduct');
});

Route::group(['prefix' => 'plan', 'controller' => PlanController::class], function () {
    Route::get('/', 'showList');
    Route::get('/show', 'show');
    Route::get('/create', 'createPlan');
    Route::get('/edit', 'updateProduct');
    Route::get('/activate', 'activate');
    Route::get('/deactivate', 'deActivate');
});
