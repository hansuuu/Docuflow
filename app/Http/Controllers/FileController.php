<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
use App\Models\Folder;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\SharingPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FileController extends Controller
{
/**
 * Display a listing of the files.
 *
 * @return \Illuminate\View\View
 */
public function index()
{
    $files = auth()->user()->files()->notTrashed()->get();
    return view('files.index', compact('files'));
}

/**
 * Show the form for creating a new file.
 *
 * @return \Illuminate\View\View
 */
public function create()
{
    $folders = auth()->user()->folders()->notTrashed()->get();
    return view('files.create', compact('folders'));
}

/**
 * Store a newly created file in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    $request->validate([
        'files.*' => 'required|file|max:10240', // 10MB max
        'folder_id' => 'nullable|exists:folders,id',
    ]);

    $uploadCount = 0;
    $errorCount = 0;
    $totalSize = 0;
    $user = auth()->user();
    $uploadedFileNames = [];

    // Ensure folders exist
    $this->ensureUserFoldersExist($user->name);

    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $uploadedFile) {
            try {
                // Determine the appropriate subfolder based on mime type
                $mimeType = $uploadedFile->getMimeType();
                $subfolder = $this->determineSubfolder($mimeType);
                
                // Check if user has enough storage
                $fileSize = $uploadedFile->getSize();
                if (!$user->hasEnoughStorage($fileSize)) {
                    return redirect()->back()->with('error', 'Not enough storage space. Please free up some space or upgrade your plan.');
                }
                
                // Get original filename
                $originalName = $uploadedFile->getClientOriginalName();
                
                // Create the path using username instead of user ID
                $path = "docuflow_users/{$user->name}/{$subfolder}/{$originalName}";
                
                // Check if file with same name exists
                if (Storage::disk('minio')->exists($path)) {
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = $uploadedFile->getClientOriginalExtension();
                    $path = "docuflow_users/{$user->name}/{$subfolder}/{$filename}_" . time() . ".{$extension}";
                }
                
                // Upload file to MinIO using put method instead of store
                Storage::disk('minio')->put($path, file_get_contents($uploadedFile));
                
                // Log the file upload
                Log::info('File uploaded successfully', [
                    'user' => $user->name,
                    'path' => $path,
                    'size' => $fileSize,
                    'mime_type' => $mimeType
                ]);
                
                // Create the file record
                File::create([
                    'name' => $originalName,
                    'path' => $path,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'user_id' => auth()->id(),
                    'folder_id' => $request->folder_id,
                ]);
                
                // Create file upload notification
                try {
                    DB::table('notifications')->insert([
                        'user_id' => auth()->id(),
                        'type' => 'file_uploaded',
                        'message' => "File uploaded successfully: {$originalName}",
                        'data' => json_encode([
                            'file_name' => $originalName,
                            'file_size' => $fileSize,
                            'mime_type' => $mimeType,
                        ]),
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create file upload notification', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id()
                    ]);
                }
                
                $uploadCount++;
                $totalSize += $fileSize;
                $uploadedFileNames[] = $originalName;
            } catch (\Exception $e) {
                Log::error('File upload error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user' => $user->name
                ]);
                $errorCount++;
            }
        }
        
        // Update user's storage usage with the total size of uploaded files
        if ($totalSize > 0) {
            $user->updateStorageUsage($totalSize);
        } else {
            // If no files were uploaded or we need to recalculate
            $user->updateStorageUsage();
        }
        
        if ($uploadCount > 0) {
            $message = $uploadCount . ' file(s) uploaded successfully!';
            
            // Add file names to the message if there are only a few
            if ($uploadCount <= 3) {
                $message .= ' (' . implode(', ', $uploadedFileNames) . ')';
            }
            
            if ($errorCount > 0) {
                $message .= ' ' . $errorCount . ' file(s) failed to upload.';
            }
            return redirect()->route('dashboard')->with('success', $message);
        }
    }
    
    return redirect()->back()->with('error', 'No files were uploaded.');
}

/**
 * Display the specified file.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function show($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    return view('files.show', compact('file'));
}

/**
 * Download the specified file.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function download($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    return Storage::disk('minio')->download($file->path, $file->name);
}

/**
 * Remove the specified file from storage.
 *
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function delete($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    $file->update(['is_trashed' => true]);
    
    return redirect()->back()->with('success', 'File moved to trash.');
}

/**
 * Permanently remove the file from storage.
 *
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroy($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    
    // Delete from storage
    Storage::disk('minio')->delete($file->path);
    
    // Delete from database
    $file->delete();
    
    // Update user's storage usage
    auth()->user()->updateStorageUsage();
    
    return redirect()->back()->with('success', 'File permanently deleted.');
}

/**
 * Restore the file from trash.
 *
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function restore($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    $file->update(['is_trashed' => false]);
    
    return redirect()->back()->with('success', 'File restored from trash.');
}

/**
 * Toggle the starred status of a file.
 *
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function toggleStar($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    $file->update(['is_starred' => !$file->is_starred]);
    
    $message = $file->is_starred 
        ? "File '{$file->name}' added to starred." 
        : "File '{$file->name}' removed from starred.";
    
    return redirect()->back()->with('success', $message);
}

/**
 * Empty the trash by permanently deleting all trashed files and folders.
 *
 * @return \Illuminate\Http\RedirectResponse
 */



 public function starred()
{
    $user = auth()->user();

    
    return view('starred', [
        'starredFolders' => $user->folders()->where('is_starred',true)->withCount('files')-get
    (),
        'starredFiles' => $user->files()->where('is_starred', true)->get()

    ]);
}
public function emptyTrash()
{
    $user = auth()->user();
    $trashedFiles = $user->trashedFiles()->get();
    $trashedFolders = $user->trashedFolders()->get();
    
    $fileCount = $trashedFiles->count();
    $folderCount = $trashedFolders->count();
    
    // Delete all trashed files from storage
    foreach ($trashedFiles as $file) {
        Storage::disk('minio')->delete($file->path);
        $file->delete(); // Permanently delete from database
    }
    
    // Delete all trashed folders
    foreach ($trashedFolders as $folder) {
        // Find all files in this folder and delete them from storage
        $folderFiles = File::where('folder_id', $folder->id)->get();
        foreach ($folderFiles as $file) {
            Storage::disk('minio')->delete($file->path);
            $file->delete();
        }
        
        $folder->delete(); // Permanently delete folder from database
    }
    
    // Update user's storage usage
    $user->updateStorageUsage();
    
    $totalItems = $fileCount + $folderCount;
    $message = $totalItems > 0 
        ? "Successfully deleted $fileCount files and $folderCount folders from trash." 
        : "Trash was already empty.";
    
    return redirect()->route('trash')->with('success', $message);
}

/**
 * Upload files.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function upload(Request $request)
{
    $request->validate([
        'files.*' => 'nullable|file|max:10240', // 10MB max
        'folder.*' => 'nullable|file|max:10240', // 10MB max for folder files
        'folder_id' => 'nullable|exists:folders,id',
    ]);

    $uploadCount = 0;
    $errorCount = 0;
    $totalSize = 0;
    $user = auth()->user();
    $uploadedFileNames = [];

    // Ensure folders exist
    $this->ensureUserFoldersExist($user->name);

    // Handle individual file uploads
    if ($request->hasFile('files')) {
        foreach ($request->file('files') as $uploadedFile) {
            try {
                // Determine the appropriate subfolder based on mime type
                $mimeType = $uploadedFile->getMimeType();
                $subfolder = $this->determineSubfolder($mimeType);
                
                // Check if user has enough storage
                $fileSize = $uploadedFile->getSize();
                if (!$user->hasEnoughStorage($fileSize)) {
                    return redirect()->back()->with('error', 'Not enough storage space. Please free up some space or upgrade your plan.');
                }
                
                // Get original filename
                $originalName = $uploadedFile->getClientOriginalName();
                
                // Create the path using username instead of user ID
                $path = "docuflow_users/{$user->name}/{$subfolder}/{$originalName}";
                
                // Check if file with same name exists
                if (Storage::disk('minio')->exists($path)) {
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $extension = $uploadedFile->getClientOriginalExtension();
                    $path = "docuflow_users/{$user->name}/{$subfolder}/{$filename}_" . time() . ".{$extension}";
                }
                
                // Upload file to MinIO using put method instead of store
                Storage::disk('minio')->put($path, file_get_contents($uploadedFile));
                
                // Log the file upload attempt
                Log::info('File upload successful', [
                    'original_name' => $originalName,
                    'mime_type' => $mimeType,
                    'size' => $fileSize,
                    'path' => $path,
                    'subfolder' => $subfolder,
                    'user' => $user->name
                ]);
                
                // Create the file record
                File::create([
                    'name' => $originalName,
                    'path' => $path,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                    'user_id' => auth()->id(),
                    'folder_id' => $request->folder_id,
                ]);
                
                // Create file upload notification
                try {
                    DB::table('notifications')->insert([
                        'user_id' => auth()->id(),
                        'type' => 'file_uploaded',
                        'message' => "File uploaded successfully: {$originalName}",
                        'data' => json_encode([
                            'file_name' => $originalName,
                            'file_size' => $fileSize,
                            'mime_type' => $mimeType,
                        ]),
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create file upload notification', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id()
                    ]);
                }
                
                $uploadCount++;
                $totalSize += $fileSize;
                $uploadedFileNames[] = $originalName;
            } catch (\Exception $e) {
                Log::error('File upload error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user' => $user->name
                ]);
                $errorCount++;
            }
        }
    }

    // Handle folder uploads
    if ($request->hasFile('folder')) {
        $folderFiles = $request->file('folder');
        $folderStructure = [];
        
        // Group files by directory
        foreach ($folderFiles as $folderFile) {
            $relativePath = $folderFile->getClientOriginalName();
            $pathParts = explode('/', $relativePath);
            
            if (count($pathParts) > 1) {
                $fileName = array_pop($pathParts);
                $folderPath = implode('/', $pathParts);
                
                if (!isset($folderStructure[$folderPath])) {
                    $folderStructure[$folderPath] = [];
                }
                
                $folderStructure[$folderPath][] = [
                    'file' => $folderFile,
                    'name' => $fileName
                ];
            } else {
                // This is a file directly in the selected folder
                try {
                    // Determine the appropriate subfolder based on mime type
                    $mimeType = $folderFile->getMimeType();
                    $subfolder = $this->determineSubfolder($mimeType);
                    
                    // Check if user has enough storage
                    $fileSize = $folderFile->getSize();
                    if (!$user->hasEnoughStorage($fileSize)) {
                        return redirect()->back()->with('error', 'Not enough storage space. Please free up some space or upgrade your plan.');
                    }
                    
                    // Get original filename
                    $originalName = $folderFile->getClientOriginalName();
                    
                    // Create the path using username instead of user ID
                    $path = "docuflow_users/{$user->name}/{$subfolder}/{$originalName}";
                    
                    // Check if file with same name exists
                    if (Storage::disk('minio')->exists($path)) {
                        $filename = pathinfo($originalName, PATHINFO_FILENAME);
                        $extension = $folderFile->getClientOriginalExtension();
                        $path = "docuflow_users/{$user->name}/{$subfolder}/{$filename}_" . time() . ".{$extension}";
                    }
                    
                    // Upload file to MinIO using put method instead of store
                    Storage::disk('minio')->put($path, file_get_contents($folderFile));
                    
                    // Create the file record
                    File::create([
                        'name' => $originalName,
                        'path' => $path,
                        'size' => $fileSize,
                        'mime_type' => $mimeType,
                        'user_id' => auth()->id(),
                        'folder_id' => $request->folder_id,
                    ]);
                    
                    // Create file upload notification
                    try {
                        DB::table('notifications')->insert([
                            'user_id' => auth()->id(),
                            'type' => 'file_uploaded',
                            'message' => "File uploaded successfully: {$originalName}",
                            'data' => json_encode([
                                'file_name' => $originalName,
                                'file_size' => $fileSize,
                                'mime_type' => $mimeType,
                            ]),
                            'is_read' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create file upload notification', [
                            'error' => $e->getMessage(),
                            'user_id' => auth()->id()
                        ]);
                    }
                    
                    $uploadCount++;
                    $totalSize += $fileSize;
                    $uploadedFileNames[] = $originalName;
                } catch (\Exception $e) {
                    Log::error('File upload error: ' . $e->getMessage(), [
                        'exception' => $e,
                        'user' => $user->name
                    ]);
                    $errorCount++;
                }
            }
        }
        
        // Create folders and upload files
        foreach ($folderStructure as $folderPath => $files) {
            $folderParts = explode('/', $folderPath);
            $parentId = $request->folder_id;
            
            // Create folder hierarchy
            foreach ($folderParts as $folderName) {
                $folder = Folder::firstOrCreate([
                    'name' => $folderName,
                    'user_id' => auth()->id(),
                    'parent_id' => $parentId,
                ]);
                
                $parentId = $folder->id;
            }
            
            // Upload files to the folder
            foreach ($files as $fileData) {
                try {
                    // Determine the appropriate subfolder based on mime type
                    $mimeType = $fileData['file']->getMimeType();
                    $subfolder = $this->determineSubfolder($mimeType);
                    
                    // Check if user has enough storage
                    $fileSize = $fileData['file']->getSize();
                    if (!$user->hasEnoughStorage($fileSize)) {
                        return redirect()->back()->with('error', 'Not enough storage space. Please free up some space or upgrade your plan.');
                    }
                    
                    // Get original filename
                    $originalName = $fileData['name'];
                    
                    // Create the path using username instead of user ID
                    $path = "docuflow_users/{$user->name}/{$subfolder}/{$originalName}";
                    
                    // Check if file with same name exists
                    if (Storage::disk('minio')->exists($path)) {
                        $filename = pathinfo($originalName, PATHINFO_FILENAME);
                        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                        $path = "docuflow_users/{$user->name}/{$subfolder}/{$filename}_" . time() . ".{$extension}";
                    }
                    
                    // Upload file to MinIO using put method instead of store
                    Storage::disk('minio')->put($path, file_get_contents($fileData['file']));
                    
                    // Create the file record
                    File::create([
                        'name' => $originalName,
                        'path' => $path,
                        'size' => $fileSize,
                        'mime_type' => $fileData['file']->getMimeType(),
                        'user_id' => auth()->id(),
                        'folder_id' => $parentId,
                    ]);
                    
                    // Create file upload notification
                    try {
                        DB::table('notifications')->insert([
                            'user_id' => auth()->id(),
                            'type' => 'file_uploaded',
                            'message' => "File uploaded successfully: {$originalName}",
                            'data' => json_encode([
                                'file_name' => $originalName,
                                'file_size' => $fileSize,
                                'mime_type' => $mimeType,
                            ]),
                            'is_read' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create file upload notification', [
                            'error' => $e->getMessage(),
                            'user_id' => auth()->id()
                        ]);
                    }
                    
                    $uploadCount++;
                    $totalSize += $fileSize;
                    $uploadedFileNames[] = $originalName;
                } catch (\Exception $e) {
                    Log::error('File upload error: ' . $e->getMessage(), [
                        'exception' => $e,
                        'user' => $user->name
                    ]);
                    $errorCount++;
                }
            }
        }
    }
    
    // Update user's storage usage with the total size of uploaded files
    if ($totalSize > 0) {
        $user->updateStorageUsage($totalSize);
    } else {
        // If no files were uploaded or we need to recalculate
        $user->updateStorageUsage();
    }
    
    if ($uploadCount > 0) {
        $message = $uploadCount . ' file(s) uploaded successfully!';
        
        // Add file names to the message if there are only a few
        if ($uploadCount <= 3) {
            $message .= ' (' . implode(', ', $uploadedFileNames) . ')';
        }
        
        if ($errorCount > 0) {
            $message .= ' ' . $errorCount . ' file(s) failed to upload.';
        }
        return redirect()->back()->with('success', $message);
    }
    
    return redirect()->back()->with('error', 'No files were uploaded.');
}

/**
 * Generate a thumbnail for an image file.
 *
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function thumbnail($id)
{
    $file = auth()->user()->files()->findOrFail($id);
    
    // Check if file is an image
    if (!in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif'])) {
        abort(400, 'File is not an image');
    }
    
    // Get the file content
    $content = Storage::disk('minio')->get($file->path);
    
    // Return the image with appropriate headers
    return response($content)->header('Content-Type', $file->mime_type);
}




/**
 * Determine the appropriate subfolder based on file type.
 *
 * @param  string  $mimeType
 * @return string
 */
private function determineSubfolder($mimeType)
{
    if (strpos($mimeType, 'image/') === 0) {
        return 'images';
    } elseif (in_array($mimeType, [
        'application/pdf', 
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain'
    ])) {
        return 'documents';
    } else {
        return 'files';
    }
}

/**
 * Ensure the user's storage folders exist.
 *
 * @param  string  $username
 * @return void
 */
private function ensureUserFoldersExist($username)
{
    // Check if docuflow_users folder exists
    if (!Storage::disk('minio')->exists('docuflow_users')) {
        Storage::disk('minio')->put('docuflow_users/.keep', '');
        Log::info('Created docuflow_users folder');
    }
    
    // Check if user folder exists
    $userPath = "docuflow_users/{$username}";
    if (!Storage::disk('minio')->exists($userPath)) {
        Storage::disk('minio')->put("{$userPath}/.keep", '');
        Log::info("Created user folder: {$userPath}");
    }
    
    // Check if subfolders exist
    $subfolders = ['documents', 'images', 'files'];
    foreach ($subfolders as $subfolder) {
        $folderPath = "{$userPath}/{$subfolder}";
        if (!Storage::disk('minio')->exists($folderPath)) {
            Storage::disk('minio')->put("{$folderPath}/.keep", '');
            Log::info("Created subfolder: {$folderPath}");
        }
    }
}

/**
 * Show the file sharing page.
 *
 * @return \Illuminate\View\View
 */
public function showSharePage()
{
    // Get the user's files for the dropdown (only non-trashed files)
    $files = auth()->check() ? auth()->user()->files()->where('is_trashed', false)->get() : collect();
    
    // Get the user's file shares
    $fileShares = auth()->check() ? auth()->user()->fileShares : collect();
    
    return view('share', compact('files', 'fileShares'));
}

/**
 * Share a file with another user.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $fileId
 * @return \Illuminate\Http\RedirectResponse
 */
public function share(Request $request, $fileId)
{
    $request->validate([
        'email' => 'required|email',
        'permission' => 'required|in:view,edit',
    ]);

    // Find the file
    $file = auth()->user()->files()->findOrFail($fileId);

    // Create a unique token for this share
    $token = Str::random(32);

    // Check if this file is already shared with this email
    $existingShare = FileShare::where('file_id', $file->id)
        ->where('user_id', auth()->id())
        ->where('shared_with', $request->email)
        ->first();

    if ($existingShare) {
        // Update the existing share
        $existingShare->update([
            'permission' => $request->permission,
            'expires_at' => $request->has('expires') ? now()->addDays(7) : null,
        ]);

        $message = "File sharing updated for {$request->email}.";
    } else {
        // Create the file share record
        FileShare::create([
            'file_id' => $file->id,
            'user_id' => auth()->id(),
            'shared_with' => $request->email,
            'permission' => $request->permission,
            'token' => $token,
            'expires_at' => $request->has('expires') ? now()->addDays(7) : null,
            'message' => $request->message,
        ]);

        // Create file sharing notification for the recipient (if they are a user)
        try {
            $recipientUser = User::where('email', $request->email)->first();
            if ($recipientUser) {
                DB::table('notifications')->insert([
                    'user_id' => $recipientUser->id,
                    'type' => 'file_shared',
                    'message' => auth()->user()->name . " shared a file with you: {$file->name}",
                    'data' => json_encode([
                        'file_id' => $file->id,
                        'file_name' => $file->name,
                        'shared_by' => auth()->user()->name,
                        'permission' => $request->permission,
                    ]),
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create file sharing notification', [
                'error' => $e->getMessage(),
                'recipient_email' => $request->email
            ]);
        }

        $message = "File shared successfully with {$request->email}!";
    }

    // Send notification if requested
    if ($request->has('notify')) {
        try {
            // Here you would typically send an email notification
            // Mail::to($request->email)->send(new FileShared($file, auth()->user(), $token, $request->message));
            Log::info('Share notification would be sent', [
                'to' => $request->email,
                'file' => $file->name,
                'from' => auth()->user()->name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send share notification', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);
        }
    }

    return redirect()->route('share')->with('success', $message);
}

/**
 * Remove a file share.
 *
 * @param  int  $id
 * @return \Illuminate\Http\RedirectResponse
 */
public function unshare($id)
{
    $fileShare = FileShare::where('user_id', auth()->id())->findOrFail($id);
    $email = $fileShare->shared_with;
    $fileShare->delete();
    
    return redirect()->route('share')->with('success', "File access for {$email} has been removed.");
}

/**
 * Generate a public sharing link for a file.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $fileId
 * @return \Illuminate\Http\RedirectResponse
 */
public function generateLink(Request $request, $fileId)
{
    $request->validate([
        'password' => 'nullable|string|min:6',
        'expires' => 'nullable|in:1,7,30,never',
    ]);

    // Find the file
    $file = auth()->user()->files()->findOrFail($fileId);

    // Create a unique token for this share
    $token = Str::random(32);
    
    // Calculate expiration date if set
    $expiresAt = null;
    if ($request->expires) {
        if ($request->expires != 'never') {
            $expiresAt = now()->addDays((int)$request->expires);
        }
    }

    // Create or update the public share
    $fileShare = FileShare::updateOrCreate(
        [
            'file_id' => $file->id,
            'user_id' => auth()->id(),
            'shared_with' => null, // Public share has no specific recipient
        ],
        [
            'permission' => 'view',
            'token' => $token,
            'password' => $request->password ? bcrypt($request->password) : null,
            'expires_at' => $expiresAt,
            'is_public' => true,
        ]
    );

    // Generate the public URL
    $publicUrl = route('files.public', ['token' => $token]);

    return redirect()->route('share')->with([
        'success' => 'Public sharing link generated successfully!',
        'public_url' => $publicUrl
    ]);
}

/**
 * Access a publicly shared file.
 *
 * @param  string  $token
 * @return \Illuminate\Http\Response
 */
public function publicAccess($token)
{
    // Find the file share by token
    $fileShare = FileShare::where('token', $token)
        ->where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->first();

    if (!$fileShare) {
        abort(404, 'This link has expired or does not exist.');
    }

    // If password protected, show password form
    if ($fileShare->password) {
        return view('files.password', compact('fileShare', 'token'));
    }

    // Get the file
    $file = File::findOrFail($fileShare->file_id);

    // Log the access
    Log::info('Public file accessed', [
        'file_id' => $file->id,
        'file_name' => $file->name,
        'token' => $token,
        'ip' => request()->ip()
    ]);

    // If it's a viewable file type, show it in the browser
    if (in_array($file->mime_type, [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
        'text/plain'
    ])) {
        $fileContent = Storage::disk('minio')->get($file->path);
        return response($fileContent)->header('Content-Type', $file->mime_type);
    }

    // Otherwise, force download
    return Storage::disk('minio')->download($file->path, $file->name);
}

/**
 * Verify password for password-protected shared file.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  string  $token
 * @return \Illuminate\Http\Response
 */
public function verifyPassword(Request $request, $token)
{
    $request->validate([
        'password' => 'required|string',
    ]);

    // Find the file share by token
    $fileShare = FileShare::where('token', $token)
        ->where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->first();

    if (!$fileShare) {
        abort(404, 'This link has expired or does not exist.');
    }

    // Verify password
    if (!password_verify($request->password, $fileShare->password)) {
        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    // Get the file
    $file = File::findOrFail($fileShare->file_id);

    // Log the access
    Log::info('Password-protected file accessed', [
        'file_id' => $file->id,
        'file_name' => $file->name,
        'token' => $token,
        'ip' => request()->ip()
    ]);

    // If it's a viewable file type, show it in the browser
    if (in_array($file->mime_type, [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
        'text/plain'
    ])) {
        $fileContent = Storage::disk('minio')->get($file->path);
        return response($fileContent)->header('Content-Type', $file->mime_type);
    }

    // Otherwise, force download
    return Storage::disk('minio')->download($file->path, $file->name);
}

/**
 * Show duplicate files.
 *
 * @return \Illuminate\View\View
 */
public function showDuplicates()
{
    $duplicateFiles = session('duplicate_files', []);
    
    if (empty($duplicateFiles)) {
        return redirect()->route('file.settings')->with('info', 'No duplicate files found.');
    }
    
    return view('duplicates', compact('duplicateFiles'));
}

/**
 * Show large files.
 *
 * @return \Illuminate\View\View
 */
public function showLargeFiles()
{
    $largeFiles = session('large_files', []);
    
    if (empty($largeFiles)) {
        return redirect()->route('file.settings')->with('info', 'No large files found.');
    }
    
    return view('large', compact('largeFiles'));
}

/**
 * Display the file settings page.
 *
 * @return \Illuminate\View\View
 */
public function showSettings()
{
    $user = auth()->user();
    
    // Debug statement
    \Log::info('showSettings method called', ['user_id' => $user->id]);
    
    try {
        // Get or create user preferences
        $userPreferences = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'default_view' => 'grid',
                'sort_by' => 'name',
                'show_hidden_files' => false,
                'auto_organize' => true,
                'trash_retention' => 30,
            ]
        );
        
        // Get or create sharing preferences
        $sharingPreferences = SharingPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'default_permission' => 'view',
                'notify_on_access' => true,
                'password_protect_by_default' => false,
                'default_expiration' => 7,
            ]
        );
        
        \Log::info('Successfully retrieved sharing preferences', [
            'user_id' => $user->id,
            'sharing_preferences' => $sharingPreferences->toArray()
        ]);
    } catch (\Exception $e) {
        \Log::error('Error retrieving preferences', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Create fallback objects if there's an error
        $userPreferences = (object) [
            'default_view' => 'grid',
            'sort_by' => 'name',
            'show_hidden_files' => false,
            'auto_organize' => true,
            'trash_retention' => 30,
        ];
        
        $sharingPreferences = (object) [
            'default_permission' => 'view',
            'notify_on_access' => true,
            'password_protect_by_default' => false,
            'default_expiration' => 7,
        ];
    }
    
    // Get file statistics
    $fileStats = [
        'documents' => [
            'size' => $user->files()->where('mime_type', 'like', 'application/%')->sum('size'),
            'count' => $user->files()->where('mime_type', 'like', 'application/%')->count(),
        ],
        'images' => [
            'size' => $user->files()->where('mime_type', 'like', 'image/%')->sum('size'),
            'count' => $user->files()->where('mime_type', 'like', 'image/%')->count(),
        ],
        'videos' => [
            'size' => $user->files()->where('mime_type', 'like', 'video/%')->sum('size'),
            'count' => $user->files()->where('mime_type', 'like', 'video/%')->count(),
        ],
        'archives' => [
            'size' => $user->files()->whereIn('mime_type', ['application/zip', 'application/x-rar-compressed', 'application/x-tar'])->sum('size'),
            'count' => $user->files()->whereIn('mime_type', ['application/zip', 'application/x-rar-compressed', 'application/x-tar'])->count(),
        ],
    ];
    
    // Mock user plan for now
    $userPlan = (object) [
        'name' => 'Free Plan',
        'storage_gb' => 5,
        'features' => [
            'Basic file storage',
            'File sharing',
            'Access on all devices',
            'Secure cloud storage',
        ],
    ];
    
    // Explicitly check that variables are set before passing to view
    \Log::info('Variables being passed to view', [
        'userPreferences' => isset($userPreferences) ? 'set' : 'not set',
        'sharingPreferences' => isset($sharingPreferences) ? 'set' : 'not set',
    ]);
    
    // Make sure to pass all variables to the view
    return view('file-settings', [
        'userPreferences' => $userPreferences,
        'fileStats' => $fileStats,
        'sharingPreferences' => $sharingPreferences,
        'userPlan' => $userPlan
    ]);
}
/**
 * Update file preferences.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateSettings(Request $request)
{
    $request->validate([
        'default_view' => 'required|in:grid,list,compact',
        'sort_by' => 'required|in:name,date,size,type',
        'trash_retention' => 'required|integer|in:7,14,30,90',
    ]);
    
    $user = auth()->user();
    
    // Update or create user preferences
    UserPreference::updateOrCreate(
        ['user_id' => $user->id],
        [
            'default_view' => $request->default_view,
            'sort_by' => $request->sort_by,
            'show_hidden_files' => $request->has('show_hidden_files'),
            'auto_organize' => $request->has('auto_organize'),
            'trash_retention' => $request->trash_retention,
        ]
    );
    
    return redirect()->route('file.settings')->with('success', 'File preferences updated successfully!');
}

/**
 * Scan for duplicate files.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
public function scanDuplicates()
{
    $user = auth()->user();
    $files = $user->files()->notTrashed()->get();
    
    // Group files by size first (potential duplicates have the same size)
    $filesBySize = $files->groupBy('size')->filter(function ($group) {
        return $group->count() > 1;
    });
    
    $duplicates = [];
    
    // For each group of same-sized files, check for duplicates
    foreach ($filesBySize as $size => $sizeGroup) {
        // Group by name (simple duplicate detection)
        $filesByName = $sizeGroup->groupBy('name');
        
        foreach ($filesByName as $name => $nameGroup) {
            if ($nameGroup->count() > 1) {
                $duplicates[] = [
                    'name' => $name,
                    'size' => $size,
                    'count' => $nameGroup->count(),
                    'files' => $nameGroup->toArray(),
                ];
            }
        }
    }
    
    // Store duplicates in session
    session(['duplicate_files' => $duplicates]);
    
    if (count($duplicates) > 0) {
        return redirect()->route('files.duplicates');
    } else {
        return redirect()->route('file.settings')->with('info', 'No duplicate files found.');
    }
}

/**
 * Find large files.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
public function findLargeFiles()
{
    $user = auth()->user();
    
    // Get files larger than 10MB
    $largeFiles = $user->files()
        ->notTrashed()
        ->where('size', '>', 10 * 1024 * 1024) // 10MB
        ->orderBy('size', 'desc')
        ->get();
    
    // Store large files in session
    session(['large_files' => $largeFiles->toArray()]);
    
    if ($largeFiles->count() > 0) {
        return redirect()->route('files.large-files');
    } else {
        return redirect()->route('file.settings')->with('info', 'No large files found (>10MB).');
    }
}

/**
 * Delete selected duplicate files.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function deleteDuplicates(Request $request)
{
    $request->validate([
        'files_to_delete' => 'required|array',
        'files_to_delete.*' => 'required|integer|exists:files,id',
    ]);
    
    $user = auth()->user();
    $filesToDelete = $request->input('files_to_delete');
    $deletedCount = 0;
    
    foreach ($filesToDelete as $fileId) {
        $file = $user->files()->findOrFail($fileId);
        
        // Delete from storage
        Storage::disk('minio')->delete($file->path);
        
        // Delete from database
        $file->delete();
        
        $deletedCount++;
    }
    
    // Update user's storage usage
    $user->updateStorageUsage();
    
    return redirect()->route('file.settings')->with('success', "{$deletedCount} duplicate files deleted successfully.");
}

/**
 * Delete selected files.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function deleteSelected(Request $request)
{
    $request->validate([
        'files_to_delete' => 'required|array',
        'files_to_delete.*' => 'required|integer|exists:files,id',
    ]);
    
    $user = auth()->user();
    $filesToDelete = $request->input('files_to_delete');
    $deletedCount = 0;
    
    foreach ($filesToDelete as $fileId) {
        $file = $user->files()->findOrFail($fileId);
        
        // Delete from storage
        Storage::disk('minio')->delete($file->path);
        
        // Delete from database
        $file->delete();
        
        $deletedCount++;
    }
    
    // Update user's storage usage
    $user->updateStorageUsage();
    
    return redirect()->route('file.settings')->with('success', "{$deletedCount} files deleted successfully.");
}

/**
 * Update file sharing preferences.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateSharingSettings(Request $request)
{
    $request->validate([
        'default_permission' => 'required|in:view,edit',
        'default_expiration' => 'required|in:1,7,30,never',
    ]);
    
    $user = auth()->user();
    
    // Update or create sharing preferences
    SharingPreference::updateOrCreate(
        ['user_id' => $user->id],
        [
            'default_permission' => $request->default_permission,
            'notify_on_access' => $request->has('notify_on_access'),
            'password_protect_by_default' => $request->has('password_protect_by_default'),
            'default_expiration' => $request->default_expiration === 'never' ? null : $request->default_expiration,
        ]
    );
    
    return redirect()->route('file.settings')->with('success', 'Sharing preferences updated successfully!');
}

}