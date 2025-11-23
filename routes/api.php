<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WilayahDbController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Wilayah endpoints backed by local DB
Route::prefix('wilayah')->group(function () {
    Route::get('/provinces', [WilayahDbController::class, 'getProvinces']);
    Route::get('/cities/{province}', [WilayahDbController::class, 'getCities']);
    Route::get('/districts/{city}', [WilayahDbController::class, 'getDistricts']);
    Route::get('/sub-district/{district}', [WilayahDbController::class, 'getSubDistrict']);
});
