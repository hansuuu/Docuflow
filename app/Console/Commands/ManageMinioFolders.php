<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageMinioFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:manage {action} {--user=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage MinIO folders (create, fix, rename)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $username = $this->option('user');
        $all = $this->option('all');

        // Get MinIO configuration
        $endpoint = env('MINIO_ENDPOINT', 'http://localhost:9000');
        $accessKey = env('MINIO_ACCESS_KEY', 'minioadmin');
        $secretKey = env('MINIO_SECRET_KEY', 'minioadmin');
        $bucket = env('MINIO_BUCKET', 'docuflow');
        $region = env('MINIO_REGION', 'us-east-1');

        // Create MinIO client
        $client = new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);

        switch ($action) {
            case 'create':
                if ($username) {
                    $this->createUserFolder($client, $bucket, $username);
                } elseif ($all) {
                    $users = DB::table('docuflow_users')->select('name')->get();
                    $this->info("Creating folders for " . count($users) . " users...");
                    
                    foreach ($users as $user) {
                        $this->createUserFolder($client, $bucket, $user->name);
                    }
                } else {
                    $this->error("Please specify a username with --user or use --all to process all users.");
                }
                break;
                
            case 'fix':
                if ($all) {
                    $this->fixAllFolders($client, $bucket);
                } else {
                    $this->error("The fix action requires the --all option.");
                }
                break;
                
            case 'rename':
                if ($username) {
                    $user = DB::table('docuflow_users')->where('name', $username)->first();
                    if ($user) {
                        $this->renameUserFolder($client, $bucket, $user->id, $user->name);
                    } else {
                        $this->error("User not found: {$username}");
                    }
                } elseif ($all) {
                    $users = DB::table('docuflow_users')->select('id', 'name')->get();
                    $this->info("Renaming folders for " . count($users) . " users...");
                    
                    foreach ($users as $user) {
                        $this->renameUserFolder($client, $bucket, $user->id, $user->name);
                    }
                } else {
                    $this->error("Please specify a username with --user or use --all to process all users.");
                }
                break;
                
            default:
                $this->error("Unknown action: {$action}. Available actions: create, fix, rename");
        }
    }

    private function createUserFolder($client, $bucket, $username)
    {
        $this->info("Creating folder for user: {$username}");
        
        try {
            // Create the main folder
            $folderKey = "docuflow_users/{$username}/";
            $client->putObject([
                'Bucket' => $bucket,
                'Key' => $folderKey,
                'Body' => '',
            ]);
            
            // Create standard subfolders
            $subfolders = ['.keep', 'documents', 'files', 'images'];
            foreach ($subfolders as $subfolder) {
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key' => "{$folderKey}{$subfolder}/",
                    'Body' => '',
                ]);
            }
            
            $this->info("Successfully created folder for user: {$username}");
        } catch (\Exception $e) {
            $this->error("Failed to create folder for user {$username}: " . $e->getMessage());
        }
    }

    private function renameUserFolder($client, $bucket, $userId, $username)
    {
        $this->info("Renaming folder for user: {$username} (ID: {$userId})");
        
        // Check if ID folder exists
        $idFolderKey = "docuflow_users/{$userId}/";
        $idFolderExists = false;
        
        try {
            // List objects with the ID prefix
            $objects = $client->listObjects([
                'Bucket' => $bucket,
                'Prefix' => $idFolderKey,
                'MaxKeys' => 1
            ]);
            
            $idFolderExists = count($objects['Contents'] ?? []) > 0;
        } catch (\Exception $e) {
            $this->error("Error checking ID folder: " . $e->getMessage());
            return;
        }
        
        if ($idFolderExists) {
            $this->info("ID folder exists, copying contents to username folder...");
            
            // Create username folder
            $usernameFolderKey = "docuflow_users/{$username}/";
            
            try {
                // Create the main folder
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $usernameFolderKey,
                    'Body' => '',
                ]);
                
                // List all objects in the ID folder
                $objects = $client->listObjects([
                    'Bucket' => $bucket,
                    'Prefix' => $idFolderKey
                ]);
                
                // Copy each object to the new location
                foreach ($objects['Contents'] ?? [] as $object) {
                    $sourceKey = $object['Key'];
                    $destinationKey = str_replace($idFolderKey, $usernameFolderKey, $sourceKey);
                    
                    $this->line("  Copying {$sourceKey} to {$destinationKey}");
                    
                    $client->copyObject([
                        'Bucket' => $bucket,
                        'CopySource' => "{$bucket}/{$sourceKey}",
                        'Key' => $destinationKey
                    ]);
                }
                
                $this->info("Successfully renamed folder for user: {$username}");
                
                // Optionally delete the ID folder
                if ($this->confirm("Do you want to delete the old ID folder?")) {
                    foreach ($objects['Contents'] ?? [] as $object) {
                        $client->deleteObject([
                            'Bucket' => $bucket,
                            'Key' => $object['Key']
                        ]);
                    }
                    $this->info("ID folder deleted.");
                }
                
            } catch (\Exception $e) {
                $this->error("Error renaming folder: " . $e->getMessage());
            }
        } else {
            $this->info("ID folder does not exist, creating new username folder...");
            $this->createUserFolder($client, $bucket, $username);
        }
    }

    private function fixAllFolders($client, $bucket)
    {
        $this->info("Fixing all user folders...");
        
        // Get all users
        $users = DB::table('docuflow_users')->select('id', 'name')->get();
        
        foreach ($users as $user) {
            $this->renameUserFolder($client, $bucket, $user->id, $user->name);
        }
        
        $this->info("All user folders have been fixed.");
    }
}