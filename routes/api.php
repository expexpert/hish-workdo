<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController; // Add this

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::get('test-api', function () {
    return response()->json(['status' => 'API working']);
});


Route::middleware('auth:sanctum')->get('/customer/profile', function (Request $request) {
    return $request->user();
});



Route::post('/customer/login', [CustomerController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/customer/logout', [CustomerController::class, 'logout']);
    Route::get('/customer/notification', [CustomerController::class, 'getCustomerNotifications']);
    Route::get('/customer/view-single-notification/{id}', [CustomerController::class, 'viewSingleNotification']);
    Route::post('/customer/clear-notifications', [CustomerController::class, 'clearNotifications']);
});







