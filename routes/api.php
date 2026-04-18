<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Middleware\BotAuthMiddleware;

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


Route::middleware('auth:sanctum')->prefix('customer')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/last-password-update', [AuthController::class, 'lastPasswordUpdate']);
    
    Route::get('/profile', [CustomerController::class, 'getProfile']);
    Route::put('/profile', [CustomerController::class, 'updateProfile']);
    Route::delete('/profile', [CustomerController::class, 'deleteProfile']);
    
    Route::get('/dashboard-data', [CustomerController::class, 'getDashboardData']);    
    Route::get('/dashboard-graph-data', [CustomerController::class, 'getDashboardGraphData']);

    Route::get('/analyse-rapide', [CustomerController::class, 'getAnalyseRapide']);

    Route::get('/has-unread-notifications', [CustomerController::class, 'hasUnreadNotifications']);
    
    Route::get('/accountant-info', [CustomerController::class, 'getAccountantInfo']);

    Route::get('/notification', [CustomerController::class, 'getCustomerNotifications']);
    Route::get('/view-single-notification/{id}', [CustomerController::class, 'viewSingleNotification']);
    Route::post('/clear-notifications', [CustomerController::class, 'clearNotifications']);

    Route::get('/documents', [CustomerController::class, 'getDocuments']);
    Route::get('/documents-data', [CustomerController::class, 'getDocumentsData']);
    Route::get('/documents/download/{id}', [CustomerController::class, 'downloadDocument']);

    Route::get('/transaction-resources', [LookupController::class, 'getTransactionResources']);
    Route::post('/transaction', [CustomerController::class, 'storeTransaction']);
    Route::get('/transactions', [CustomerController::class, 'getTransactions']);
    Route::get('/transaction/{id}', [CustomerController::class, 'viewSingleTransaction']);
    Route::get('/transactions/receipt/{id}', [CustomerController::class, 'downloadReceipt']);

    Route::post('/bank-statement', [CustomerController::class, 'storeStatement']);
    Route::get('/bank-statements', [CustomerController::class, 'getBankStatements']);
    Route::get('/bank-statement/{id}', [CustomerController::class, 'viewSingleBankStatement']);
    Route::get('/bank-statement/download/{id}', [CustomerController::class, 'downloadBankStatement']);

    Route::get('/workflow-status', [CustomerController::class, 'getWorkflowStatus']);

    Route::post('/customer-client', [CustomerController::class, 'storeCustomerClient']);
    Route::get('/customer-clients', [CustomerController::class, 'getCustomerClients']);
    Route::get('/customer-client/{id}', [CustomerController::class, 'viewSingleCustomerClient']);
    Route::put('/customer-client/{id}', [CustomerController::class, 'updateCustomerClient']);
    Route::delete('/customer-client/{id}', [CustomerController::class, 'deleteCustomerClient']);
    Route::get('/customer-client-invoice/{id}', [CustomerController::class, 'getCustomerClientInvoices']);

    Route::post('/customer-supplier', [CustomerController::class, 'storeCustomerSupplier']);
    Route::get('/customer-suppliers', [CustomerController::class, 'getCustomerSuppliers']);
    Route::get('/customer-supplier/{id}', [CustomerController::class, 'viewSingleCustomerSupplier']);
    Route::put('/customer-supplier/{id}', [CustomerController::class, 'updateCustomerSupplier']);
    Route::delete('/customer-supplier/{id}', [CustomerController::class, 'deleteCustomerSupplier']);
    Route::get('/customer-supplier-expenses/{id}', [CustomerController::class, 'getCustomerSupplierExpenses']);
    

    Route::post('/customer-expense', [CustomerController::class, 'storeExpense']);
    Route::get('/customer-expenses', [CustomerController::class, 'getExpenses']);
    Route::get('/customer-expense/{id}', [CustomerController::class, 'viewSingleExpense']);
    Route::put('/customer-expense/{id}', [CustomerController::class, 'updateExpense']);
    Route::delete('/customer-expense/{id}', [CustomerController::class, 'deleteExpense']);
    Route::get('/export-expenses', [CustomerController::class, 'exportExpenses']);
    Route::get('/customer-expenses/file/{id}', [CustomerController::class, 'downloadExpenseFile']);
    Route::get('/customer-expenses/chart', [CustomerController::class, 'getExpenseCategoryChart']);
    Route::post('/duplicate-expense/{id}', [CustomerController::class, 'duplicateExpense']);


    Route::get('/customer-clients-resources', [LookupController::class, 'getCustomerClientResources']);
    Route::post('/customer-invoice', [CustomerController::class, 'storeInvoice']);
    Route::get('/customer-invoices', [CustomerController::class, 'getInvoices']);
    Route::get('/customer-invoice/{id}', [CustomerController::class, 'viewSingleInvoice']);
    Route::put('/customer-invoice/{id}', [CustomerController::class, 'updateInvoice']);
    Route::delete('/customer-invoice/{id}', [CustomerController::class, 'deleteInvoice']);
    Route::get('/export-invoices', [CustomerController::class, 'exportInvoices']);
    Route::get('/customer-invoices/download/{id}', [CustomerController::class, 'downloadInvoice']);
    Route::get('/customer-invoices/pdf/{id}', [CustomerController::class, 'downloadInvoicePdf']);


    Route::get('/product-resources', [LookupController::class, 'getProductResources']);
    Route::post('/customer-product', [CustomerController::class, 'storeCustomerProduct']);
    Route::get('/customer-products', [CustomerController::class, 'getCustomerProducts']);
    Route::get('/customer-product/{id}', [CustomerController::class, 'viewSingleCustomerProduct']);
    Route::put('/customer-product/{id}', [CustomerController::class, 'updateCustomerProduct']);
    Route::delete('/customer-product/{id}', [CustomerController::class, 'deleteCustomerProduct']);


    Route::get('/customer-quotes', [CustomerController::class, 'getQuotes']);
    Route::post('/customer-quote', [CustomerController::class, 'storeQuote']);
    Route::get('/customer-quote/{id}', [CustomerController::class, 'viewSingleQuote']);
    Route::put('/customer-quote/{id}', [CustomerController::class, 'updateQuote']);
    Route::delete('/customer-quote/{id}', [CustomerController::class, 'deleteQuote']);
    Route::get('/customer-quotes/pdf/{id}', [CustomerController::class, 'downloadQuotePdf']);

    Route::post('/quote-to-invoice/{id}', [CustomerController::class, 'quoteToInvoice']);


    Route::post('/send-accountant-email', [CustomerController::class, 'sendToAccountant']);
    
    // WhatsApp Bot Activation (OTP) - Protected by standard user auth too
    Route::post('/bot/request-activation', [CustomerController::class, 'requestActivation']);
    Route::post('/bot/verify-activation', [CustomerController::class, 'verifyActivation']);
});

