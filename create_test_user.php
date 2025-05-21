<?php
// Save this as create_user.php in your project root
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Creating a test user...\n";

try {
    $username = 'testuser_' . time();
    $email = 'test_' . time() . '@example.com';
    $password = 'password123';
    
    echo "Username: $username\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
    
    $userId = DB::table('docuflow_users')->insertGetId([
        'name' => $username,
        'email' => $email,
        'password' => Hash::make($password),
        'total_storage' => 256,
        'used_storage' => 0,
        'used_storage_percentage' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "User created with ID: $userId\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}