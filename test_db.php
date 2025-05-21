<?php
// Save this as test_db.php in your project root
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing database connection...\n";

try {
    // Test the connection
    $pdo = DB::connection()->getPdo();
    echo "Connected successfully to database: " . DB::connection()->getDatabaseName() . "\n";
    
    // Try a simple query
    $users = DB::table('docuflow_users')->count();
    echo "Number of users in docuflow_users table: $users\n";
    
    // Try to insert a test record
    $testId = DB::table('docuflow_users')->insertGetId([
        'name' => 'test_' . time(),
        'email' => 'test_' . time() . '@example.com',
        'password' => bcrypt('password123'),
        'total_storage' => 256,
        'used_storage' => 0,
        'used_storage_percentage' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Test user inserted with ID: $testId\n";
    
    // Delete the test record
    DB::table('docuflow_users')->where('id', $testId)->delete();
    echo "Test user deleted\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}