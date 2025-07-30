<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\PropertyController;

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
