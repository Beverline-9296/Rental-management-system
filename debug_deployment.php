<?php
/**
 * Deployment Debug Script
 * Add this to your routes/web.php temporarily to diagnose issues
 */

// Add this route to routes/web.php for debugging:
/*
Route::get('/debug-deployment', function () {
    $checks = [];
    
    // Check APP_KEY
    $checks['app_key'] = [
        'status' => !empty(config('app.key')),
        'value' => config('app.key') ? 'Set' : 'Missing',
        'message' => config('app.key') ? 'Application key is configured' : 'APP_KEY is missing - run php artisan key:generate'
    ];
    
    // Check Database Connection
    try {
        DB::connection()->getPdo();
        $checks['database'] = [
            'status' => true,
            'value' => 'Connected',
            'message' => 'Database connection successful'
        ];
    } catch (Exception $e) {
        $checks['database'] = [
            'status' => false,
            'value' => 'Failed',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ];
    }
    
    // Check Redis Connection
    try {
        Cache::store('redis')->put('test', 'value', 10);
        $checks['redis'] = [
            'status' => true,
            'value' => 'Connected',
            'message' => 'Redis connection successful'
        ];
    } catch (Exception $e) {
        $checks['redis'] = [
            'status' => false,
            'value' => 'Failed',
            'message' => 'Redis connection failed: ' . $e->getMessage()
        ];
    }
    
    // Check Storage Permissions
    $checks['storage'] = [
        'status' => is_writable(storage_path()),
        'value' => is_writable(storage_path()) ? 'Writable' : 'Not Writable',
        'message' => is_writable(storage_path()) ? 'Storage directory is writable' : 'Storage directory is not writable'
    ];
    
    // Check Environment
    $checks['environment'] = [
        'status' => true,
        'value' => config('app.env'),
        'message' => 'Environment: ' . config('app.env')
    ];
    
    // Check Debug Mode
    $checks['debug'] = [
        'status' => true,
        'value' => config('app.debug') ? 'Enabled' : 'Disabled',
        'message' => 'Debug mode: ' . (config('app.debug') ? 'Enabled' : 'Disabled')
    ];
    
    // Check Tables Exist
    try {
        $tables = DB::select("SHOW TABLES");
        $checks['migrations'] = [
            'status' => count($tables) > 0,
            'value' => count($tables) . ' tables',
            'message' => count($tables) > 0 ? count($tables) . ' database tables found' : 'No database tables found - migrations may not have run'
        ];
    } catch (Exception $e) {
        $checks['migrations'] = [
            'status' => false,
            'value' => 'Failed',
            'message' => 'Could not check tables: ' . $e->getMessage()
        ];
    }
    
    return response()->json([
        'status' => 'Deployment Debug Information',
        'timestamp' => now(),
        'checks' => $checks,
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ], 200, [], JSON_PRETTY_PRINT);
});
*/

echo "Add the above route to routes/web.php and visit /debug-deployment to diagnose issues\n";
?>
