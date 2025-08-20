<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\UssdController;


//--------------------------------------------------------------------------
// API Routes
//--------------------------------------------------------------------------
//Here is where you can register API routes for your application. These
//routes are loaded by the RouteServiceProvider within a group which
//is assigned the "api" middleware group. Enjoy building your API!


// Public API routes
Route::middleware('api')->group(function () {
    // Get available units for a property
    Route::get('/properties/{property}/available-units', [PropertyController::class, 'availableUnits'])
        ->name('api.properties.available-units');
});

// USSD callback route (no auth required)
Route::match(['get', 'post'], '/ussd/callback', [UssdController::class, 'callback'])->name('ussd.callback');

// USSD test routes (for debugging)
Route::post('/ussd/test', [\App\Http\Controllers\UssdTestController::class, 'test'])->name('ussd.test');
Route::post('/ussd/debug', [\App\Http\Controllers\UssdTestController::class, 'debug'])->name('ussd.debug');

// M-Pesa callback routes (no auth required)
Route::prefix('mpesa')->group(function () {
    Route::post('/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
    Route::post('/timeout', [MpesaController::class, 'timeout'])->name('mpesa.timeout');
    
    // M-Pesa Test Routes (for development/testing)
    Route::get('/test/success', [App\Http\Controllers\MpesaTestController::class, 'testSuccess'])->name('mpesa.test.success');
    Route::get('/test/failure', [App\Http\Controllers\MpesaTestController::class, 'testFailure'])->name('mpesa.test.failure');
    Route::get('/test/timeout', [App\Http\Controllers\MpesaTestController::class, 'testTimeout'])->name('mpesa.test.timeout');
    Route::get('/test/sandbox', [App\Http\Controllers\MpesaTestController::class, 'testSandboxDetection'])->name('mpesa.test.sandbox');
    Route::get('/test/status/{transactionId}', [App\Http\Controllers\MpesaTestController::class, 'getTestStatus'])->name('mpesa.test.status');
});

// Authenticated API routes
Route::middleware(['auth', 'role:tenant'])->group(function () {
    // M-Pesa STK Push routes
    Route::prefix('mpesa')->group(function () {
        Route::post('/stk-push', [MpesaController::class, 'stkPush'])->name('mpesa.stk-push');
        Route::post('/check-status', [MpesaController::class, 'checkStatus'])->name('mpesa.check-status');
    });
});
