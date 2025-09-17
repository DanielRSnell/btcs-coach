<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LivewireUploadDebug
{
    /**
     * Handle an incoming request for Livewire upload debugging.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log for Livewire upload routes
        if ($request->is('livewire/upload-file*')) {
            $this->logUploadAttempt($request);
        }

        $response = $next($request);

        // Log response details for failed uploads
        if ($request->is('livewire/upload-file*') && $response->getStatusCode() >= 400) {
            $this->logUploadFailure($request, $response);
        }

        return $response;
    }

    /**
     * Log upload attempt details
     */
    protected function logUploadAttempt(Request $request): void
    {
        Log::info('Livewire Upload Attempt', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'csrf_token_present' => $request->hasHeader('X-CSRF-TOKEN'),
            'csrf_token' => $request->header('X-CSRF-TOKEN'),
            'signature' => $request->get('signature'),
            'expires' => $request->get('expires'),
            'expires_readable' => $request->get('expires') ? date('Y-m-d H:i:s', $request->get('expires')) : null,
            'current_time' => time(),
            'current_time_readable' => date('Y-m-d H:i:s'),
            'signature_expired' => $request->get('expires') ? (time() > $request->get('expires')) : null,
            'content_length' => $request->header('Content-Length'),
            'content_type' => $request->header('Content-Type'),
            'cookies' => $request->cookies->all(),
            'session_data' => [
                'driver' => config('session.driver'),
                'lifetime' => config('session.lifetime'),
                'secure' => config('session.secure'),
                'same_site' => config('session.same_site'),
                'domain' => config('session.domain'),
            ],
        ]);
    }

    /**
     * Log upload failure details
     */
    protected function logUploadFailure(Request $request, Response $response): void
    {
        $responseContent = $response->getContent();
        $decodedContent = json_decode($responseContent, true);

        Log::error('Livewire Upload Failed', [
            'status_code' => $response->getStatusCode(),
            'response_content' => $responseContent,
            'response_decoded' => $decodedContent,
            'response_headers' => $response->headers->all(),
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'session_issues' => $this->checkSessionIssues($request),
            'signature_issues' => $this->checkSignatureIssues($request),
        ]);
    }

    /**
     * Check for common session-related issues
     */
    protected function checkSessionIssues(Request $request): array
    {
        return [
            'session_id_present' => !empty(session()->getId()),
            'session_driver' => config('session.driver'),
            'session_started' => session()->isStarted(),
            'csrf_token_match' => $request->hasHeader('X-CSRF-TOKEN') &&
                                  $request->header('X-CSRF-TOKEN') === session()->token(),
            'authenticated' => auth()->check(),
            'session_regenerated_recently' => session()->has('_token'),
        ];
    }

    /**
     * Check for signature-related issues
     */
    protected function checkSignatureIssues(Request $request): array
    {
        $signature = $request->get('signature');
        $expires = $request->get('expires');
        $currentTime = time();

        return [
            'has_signature' => !empty($signature),
            'has_expires' => !empty($expires),
            'is_expired' => $expires ? ($currentTime > $expires) : null,
            'expires_in_seconds' => $expires ? ($expires - $currentTime) : null,
            'signature_length' => $signature ? strlen($signature) : 0,
            'app_key_set' => !empty(config('app.key')),
            'app_env' => config('app.env'),
        ];
    }
}
