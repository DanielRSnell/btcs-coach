<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::get('modules/{module:slug}', [ModuleController::class, 'show'])->name('modules.show');
    Route::get('modules/{module:slug}/chat', [ModuleController::class, 'chat'])->name('modules.chat');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
