<?php
// diagnostic.php (place in your project root)

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "DocuFlow Diagnostic Tool\n";
echo "=======================\n\n";

// Check database connection
echo "Checking database connection...\n";
try {
    DB::connection()->getPdo();
    echo "✅ Connected to database: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    echo "❌ Could not connect to the database. Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Check tables
echo "Checking tables...\n";
$requiredTables = ['docuflow_users', 'docuflow_folders', 'migrations', 'failed_jobs', 'password_reset_tokens'];
$missingTables = [];

foreach ($requiredTables as $table) {
    if (Schema::hasTable($table)) {
        echo "✅ Table '$table' exists\n";
        
        // Check columns for specific tables
        if ($table === 'docuflow_users') {
            $requiredColumns = ['id', 'name', 'email', 'password', 'total_storage', 'used_storage', 'used_storage_percentage'];
            foreach ($requiredColumns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    echo "  ✅ Column '$column' exists\n";
                } else {
                    echo "  ❌ Column '$column' is missing\n";
                }
            }
        }
        
        if ($table === 'docuflow_folders') {
            $requiredColumns = ['id', 'name', 'user_id', 'parent_id', 'is_starred', 'is_trashed'];
            foreach ($requiredColumns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    echo "  ✅ Column '$column' exists\n";
                } else {
                    echo "  ❌ Column '$column' is missing\n";
                }
            }
        }
    } else {
        echo "❌ Table '$table' does not exist\n";
        $missingTables[] = $table;
    }
}

echo "\n";

// Check for other tables
echo "Other tables in the database:\n";
$tables = DB::select('SHOW TABLES');
$tableColumn = 'Tables_in_' . DB::connection()->getDatabaseName();

foreach ($tables as $table) {
    $tableName = $table->$tableColumn;
    if (!in_array($tableName, $requiredTables)) {
        echo "- $tableName\n";
    }
}

echo "\n";

// Check for users
echo "Checking for users in docuflow_users table...\n";
if (Schema::hasTable('docuflow_users')) {
    $userCount = DB::table('docuflow_users')->count();
    echo "Found $userCount users\n";
    
    if ($userCount > 0) {
        $users = DB::table('docuflow_users')->select('id', 'name', 'email')->get();
        echo "User list:\n";
        foreach ($users as $user) {
            echo "- ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
        }
    }
} else {
    echo "Cannot check users because the docuflow_users table does not exist\n";
}

echo "\n";

// Test user creation
echo "Testing user creation...\n";
if (Schema::hasTable('docuflow_users')) {
    try {
        $testEmail = 'test_' . time() . '@example.com';
        $userId = DB::table('docuflow_users')->insertGetId([
            'name' => 'Test User',
            'email' => $testEmail,
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'total_storage' => 256,
            'used_storage' => 0,
            'used_storage_percentage' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✅ Test user created with ID: $userId and email: $testEmail\n";
        
        // Test folder creation
        if (Schema::hasTable('docuflow_folders')) {
            try {
                $folderId = DB::table('docuflow_folders')->insertGetId([
                    'name' => 'Test Root',
                    'user_id' => $userId,
                    'parent_id' => null,
                    'is_starred' => false,
                    'is_trashed' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "✅ Test folder created with ID: $folderId for user ID: $userId\n";
            } catch (\Exception $e) {
                echo "❌ Failed to create test folder. Error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ Cannot create test folder because the docuflow_folders table does not exist\n";
        }
    } catch (\Exception $e) {
        echo "❌ Failed to create test user. Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Cannot create test user because the docuflow_users table does not exist\n";
}

echo "\n";

// Check auth configuration
echo "Checking auth configuration...\n";
$authConfig = config('auth');
echo "Auth providers:\n";
foreach ($authConfig['providers'] as $name => $provider) {
    echo "- $name: model = {$provider['model']}, driver = {$provider['driver']}\n";
}

echo "\nAuth guards:\n";
foreach ($authConfig['guards'] as $name => $guard) {
    echo "- $name: provider = {$guard['provider']}, driver = {$guard['driver']}\n";
}

echo "\n";

// Recommendations
echo "Recommendations:\n";
if (count($missingTables) > 0) {
    echo "❌ Create the missing tables: " . implode(', ', $missingTables) . "\n";
    echo "   Run: php artisan migrate\n";
}

// Check if the users model is configured correctly
$userModel = config('auth.providers.users.model');
echo "✅ Your configured user model is: $userModel\n";

if ($userModel === 'App\\Models\\User') {
    echo "   Make sure this model is configured to use the 'docuflow_users' table\n";
    echo "   Add 'protected \$table = 'docuflow_users';' to your User model\n";
}

echo "\nDiagnostic complete!\n";