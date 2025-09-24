<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production but not for Livewire upload URLs
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');

            // Prevent Livewire from using signed URLs for file uploads
            $this->preventLivewireSignedUploads();
        }

        // Fix Filament file upload session configuration for production
        $this->configureFilamentUploads();
    }

    /**
     * Configure Filament file upload settings for consistent behavior
     * between local and production environments
     */
    private function configureFilamentUploads(): void
    {
        // Ensure consistent session configuration for file uploads
        if (app()->runningInConsole()) {
            return;
        }

        // Force consistent CSRF token configuration in production
        if (config('app.env') === 'production') {
            // Ensure session driver is properly configured
            config(['session.same_site' => 'lax']);

            // Set secure cookies for HTTPS in production
            config(['session.secure' => true]);
            config(['session.http_only' => true]);

            // Extend session lifetime for file uploads
            config(['session.lifetime' => 240]); // 4 hours instead of 2

            // Ensure consistent session cookie configuration
            config(['session.cookie' => 'btcs_coach_session']);
            config(['session.path' => '/']);

            // Force session encryption to be consistent
            config(['session.encrypt' => false]);

            // Set consistent temporary file upload configuration
            config(['livewire.temporary_file_upload.max_upload_time' => 10]);
        }
    }

    /**
     * Prevent Livewire from using signed URLs for file uploads in production
     */
    private function preventLivewireSignedUploads(): void
    {
        // The core issue: Remove the signed URL middleware from Livewire uploads
        // This forces Livewire to use the same upload URL pattern as local
        config([
            'livewire.temporary_file_upload.middleware' => 'web', // Use web middleware instead of signed
        ]);
    }
}
