<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // Validate the request
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        try {
            // Check if email already exists - use the full table name with prefix
            $existingUser = DB::table('docuflow_users')->where('email', $request->email)->first();
            if ($existingUser) {
                return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
            }
            
            // Begin transaction
            DB::beginTransaction();
            
            // Insert user directly with DB facade - use the full table name with prefix
            $userId = DB::table('docuflow_users')->insertGetId([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'total_storage' => 256,
                'used_storage' => 0,
                'used_storage_percentage' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create welcome notification
            try {
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'type' => 'welcome',
                    'message' => 'Welcome to DocuFlow! Thank you for creating an account.',
                    'data' => json_encode([
                        'action_url' => route('dashboard'),
                        'action_text' => 'Get Started',
                    ]),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                Log::info('Welcome notification created for new user', ['user_id' => $userId]);
            } catch (\Exception $e) {
                Log::error('Failed to create welcome notification', [
                    'error' => $e->getMessage(),
                    'user_id' => $userId
                ]);
            }
            
            // Log the user creation
            Log::info('User created successfully', ['id' => $userId, 'name' => $request->username]);
            
            // Create root folder - use the full table name with prefix
            $folderId = DB::table('docuflow_folders')->insertGetId([
                'name' => 'Root',
                'user_id' => $userId,
                'parent_id' => null,
                'is_starred' => false,
                'is_trashed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Root folder created', ['folder_id' => $folderId, 'user_id' => $userId]);
            
            // Create MinIO folder in docuflow_users directory - with detailed error handling
            try {
                // Check if docuflow_users folder exists
                if (!Storage::disk('minio')->exists('docuflow_users')) {
                    Storage::disk('minio')->put('docuflow_users/.keep', '');
                    Log::info('Created docuflow_users folder');
                }
                
                // Log MinIO configuration
                Log::info('MinIO configuration', [
                    'driver' => config('filesystems.disks.minio.driver'),
                    'key' => config('filesystems.disks.minio.key'),
                    'endpoint' => config('filesystems.disks.minio.endpoint'),
                    'bucket' => config('filesystems.disks.minio.bucket')
                ]);
                
                // Create user directory in docuflow_users directory using username instead of ID
                $userPath = "docuflow_users/{$request->username}/.keep";
                $userResult = Storage::disk('minio')->put($userPath, '');
                
                // Create a subdirectory for files
                $filesPath = "docuflow_users/{$request->username}/files/.keep";
                $filesResult = Storage::disk('minio')->put($filesPath, '');
                
                // Create a subdirectory for documents
                $docsPath = "docuflow_users/{$request->username}/documents/.keep";
                $docsResult = Storage::disk('minio')->put($docsPath, '');
                
                // Create a subdirectory for images
                $imagesPath = "docuflow_users/{$request->username}/images/.keep";
                $imagesResult = Storage::disk('minio')->put($imagesPath, '');
                
                Log::info('MinIO folders creation attempt', [
                    'user_path' => $userPath,
                    'files_path' => $filesPath,
                    'docs_path' => $docsPath,
                    'images_path' => $imagesPath,
                    'user_result' => $userResult,
                    'files_result' => $filesResult,
                    'docs_result' => $docsResult,
                    'images_result' => $imagesResult
                ]);
                
                if (!$userResult || !$filesResult || !$docsResult || !$imagesResult) {
                    Log::warning('Some MinIO folders not created successfully', [
                        'user_result' => $userResult,
                        'files_result' => $filesResult,
                        'docs_result' => $docsResult,
                        'images_result' => $imagesResult
                    ]);
                }
                
            } catch (\Exception $e) {
                // Log MinIO error but continue
                Log::error('MinIO folder creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Commit transaction
            DB::commit();
            
            // Redirect to login page instead of logging in automatically
            return redirect()->route('login')->with('success', 'Registration successful! Please login with your credentials.');
            
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            // Log the error
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return with error
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }
}