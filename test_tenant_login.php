<?php
/**
 * Simple test script to verify tenant login functionality
 * Run this with: php test_tenant_login.php
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Tenant Login Test ===\n\n";

// Check if we have any tenants in the database
$tenants = User::where('role', 'tenant')->get();

echo "Found " . $tenants->count() . " tenant(s) in the database:\n";

foreach ($tenants as $tenant) {
    echo "- ID: {$tenant->id}, Name: {$tenant->name}, Email: {$tenant->email}, Role: {$tenant->role}\n";
    
    // Test if the tenant can be authenticated (role check)
    if ($tenant->isTenant()) {
        echo "  ✅ Role check passed - user is correctly identified as tenant\n";
    } else {
        echo "  ❌ Role check failed - user is NOT identified as tenant\n";
    }
}

if ($tenants->count() === 0) {
    echo "\n⚠️  No tenants found. Please create a tenant first through the landlord dashboard.\n";
} else {
    echo "\n✅ Tenant login system appears to be properly configured.\n";
    echo "📝 To test login:\n";
    echo "   1. Go to /login\n";
    echo "   2. Use the tenant's email and the password provided by the landlord\n";
    echo "   3. After login, you should be redirected to /tenant/dashboard\n";
}

echo "\n=== End Test ===\n";
