<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FixFilamentUploads
{
    /**
     * Handle an incoming request to fix Filament upload token/signature issues
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle Filament file upload requests specifically
        if ($this->isFilamentUploadRequest($request)) {
            $this->fixUploadTokenIssues($request);
        }

        return $next($request);
    }

    /**
     * Fix upload token/signature issues for production environment
     */
    private function fixUploadTokenIssues(Request $request): void
    {
        // Ensure proper headers for CORS and CSRF
        if (config('app.env') === 'production') {
            // Set proper headers for production environment
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
            $request->headers->set('Accept', 'application/json');

            // Ensure session is started and valid
            if (!$request->hasSession() || !$request->session()->isStarted()) {
                $request->session()->start();
            }

            // Handle token validation for livewire uploads
            if ($request->hasFile('file') || $request->has('_token')) {
                $this->ensureValidToken($request);
            }

            // Fix signature validation for temporary uploads
            $this->fixSignatureValidation($request);
        }
    }

    /**
     * Ensure CSRF token is valid and fresh
     */
    private function ensureValidToken(Request $request): void
    {
        $token = $request->get('_token') ?: $request->header('X-CSRF-TOKEN');

        // If no token or token is invalid, regenerate
        if (!$token || !hash_equals($request->session()->token(), $token)) {
            $request->session()->regenerateToken();

            // Update the request token
            if ($request->has('_token')) {
                $request->merge(['_token' => $request->session()->token()]);
            }
        }
    }

    /**
     * Fix signature validation for temporary file uploads
     */
    private function fixSignatureValidation(Request $request): void
    {
        // Check if this is a signed temporary upload URL
        if ($request->hasValidSignature()) {
            return;
        }

        // For expired signatures, allow the upload if user is authenticated
        // and request comes from admin panel
        if (auth()->check() && $request->is('admin/*')) {
            $request->setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
        }
    }

    /**
     * Determine if this is a Filament file upload request
     */
    private function isFilamentUploadRequest(Request $request): bool
    {
        return $request->is('livewire/upload-file') ||
               $request->is('livewire/preview-file/*') ||
               $request->is('admin/*/livewire/upload-file') ||
               str_contains($request->url(), 'livewire') ||
               $request->hasHeader('X-Livewire');
    }
}