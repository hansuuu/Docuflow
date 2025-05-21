<?php
// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "Testing direct registration...\n";

// Generate unique email
$email = 'direct_test_' . time() . '@example.com';
$name = 'DirectTest' . time();

try {
    // Insert user directly
    $userId = DB::table('users')->insertGetId([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make('password123'),
        'total_storage' => 256,
        'used_storage' => 0,
        'used_storage_percentage' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "User created with ID: $userId\n";
    
    // Verify user exists
    $user = DB::table('users')->where('id', $userId)->first();
    
    if ($user) {
        echo "User verification successful!\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
    } else {
        echo "Failed to verify user!\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}