// --- WhatsApp Bot Bridge (Secure Proxy Access via Dedicated Prefix) ---
Route::group(['prefix' => 'bot'], function () {
    
    // 1. Secure Bot Identity Routes (Require X-Bot-Secret)
    Route::group(['middleware' => 'bot.auth'], function () {
        
        // A. Customer Contextual Routes (Require X-Customer-Phone)
        Route::group(['prefix' => 'customer'], function () {
            // Shared Data Store Handlers
            Route::post('/transaction', [CustomerController::class, 'storeTransaction']);
            // Add other routes as per your reference...
            Route::post('/bank-statement', [CustomerController::class, 'storeStatement']);
            Route::post('/customer-client', [CustomerController::class, 'storeCustomerClient']);
            Route::post('/customer-supplier', [CustomerController::class, 'storeCustomerSupplier']);
            Route::post('/customer-expense', [CustomerController::class, 'storeExpense']);
            Route::post('/customer-invoice', [CustomerController::class, 'storeInvoice']);
            Route::post('/customer-product', [CustomerController::class, 'storeCustomerProduct']);
            
            // Status metrics specifically for the bot
            Route::get('/dashboard-data', [CustomerController::class, 'getDashboardData']);

            // AI Quota & Limits
            Route::get('/ai/status', [CustomerController::class, 'aiStatus']);
            Route::post('/ai/log', [CustomerController::class, 'aiLog']);

            // Resource Lookups
            Route::get('/customer-clients', [CustomerController::class, 'getCustomerClients']);
            Route::get('/transaction-resources', [LookupController::class, 'getTransactionResources']);

            Route::get('/customer-invoices', [CustomerController::class, 'getInvoices']);
            Route::get('/customer-expenses', [CustomerController::class, 'getExpenses']);
            Route::get('/bank-statements', [CustomerController::class, 'getBankStatements']);

            // Profile check for Global Activation
            Route::get('/profile', [CustomerController::class, 'getProfile']);
        });

        // Add any other bot.auth routes here if needed
    });
    // 2. Public Technical Routes (Require Signed URL for Security)
    Route::get('/file/{id}', [CustomerController::class, 'downloadFilePublic'])
        ->middleware('signed')
        ->name('api.download.file.public');
    Route::get('/invoice/pdf/{id}', [CustomerController::class, 'downloadInvoicePdfPublic'])
        ->middleware('signed')
        ->name('api.download.invoice.pdf.public');
});
