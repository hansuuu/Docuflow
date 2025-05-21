<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

echo "Testing MinIO connection...\n";

try {
    // Get the MinIO configuration
    $config = config('filesystems.disks.minio');
    echo "MinIO Configuration:\n";
    print_r($config);
    
    // Test if we can list files
    echo "\nListing files in bucket:\n";
    $files = Storage::disk('minio')->allFiles();
    print_r($files);
    
    // Create a test user folder
    $userId = 999; // Test user ID
    $folderPath = 'docuflow_users/' . $userId;
    
    echo "\nCreating test user folder: $folderPath\n";
    Storage::disk('minio')->put($folderPath . '/.gitkeep', 'Test file');
    
    // Create subfolders
    $subfolders = ['files', 'documents', 'images'];
    foreach ($subfolders as $subfolder) {
        $subfolderPath = $folderPath . '/' . $subfolder;
        echo "Creating subfolder: $subfolderPath\n";
        Storage::disk('minio')->put($subfolderPath . '/.gitkeep', 'Test file');
    }
    
    // Check if directories were created
    echo "Checking if directories were created:\n";
    $dirs = Storage::disk('minio')->directories('docuflow_users');
    print_r($dirs);
    
    echo "\nMinIO connection test completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}