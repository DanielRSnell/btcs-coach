<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ActionItemController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect dashboard to sessions
    Route::get('dashboard', function () {
        return redirect()->route('sessions');
    })->name('dashboard');
    
    // Sessions route - main landing page after login
    Route::get('sessions', [SessionsController::class, 'index'])->name('sessions');
    
    // Debug route for Railway deployment
    Route::get('debug/assets', function() {
        $manifestPath = public_path('build/manifest.json');
        $manifestExists = file_exists($manifestPath);
        $dashboardExists = false;
        $manifestContent = null;
        
        if ($manifestExists) {
            $manifestContent = json_decode(file_get_contents($manifestPath), true);
            $dashboardExists = isset($manifestContent['resources/js/pages/Dashboard.tsx']);
        }
        
        return response()->json([
            'manifest_path' => $manifestPath,
            'manifest_exists' => $manifestExists,
            'dashboard_in_manifest' => $dashboardExists,
            'public_path' => public_path(),
            'build_dir_exists' => is_dir(public_path('build')),
            'assets_dir_exists' => is_dir(public_path('build/assets')),
            'dashboard_file_exists' => file_exists(public_path('build/assets/Dashboard-4_Sq07_K.js')),
            'sample_files' => $manifestExists && $manifestContent ? array_slice(array_keys($manifestContent), 0, 10) : [],
        ]);
    });
    Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('modules/{module:slug}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('modules/{module:slug}/chat', [ModuleController::class, 'chat'])->name('modules.chat');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
    
    // Action Item routes
    Route::put('action-items/{actionItem}/complete', [ActionItemController::class, 'markAsCompleted'])->name('action-items.complete');
    Route::put('action-items/{actionItem}/status', [ActionItemController::class, 'updateStatus'])->name('action-items.status');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
