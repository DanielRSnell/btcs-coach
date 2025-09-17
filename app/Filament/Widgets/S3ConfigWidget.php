<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;

class S3ConfigWidget extends Widget
{
    protected static string $view = 'filament.widgets.s3-config-widget';

    protected function getViewData(): array
    {
        return [
            'environment' => config('app.env'),
            'defaultDisk' => config('filesystems.default'),
            's3Region' => config('filesystems.disks.s3.region'),
            's3Bucket' => config('filesystems.disks.s3.bucket'),
            's3Endpoint' => config('filesystems.disks.s3.endpoint'),
            's3AccessKey' => $this->maskSecret(config('filesystems.disks.s3.key')),
            's3SecretKey' => $this->maskSecret(config('filesystems.disks.s3.secret')),
            's3ConnectionTest' => $this->testS3Connection(),
            'sessionDriver' => config('session.driver'),
            'cacheStore' => config('cache.default'),
            'sessionSecure' => config('session.secure'),
            'appKey' => $this->maskSecret(config('app.key')),
            'hasS3AccessKey' => $this->hasValue(config('filesystems.disks.s3.key')),
            'hasS3SecretKey' => $this->hasValue(config('filesystems.disks.s3.secret')),
            'hasAppKey' => $this->hasValue(config('app.key')),
            's3Connected' => $this->testS3Connection() === 'Connected',
        ];
    }

    protected function maskSecret(?string $value): string
    {
        if (empty($value)) {
            return '❌ Not Set';
        }

        if (strlen($value) <= 8) {
            return '✅ Set (' . strlen($value) . ' chars)';
        }

        return '✅ ' . substr($value, 0, 4) . '***' . substr($value, -4) . ' (' . strlen($value) . ' chars)';
    }

    protected function maskEndpoint(?string $value): string
    {
        if (empty($value)) {
            return '❌ Not Set';
        }

        return $value;
    }

    protected function hasValue(?string $value): bool
    {
        return !empty($value);
    }

    protected function testS3Connection(): string
    {
        try {
            $disk = Storage::disk('s3');

            // Quick test - try to get disk configuration
            $disk->exists('test-connection-probe');

            return 'Connected';
        } catch (\Exception $e) {
            return 'Failed: ' . substr($e->getMessage(), 0, 50) . '...';
        }
    }

    public function getDisplayName(): string
    {
        return 'S3 & Environment Configuration';
    }

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        // Only show to authenticated admin users
        return auth()->check() && auth()->user()?->role === 'admin';
    }
}