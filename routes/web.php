<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Welcome page
Route::get('/', function () {
    return view('home');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('home');
    })->name('dashboard');
    
    // Help & Support
    Route::get('/help', function () {
        return view('help');
    })->name('help');
    
    // Plans & Upgrade
    Route::get('/plans', function () {
        return view('plans');
    })->name('plans');

    // User Settings Routes
    Route::get('/settings/user', [UserSettingsController::class, 'index'])->name('user.settings');
    Route::put('/settings/user/profile', [UserSettingsController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('/settings/user/password', [UserSettingsController::class, 'updatePassword'])->name('user.password.update');
    Route::put('/settings/user/notifications', [UserSettingsController::class, 'updateNotifications'])->name('user.notifications.update');
    Route::post('/settings/user/data/download', [UserSettingsController::class, 'downloadData'])->name('user.data.download');
    Route::post('/settings/user/deactivate', [UserSettingsController::class, 'deactivateAccount'])->name('user.deactivate');
    Route::delete('/settings/user/delete', [UserSettingsController::class, 'deleteAccount'])->name('user.delete');
    Route::get('/settings/user/activity', [UserSettingsController::class, 'showActivity'])->name('user.activity');
    Route::get('/settings/user/2fa/setup', [UserSettingsController::class, 'showTwoFactorSetup'])->name('user.2fa.setup');

    // File routes
    Route::get('/files/{file}/thumbnail', [App\Http\Controllers\FileController::class, 'thumbnail'])->name('files.thumbnail');
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::get('/files/create', [FileController::class, 'create'])->name('files.create');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::get('/files/{id}', [FileController::class, 'show'])->name('files.show');
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');
    Route::delete('/files/{id}', [FileController::class, 'delete'])->name('files.delete');
    Route::delete('/files/{id}/permanent', [FileController::class, 'destroy'])->name('files.destroy');
    Route::patch('/files/{id}/restore', [FileController::class, 'restore'])->name('files.restore');
    Route::patch('/files/{id}/star', [FileController::class, 'toggleStar'])->name('files.toggle-star');
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    
    // File sharing routes
    Route::get('/share', [FileController::class, 'showSharePage'])->name('share');
    Route::post('/files/{file}/share', [FileController::class, 'share'])->name('files.share');
    Route::delete('/file-shares/{id}', [FileController::class, 'unshare'])->name('files.unshare');
    Route::get('/starred', [FileController::class, 'starred'])->name('files.starred');
    
    // Folder routes
    Route::get('/folders', [FolderController::class, 'index'])->name('folders');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/folders/{id}', [FolderController::class, 'show'])->name('folders.show');
    Route::patch('/folders/{id}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/folders/{id}', [FolderController::class, 'delete'])->name('folders.delete');
    Route::delete('/folders/{id}/permanent', [FolderController::class, 'destroy'])->name('folders.destroy');
    Route::patch('/folders/{id}/restore', [FolderController::class, 'restore'])->name('folders.restore');
    Route::patch('/folders/{id}/star', [FolderController::class, 'toggleStar'])->name('folders.toggle-star');
    
    Route::post('/trash/empty', [FileController::class, 'emptyTrash'])->name('trash.empty');

    // File star toggle
    Route::post('/files/{id}/toggle-star', [FileController::class, 'toggleStar'])->name('files.toggle-star');
    // Folder star toggle
    Route::post('/folders/{id}/toggle-star', [FolderController::class, 'toggleStar'])->name('folders.toggle-star');

    // File sharing routes
    Route::post('/files/{id}/generate-link', [FileController::class, 'generateLink'])->name('files.generate-link');
    
    // File Settings
    Route::get('/settings/files', [FileController::class, 'showSettings'])->name('file.settings');
    Route::post('/settings/files', [FileController::class, 'updateSettings'])->name('file.settings.update');
    Route::post('/settings/files/sharing', [FileController::class, 'updateSharingSettings'])->name('file.sharing.update');
    Route::post('/files/scan-duplicates', [FileController::class, 'scanDuplicates'])->name('files.scan-duplicates');
    Route::post('/files/find-large', [FileController::class, 'findLargeFiles'])->name('files.find-large');
    Route::get('/files/duplicates', [FileController::class, 'showDuplicates'])->name('files.duplicates');
    Route::get('/files/large', [FileController::class, 'showLargeFiles'])->name('files.large');

    // Starred files and folders
    Route::get('/starred', function () {
        return view('starred');
    })->name('starred');
    
    // Trash
    Route::get('/trash', function () {
        return view('trash');
    })->name('trash');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Public file access routes (no auth required)
Route::get('/s/{token}', [FileController::class, 'publicAccess'])->name('files.public');
Route::post('/s/{token}/verify', [FileController::class, 'verifyPassword'])->name('files.verify-password');