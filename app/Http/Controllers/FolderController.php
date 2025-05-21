<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Display a listing of the folders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get root folders (those without a parent) that aren't in trash
        $folders = auth()->user()->folders()
            ->whereNull('parent_id')
            ->where('is_trashed', false)
            ->get();
        
        // Get files that aren't in any folder and aren't in trash
        $files = auth()->user()->files()
            ->whereNull('folder_id')
            ->where('is_trashed', false)
            ->get();
        
        return view('folders', compact('folders', 'files'));
    }

    /**
     * Display the specified folder.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $folder = auth()->user()->folders()->findOrFail($id);
        
        // Get subfolders that aren't in trash
        $folders = $folder->children()->where('is_trashed', false)->get();
        
        // Get files in this folder that aren't in trash
        $files = $folder->files()->where('is_trashed', false)->get();
        
        // Get the breadcrumb path
        $breadcrumbs = $this->getBreadcrumbs($folder);
        
        return view('folders', compact('folder', 'folders', 'files', 'breadcrumbs'));
    }

    /**
     * Get the breadcrumb path for a folder.
     *
     * @param  \App\Models\Folder  $folder
     * @return array
     */
    private function getBreadcrumbs(Folder $folder)
    {
        $breadcrumbs = [];
        $current = $folder;
        
        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name
            ]);
            $current = $current->parent;
        }
        
        return $breadcrumbs;
    }

    /**
     * Create a new folder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Folder created successfully.');
    }

    /**
     * Update a folder.
     */
    public function update(Request $request, Folder $folder)
    {
        // Check if user has permission
        if ($folder->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to update this folder.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Folder updated successfully.');
    }

    /**
     * Delete a folder (move to trash).
     */
    public function delete(Folder $folder)
    {
        // Check if user has permission
        if ($folder->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to delete this folder.');
        }

        $folder->update(['is_trashed' => true]);
        
        // Also trash all files in this folder
        $folder->files()->update(['is_trashed' => true]);
        
        // And all subfolders
        $this->trashSubfolders($folder);

        return redirect()->back()->with('success', 'Folder moved to trash.');
    }

    /**
     * Recursively trash subfolders and their contents.
     */
    private function trashSubfolders(Folder $folder)
    {
        foreach ($folder->children as $child) {
            $child->update(['is_trashed' => true]);
            $child->files()->update(['is_trashed' => true]);
            $this->trashSubfolders($child);
        }
    }

    /**
     * Restore a folder from trash.
     */
    public function restore(Folder $folder)
    {
        // Check if user has permission
        if ($folder->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to restore this folder.');
        }

        $folder->update(['is_trashed' => false]);
        
        // Also restore all files in this folder
        $folder->files()->update(['is_trashed' => false]);

        return redirect()->back()->with('success', 'Folder restored from trash.');
    }

    /**
     * Toggle star status for a folder.
     */
    public function toggleStar($id)
    {
        $folder = auth()->user()->folders()->findOrFail($id);
        
        // Check if user has permission
        if ($folder->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to star/unstar this folder.');
        }

        $folder->update(['is_starred' => !$folder->is_starred]);
        
        $message = $folder->is_starred 
            ? "Folder '{$folder->name}' added to starred." 
            : "Folder '{$folder->name}' removed from starred.";

        return redirect()->back()->with('success', $message);
    }

    /**
     * Permanently delete a folder.
     */
    public function destroy(Folder $folder)
    {
        // Check if user has permission
        if ($folder->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to delete this folder.');
        }

        // Delete all files in this folder
        foreach ($folder->files as $file) {
            // Delete the actual file from storage
            Storage::disk('minio')->delete($file->path);
            // Delete the database record
            $file->delete();
        }
        
        // Recursively delete subfolders
        foreach ($folder->children as $child) {
            $this->destroy($child);
        }
        
        // Finally delete the folder itself
        $folder->delete();

        return redirect()->back()->with('success', 'Folder permanently deleted.');
    }
}