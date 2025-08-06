<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PropertyController;
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
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Get recent properties with their unit counts
        $properties = \App\Models\Property::where('landlord_id', $user->id)
            ->withCount('units')
            ->withCount(['units as occupied_units' => function($query) {
                $query->where('status', 'occupied');
            }])
            ->withCount(['units as vacant_units' => function($query) {
                $query->where('status', 'vacant');
            }])
            ->latest()
            ->take(5)
            ->get();
            
        // Get summary statistics
        $total_properties = \App\Models\Property::where('landlord_id', $user->id)->count();
        
        // Get total occupied units
        $total_occupied_units = \App\Models\Unit::whereHas('property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->where('status', 'occupied')
            ->count();
            
        // Get total unique tenants
        $total_tenants = \App\Models\TenantAssignment::whereHas('unit.property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->where('status', 'active')
            ->distinct('tenant_id')
            ->count('tenant_id');
        
        // Calculate total arrears (placeholder - update with your actual arrears calculation)
        $total_arrears = 0;
        
        return view('landlord.dashboard', [
            'properties' => $properties,
            'user' => $user,
            'total_properties' => $total_properties,
            'total_tenants' => $total_tenants,
            'occupied_units' => $total_occupied_units,
            'total_arrears' => $total_arrears
        ]);
    })->name('dashboard');
    
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
    Route::post('payments', [\App\Http\Controllers\Landlord\PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [\App\Http\Controllers\Landlord\PaymentController::class, 'show'])->name('payments.show');

    // Other landlord routes

    Route::get('/settings', [LandlordController::class, 'settings'])->name('settings');
});

// Tenant Routes (Protected by role middleware)
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/dashboard', [TenantController::class, 'dashboard'])->name('dashboard');
    Route::get('/payments', [\App\Http\Controllers\Tenant\PaymentController::class, 'index'])->name('payments');
    Route::get('/unit-details', [TenantController::class, 'unitDetails'])->name('unit-details');
    Route::get('/messages', [TenantController::class, 'messages'])->name('messages');
    Route::get('/contact-landlord', [TenantController::class, 'contactLandlord'])->name('contact-landlord');
    Route::get('/settings', [TenantController::class, 'settings'])->name('settings');
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';





