<?php

use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'paypal', 'controller' => PayPalController::class], function () {
    Route::post('/', 'store')->name('pay');
    Route::get('/createOrder', 'createOrder');
    Route::get('/executeOrder', 'executeOrder');
    Route::get('/cancel', 'cancel');
    Route::get('/success', 'executeOrder');
});
