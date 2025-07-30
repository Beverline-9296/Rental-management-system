<?php
/**
 * Quick script to set a known password for a tenant for testing
 * Usage: php set_tenant_password.php [tenant_email] [new_password]
 */

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get command line arguments
$email = $argv[1] ?? null;
$password = $argv[2] ?? 'password123';

if (!$email) {
    echo "Usage: php set_tenant_password.php [tenant_email] [new_password]\n";
    echo "Example: php set_tenant_password.php 1234@gmail.com password123\n\n";
    
    // Show available tenants
    $tenants = User::where('role', 'tenant')->get(['id', 'name', 'email']);
    echo "Available tenants:\n";
    foreach ($tenants as $tenant) {
        echo "- {$tenant->email} ({$tenant->name})\n";
    }
    exit(1);
}

// Find the tenant
$tenant = User::where('email', $email)->where('role', 'tenant')->first();

if (!$tenant) {
    echo "❌ Tenant with email '{$email}' not found.\n";
    exit(1);
}

// Update the password
$tenant->update([
    'password' => Hash::make($password)
]);

echo "✅ Password updated successfully!\n";
echo "📧 Email: {$tenant->email}\n";
echo "🔑 Password: {$password}\n";
echo "👤 Name: {$tenant->name}\n\n";
echo "🌐 You can now log in at: http://127.0.0.1:8000/login\n";
echo "After login, you'll be redirected to the tenant dashboard.\n";
