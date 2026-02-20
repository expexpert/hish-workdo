<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LookupController;

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



Route::post('/customer/login', [AuthController::class, 'login']);
Route::post('/customer/forgot-password', [AuthController::class, 'ForgotPassword']);
Route::post('/customer/forgot-password-otp', [AuthController::class, 'resetPasswordWithOtp']);



Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    Route::get('/profile', [CustomerController::class, 'getProfile']);
    Route::get('/dashboard-data', [CustomerController::class, 'getDashboardData']);
    
    Route::get('accoutant-info', [CustomerController::class, 'getAccountantInfo']);

    Route::get('/notification', [CustomerController::class, 'getCustomerNotifications']);
    Route::get('/view-single-notification/{id}', [CustomerController::class, 'viewSingleNotification']);
    Route::post('/clear-notifications', [CustomerController::class, 'clearNotifications']);

    Route::get('/documents', [CustomerController::class, 'getDocuments']);

    Route::get('/transaction-resources', [LookupController::class, 'getTransactionResources']);
    Route::post('/transaction', [CustomerController::class, 'storeTransaction']);
    Route::get('/transactions', [CustomerController::class, 'getTransactions']);
    Route::get('/transaction/{id}', [CustomerController::class, 'viewSingleTransaction']);

    Route::post('/bank-statement', [CustomerController::class, 'storeStatement']);
    Route::get('/bank-statements', [CustomerController::class, 'getBankStatements']);
    Route::get('/bank-statement/{id}', [CustomerController::class, 'viewSingleBankStatement']);

    Route::get('/workflow-status', [CustomerController::class, 'getWorkflowStatus']);

    Route::post('/customer-client', [CustomerController::class, 'storeCustomerClient']);
    Route::get('/customer-clients', [CustomerController::class, 'getCustomerClients']);
    Route::get('/customer-client/{id}', [CustomerController::class, 'viewSingleCustomerClient']);

});







