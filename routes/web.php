<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});
// Handle form submission (POST)
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');

// M-Pesa Testing Dashboard (for development)
Route::get('/mpesa-test', function () {
    return view('mpesa-test');
})->name('mpesa.test.dashboard');

// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware('auth');

// Redirect authenticated users to appropriate dashboard
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isLandlord()) {
        return redirect()->route('landlord.dashboard');
    } else {
        return redirect()->route('tenant.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Landlord Routes (Protected by role middleware)
Route::middleware(['auth', 'role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [LandlordController::class, 'dashboard'])->name('dashboard');
    
    // Properties Resource
    Route::resource('properties', \App\Http\Controllers\Landlord\PropertyController::class);
    
    // Tenants Resource
    Route::resource('tenants', \App\Http\Controllers\Landlord\TenantController::class)->except(['show']);
    Route::get('tenants/{tenant}', [\App\Http\Controllers\Landlord\TenantController::class, 'show'])->name('tenants.show');
    Route::post('tenants/{tenant}/reset-password', [\App\Http\Controllers\Landlord\TenantController::class, 'resetPassword'])->name('tenants.reset-password');
    
    // Maintenance Routes
    Route::get('maintenance', [\App\Http\Controllers\Landlord\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/{maintenanceRequest}', [\App\Http\Controllers\Landlord\MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::patch('maintenance/{maintenanceRequest}/status', [\App\Http\Controllers\Landlord\MaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');
    Route::get('properties/{property}/units', [\App\Http\Controllers\Landlord\MaintenanceController::class, 'getUnits'])->name('properties.units');
    
    // Profile Routes
    Route::get('profile', [\App\Http\Controllers\Landlord\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\Landlord\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile/photo', [\App\Http\Controllers\Landlord\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    
    // Message Routes
    Route::get('messages', [\App\Http\Controllers\Landlord\MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/create', [\App\Http\Controllers\Landlord\MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [\App\Http\Controllers\Landlord\MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{message}', [\App\Http\Controllers\Landlord\MessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{message}/read', [\App\Http\Controllers\Landlord\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::delete('messages/{message}', [\App\Http\Controllers\Landlord\MessageController::class, 'destroy'])->name('messages.destroy');

    // Payment Management Routes
    Route::get('payments', [\App\Http\Controllers\Landlord\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [\App\Http\Controllers\Landlord\PaymentController::class, 'create'])->name('payments.create');
    Route::get('payments/request', [\App\Http\Controllers\Landlord\PaymentController::class, 'showRequestForm'])->name('payments.request');
    Route::post('payments/send-request', [\App\Http\Controllers\Landlord\PaymentController::class, 'sendRequest'])->name('payments.send-request');
    Route::post('payments', [\App\Http\Controllers\Landlord\PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [\App\Http\Controllers\Landlord\PaymentController::class, 'show'])->name('payments.show');

    // Other landlord routes

    Route::get('/settings', [LandlordController::class, 'settings'])->name('settings');
});

// Tenant Routes (Protected by role middleware)
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [TenantController::class, 'dashboard'])->name('dashboard');
    Route::get('/payments', [\App\Http\Controllers\Tenant\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/make-payment', [\App\Http\Controllers\Tenant\PaymentController::class, 'makePayment'])->name('payments.make-payment');
    Route::get('/payments/make', [\App\Http\Controllers\Tenant\PaymentController::class, 'make'])->name('payments.make');
    Route::get('/payments/export', [\App\Http\Controllers\Tenant\PaymentController::class, 'showExportForm'])->name('payments.export');
    Route::post('/payments/export-excel', [\App\Http\Controllers\Tenant\PaymentController::class, 'exportExcel'])->name('payments.export-excel');
    
    // M-Pesa Payment Routes
    Route::post('/mpesa/stk-push', [\App\Http\Controllers\MpesaController::class, 'stkPush'])->name('mpesa.stk-push');
    Route::post('/mpesa/check-status', [\App\Http\Controllers\MpesaController::class, 'checkStatus'])->name('mpesa.check-status');
    
    Route::get('/unit-details', [TenantController::class, 'unitDetails'])->name('unit-details');
    Route::get('/messages', [TenantController::class, 'messages'])->name('messages');
    Route::get('/contact-landlord', [TenantController::class, 'contactLandlord'])->name('contact-landlord');
    Route::get('/settings', [TenantController::class, 'settings'])->name('settings');
    Route::post('/settings', [TenantController::class, 'updateSettings'])->name('settings.update');
    Route::post('/make-payment', [TenantController::class, 'makePayment'])->name('make-payment');
    
    // Maintenance Routes
    Route::get('maintenance', [\App\Http\Controllers\Tenant\MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/create', [\App\Http\Controllers\Tenant\MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('maintenance', [\App\Http\Controllers\Tenant\MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('maintenance/{maintenanceRequest}', [\App\Http\Controllers\Tenant\MaintenanceController::class, 'show'])->name('maintenance.show');
    
    // Profile Routes
    Route::get('profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\Tenant\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile/photo', [\App\Http\Controllers\Tenant\ProfileController::class, 'deletePhoto'])->name('profile.delete-photo');
    
    // Message Routes
    Route::get('messages', [\App\Http\Controllers\Tenant\MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/create', [\App\Http\Controllers\Tenant\MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [\App\Http\Controllers\Tenant\MessageController::class, 'store'])->name('messages.store');
    Route::get('messages/{message}', [\App\Http\Controllers\Tenant\MessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{message}/read', [\App\Http\Controllers\Tenant\MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::delete('messages/{message}', [\App\Http\Controllers\Tenant\MessageController::class, 'destroy'])->name('messages.destroy');
});

// Receipt Routes (Available to both landlords and tenants)
Route::middleware('auth')->group(function () {
    Route::get('/receipts', [\App\Http\Controllers\ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('/receipts/{receipt}', [\App\Http\Controllers\ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/download', [\App\Http\Controllers\ReceiptController::class, 'downloadPdf'])->name('receipts.download');
    Route::post('/receipts/{receipt}/resend-email', [\App\Http\Controllers\ReceiptController::class, 'resendEmail'])->name('receipts.resend-email');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';





