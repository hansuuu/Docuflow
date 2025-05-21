<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Models\SharingPreference;
use App\Models\StoragePlan;
use App\Models\File;
use Illuminate\Support\Facades\DB;

class FileSettingsController extends Controller
{
    /**
     * Display the file settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user preferences or create default ones if they don't exist
        $userPreferences = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'default_view' => 'grid',
                'sort_by' => 'date',
                'show_hidden_files' => false,
                'auto_organize' => true,
                'trash_retention' => 30
            ]
        );
        
        // Get sharing preferences or create default ones if they don't exist
        $sharingPreferences = SharingPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'default_permission' => 'view',
                'notify_on_access' => true,
                'password_protect_by_default' => false,
                'default_expiration' => 7
            ]
        );
        
        // Get user's storage plan
        $userPlan = StoragePlan::find($user->storage_plan_id) ?? $this->getDefaultPlan();
        
        // Get file statistics by type
        $fileStats = $this->getFileStatsByType($user->id);
        
        return view('file-settings', compact(
            'userPreferences', 
            'sharingPreferences', 
            'userPlan',
            'fileStats'
        ));
    }
    
    /**
     * Update the user's file preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFilePreferences(Request $request)
    {
        $request->validate([
            'default_view' => 'required|in:grid,list,compact',
            'sort_by' => 'required|in:name,date,size,type',
            'trash_retention' => 'required|in:7,14,30,90',
        ]);
        
        $userPreferences = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'default_view' => $request->default_view,
                'sort_by' => $request->sort_by,
                'show_hidden_files' => $request->has('show_hidden_files'),
                'auto_organize' => $request->has('auto_organize'),
                'trash_retention' => $request->trash_retention
            ]
        );
        
        return redirect()->route('file.settings')->with('success', 'File preferences updated successfully!');
    }
    
    /**
     * Update the user's sharing preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSharingPreferences(Request $request)
    {
        $request->validate([
            'default_permission' => 'required|in:view,edit,download',
            'default_expiration' => 'required|in:never,1,7,30',
        ]);
        
        $sharingPreferences = SharingPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'default_permission' => $request->default_permission,
                'notify_on_access' => $request->has('notify_on_access'),
                'password_protect_by_default' => $request->has('password_protect_by_default'),
                'default_expiration' => $request->default_expiration
            ]
        );
        
        return redirect()->route('file.settings')->with('success', 'Sharing preferences updated successfully!');
    }
    
    /**
     * Scan for duplicate files.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function scanDuplicates()
    {
        $user = auth()->user();
        
        // Find duplicate files based on file hash or size+name
        $duplicates = File::where('user_id', $user->id)
            ->where('is_trashed', false)
            ->select('file_hash', DB::raw('COUNT(*) as count'))
            ->groupBy('file_hash')
            ->having('count', '>', 1)
            ->get();
        
        $duplicateCount = $duplicates->sum('count') - $duplicates->count();
        
        if ($duplicateCount > 0) {
            // Store duplicate file IDs in session for the duplicates view
            $duplicateFiles = File::where('user_id', $user->id)
                ->whereIn('file_hash', $duplicates->pluck('file_hash'))
                ->get()
                ->groupBy('file_hash');
            
            session(['duplicate_files' => $duplicateFiles]);
            
            return redirect()->route('files.duplicates')->with('success', "Found {$duplicateCount} duplicate files!");
        }
        
        return redirect()->route('file.settings')->with('info', 'No duplicate files found.');
    }
    
    /**
     * Find large files.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function findLargeFiles()
    {
        $user = auth()->user();
        
        // Find files larger than 100MB
        $largeFiles = File::where('user_id', $user->id)
            ->where('is_trashed', false)
            ->where('size', '>', 100 * 1024 * 1024) // 100MB
            ->orderBy('size', 'desc')
            ->get();
        
        if ($largeFiles->count() > 0) {
            // Store large file IDs in session for the large files view
            session(['large_files' => $largeFiles]);
            
            return redirect()->route('files.large')->with('success', "Found {$largeFiles->count()} large files!");
        }
        
        return redirect()->route('file.settings')->with('info', 'No large files found (over 100MB).');
    }
    
    /**
     * Get the default storage plan.
     *
     * @return object
     */
    private function getDefaultPlan()
    {
        return (object) [
            'name' => 'Standard Plan',
            'storage_gb' => 256,
            'features' => [
                '256 GB cloud storage',
                'File sharing with permissions',
                '30-day file recovery'
            ]
        ];
    }
    
    /**
     * Get file statistics by type.
     *
     * @param  int  $userId
     * @return array
     */
    private function getFileStatsByType($userId)
    {
        $stats = [
            'documents' => ['size' => 0, 'count' => 0],
            'images' => ['size' => 0, 'count' => 0],
            'videos' => ['size' => 0, 'count' => 0],
            'archives' => ['size' => 0, 'count' => 0]
        ];
        
        // Get document files
        $documentFiles = File::where('user_id', $userId)
            ->where('is_trashed', false)
            ->where(function($query) {
                $query->where('mime_type', 'like', 'application/pdf%')
                    ->orWhere('mime_type', 'like', 'application/msword%')
                    ->orWhere('mime_type', 'like', 'application/vnd.openxmlformats-officedocument%')
                    ->orWhere('mime_type', 'like', 'text/%');
            })
            ->get();
        
        $stats['documents']['size'] = $documentFiles->sum('size');
        $stats['documents']['count'] = $documentFiles->count();
        
        // Get image files
        $imageFiles = File::where('user_id', $userId)
            ->where('is_trashed', false)
            ->where('mime_type', 'like', 'image/%')
            ->get();
        
        $stats['images']['size'] = $imageFiles->sum('size');
        $stats['images']['count'] = $imageFiles->count();
        
        // Get video files
        $videoFiles = File::where('user_id', $userId)
            ->where('is_trashed', false)
            ->where('mime_type', 'like', 'video/%')
            ->get();
        
        $stats['videos']['size'] = $videoFiles->sum('size');
        $stats['videos']['count'] = $videoFiles->count();
        
        // Get archive files
        $archiveFiles = File::where('user_id', $userId)
            ->where('is_trashed', false)
            ->where(function($query) {
                $query->where('mime_type', 'like', 'application/zip%')
                    ->orWhere('mime_type', 'like', 'application/x-rar%')
                    ->orWhere('mime_type', 'like', 'application/x-tar%')
                    ->orWhere('mime_type', 'like', 'application/x-gzip%');
            })
            ->get();
        
        $stats['archives']['size'] = $archiveFiles->sum('size');
        $stats['archives']['count'] = $archiveFiles->count();
        
        return $stats;
    }
}