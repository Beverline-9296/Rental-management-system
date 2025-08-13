<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\MpesaController;

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

// Public API routes
Route::middleware('api')->group(function () {
    // Get available units for a property
    Route::get('/properties/{property}/available-units', [PropertyController::class, 'availableUnits'])
        ->name('api.properties.available-units');
});

// M-Pesa callback routes (no auth required)
Route::prefix('mpesa')->group(function () {
    Route::post('/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
    Route::post('/timeout', [MpesaController::class, 'timeout'])->name('mpesa.timeout');
});

// Authenticated API routes
Route::middleware(['auth', 'role:tenant'])->group(function () {
    // M-Pesa STK Push routes
    Route::prefix('mpesa')->group(function () {
        Route::post('/stk-push', [MpesaController::class, 'stkPush'])->name('mpesa.stk-push');
        Route::post('/check-status', [MpesaController::class, 'checkStatus'])->name('mpesa.check-status');
    });
});
