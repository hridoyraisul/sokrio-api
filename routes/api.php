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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    Route::get('product-create-pre-requisit',[\App\Http\Controllers\ApiController::class,'productCreatePreRequisit']);
    Route::post('product-create',[\App\Http\Controllers\ApiController::class,'productCreate']);
    Route::post('create-stock-record',[\App\Http\Controllers\ApiController::class,'createStockRecord']);
    Route::post('create-sales-record',[\App\Http\Controllers\ApiController::class,'createSalesRecord']);

});
