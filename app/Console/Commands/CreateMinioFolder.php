<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateMinioFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:create-folder {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a folder in MinIO for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $this->info("Creating MinIO folder for user: {$username}");

        try {
            // Get MinIO configuration
            $endpoint = env('MINIO_ENDPOINT', 'http://localhost:9000');
            $accessKey = env('MINIO_ACCESS_KEY', 'minioadmin');
            $secretKey = env('MINIO_SECRET_KEY', 'minioadmin');
            $bucket = env('MINIO_BUCKET', 'docuflow');
            $region = env('MINIO_REGION', 'us-east-1');

            $this->info("Using endpoint: {$endpoint}");

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

            // Create a folder for the user using username
            $folderKey = "docuflow_users/{$username}/";
            $client->putObject([
                'Bucket' => $bucket,
                'Key' => $folderKey,
                'Body' => '',
            ]);

            // Create standard subfolders
            $subfolders = ['.keep', 'documents', 'files', 'images'];
            foreach ($subfolders as $subfolder) {
                $this->info("Creating subfolder: {$subfolder}");
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key' => "{$folderKey}{$subfolder}/",
                    'Body' => '',
                ]);
            }

            $this->info("MinIO folder created successfully: {$folderKey}");
            Log::info('MinIO folder created successfully via command', ['username' => $username, 'folder' => $folderKey]);

        } catch (\Exception $e) {
            $this->error("Failed to create MinIO folder: {$e->getMessage()}");
            Log::error('Failed to create MinIO folder via command', [
                'username' => $username,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}