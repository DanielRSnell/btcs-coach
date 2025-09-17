<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle Livewire upload-specific errors with better messages
        if ($request->is('livewire/upload-file*')) {
            return $this->handleLivewireUploadError($request, $e);
        }

        // Handle API errors for session management
        if ($request->is('api/*')) {
            return $this->handleApiError($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle Livewire upload-specific errors with detailed messages
     */
    protected function handleLivewireUploadError(Request $request, Throwable $e)
    {
        $statusCode = $this->getStatusCode($e);
        $message = $this->getLivewireUploadErrorMessage($e, $statusCode);

        // Log the actual error for debugging
        if (config('app.debug') || config('app.env') !== 'production') {
            \Log::error('Livewire Upload Error', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'headers' => $request->headers->all(),
                'has_csrf' => $request->hasHeader('X-CSRF-TOKEN'),
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
            ]);
        }

        return response()->json([
            'message' => $message,
            'error_code' => $this->getErrorCode($e),
            'status' => $statusCode,
            'debug_info' => $this->getDebugInfo($request, $e),
        ], $statusCode);
    }

    /**
     * Handle API errors with consistent JSON responses
     */
    protected function handleApiError(Request $request, Throwable $e)
    {
        $statusCode = $this->getStatusCode($e);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage() ?: 'An error occurred',
            'error_code' => $this->getErrorCode($e),
            'status' => $statusCode,
        ], $statusCode);
    }

    /**
     * Get appropriate error message for Livewire upload errors
     */
    protected function getLivewireUploadErrorMessage(Throwable $e, int $statusCode): string
    {
        switch ($statusCode) {
            case 401:
                if (str_contains($e->getMessage(), 'signature') || str_contains($e->getMessage(), 'expired')) {
                    return 'File upload session has expired. Please refresh the page and try again.';
                }
                if (str_contains($e->getMessage(), 'Unauthenticated') || str_contains($e->getMessage(), 'CSRF')) {
                    return 'Authentication failed. Please refresh the page and log in again.';
                }
                return 'Upload authorization failed. This may be due to an expired session or invalid authentication. Please refresh the page and try again.';

            case 403:
                return 'You do not have permission to upload files. Please contact an administrator.';

            case 413:
                return 'File is too large. Maximum upload size is ' . ini_get('upload_max_filesize') . '.';

            case 422:
                if ($e instanceof ValidationException) {
                    $errors = $e->errors();
                    $firstError = reset($errors);
                    return is_array($firstError) ? reset($firstError) : $firstError;
                }
                return 'File validation failed. Please check the file type and size.';

            case 500:
                if (str_contains($e->getMessage(), 'storage') || str_contains($e->getMessage(), 'disk')) {
                    return 'File storage error. Please try again or contact support if the problem persists.';
                }
                if (str_contains($e->getMessage(), 'S3') || str_contains($e->getMessage(), 'AWS')) {
                    return 'Cloud storage temporarily unavailable. Please try again in a few moments.';
                }
                return 'Server error during file upload. Please try again or contact support.';

            default:
                return $e->getMessage() ?: 'An unexpected error occurred during file upload.';
        }
    }

    /**
     * Get HTTP status code from exception
     */
    protected function getStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpException) {
            return $e->getStatusCode();
        }

        if ($e instanceof ValidationException) {
            return 422;
        }

        return 500;
    }

    /**
     * Get error code for tracking
     */
    protected function getErrorCode(Throwable $e): string
    {
        return class_basename($e) . '_' . $this->getStatusCode($e);
    }

    /**
     * Get debug information (only in debug mode)
     */
    protected function getDebugInfo(Request $request, Throwable $e): ?array
    {
        if (!config('app.debug')) {
            return null;
        }

        return [
            'authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'has_csrf_token' => $request->hasHeader('X-CSRF-TOKEN'),
            'request_signature' => $request->get('signature'),
            'request_expires' => $request->get('expires'),
            'current_time' => time(),
            'exception_class' => get_class($e),
            'exception_file' => $e->getFile(),
            'exception_line' => $e->getLine(),
        ];
    }
}