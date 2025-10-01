<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Debug route to check Railway proxy headers (remove after testing)
Route::get('/debug-headers', function () {
    if (config('app.env') !== 'production') {
        abort(404);
    }

    return response()->json([
        'headers' => request()->headers->all(),
        'server' => request()->server->all(),
        'url' => request()->url(),
        'scheme' => request()->getScheme(),
        'is_secure' => request()->isSecure(),
        'forwarded_proto' => request()->header('X-Forwarded-Proto'),
    ]);
});

// Override Livewire upload route in production to bypass signature validation
if (config('app.env') === 'production') {
    Route::post('/livewire/upload-file', [App\Http\Controllers\CustomFileUploadController::class, 'handle'])
        ->middleware(['web', 'auth'])
        ->name('livewire.upload-file');
}

// Session API routes with JSON error responses (outside auth middleware to use custom middleware)
Route::prefix('api/sessions')->middleware('api.auth')->group(function () {
    Route::post('register', [SessionsController::class, 'registerSession'])
        ->name('api.sessions.register');
    Route::post('update', [SessionsController::class, 'updateSession'])
        ->name('api.sessions.update');
    Route::post('check', [SessionsController::class, 'checkSession'])
        ->name('api.sessions.check');
    Route::post('feedback', [SessionsController::class, 'submitFeedback'])
        ->name('api.sessions.feedback');
});

// Team API routes
Route::prefix('api/team')->middleware('api.auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\TeamController::class, 'index'])
        ->name('api.team.index');
    Route::post('by-org-level', [\App\Http\Controllers\Api\TeamController::class, 'byOrgLevel'])
        ->name('api.team.by-org-level');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirect dashboard to sessions
    Route::get('dashboard', function () {
        return redirect()->route('sessions');
    })->name('dashboard');
    
    // Sessions routes - main landing page after login
    Route::get('sessions', [SessionsController::class, 'index'])->name('sessions');
    Route::get('sessions/new', function () {
        return Inertia::render('NewSession');
    })->name('sessions.new');
    Route::get('sessions/audio', function () {
        $user = auth()->user();
        return Inertia::render('sessions/audio', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'pi_behavioral_pattern_id' => $user->pi_behavioral_pattern_id,
                'pi_behavioral_pattern' => $user->pi_behavioral_pattern,
                'pi_raw_scores' => $user->pi_raw_scores,
                'pi_assessed_at' => $user->pi_assessed_at,
                'pi_notes' => $user->pi_notes,
                'pi_profile' => $user->pi_profile,
                'has_pi_assessment' => $user->hasPiAssessment(),
                'has_pi_profile' => $user->hasPiProfile(),
            ],
        ]);
    })->name('sessions.audio');
    Route::get('sessions/{sessionId}', [SessionsController::class, 'show'])->name('sessions.show');
    
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
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
