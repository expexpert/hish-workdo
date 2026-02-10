<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerAuthController; // Add this

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is working 🎉',
        'env' => app()->environment(),
    ]);
});













