<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Storage;

class S3ConfigWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Primary status indicators
            Stat::make('Environment', config('app.env'))
                ->description('App environment')
                ->descriptionIcon('heroicon-m-server')
                ->color('primary'),

            Stat::make('S3 Connection', $this->testS3Connection())
                ->description('Storage status')
                ->descriptionIcon('heroicon-m-signal')
                ->color($this->testS3Connection() === 'Connected' ? 'success' : 'danger'),

            Stat::make('Session Driver', config('session.driver'))
                ->description('Session storage')
                ->descriptionIcon('heroicon-m-identification')
                ->color(config('session.driver') === 'database' ? 'success' : 'warning'),

            // S3 Configuration details
            Stat::make('Default Disk', config('filesystems.default'))
                ->description('Filesystem disk')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color(config('filesystems.default') === 's3' ? 'success' : 'warning'),

            Stat::make('S3 Region', config('filesystems.disks.s3.region'))
                ->description('Storage region')
                ->descriptionIcon('heroicon-m-globe-americas')
                ->color('info'),

            Stat::make('S3 Bucket', config('filesystems.disks.s3.bucket'))
                ->description('Bucket name')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('info'),

            // Security status
            Stat::make('Access Key', $this->shortenSecret(config('filesystems.disks.s3.key')))
                ->description('S3 access key')
                ->descriptionIcon('heroicon-m-key')
                ->color($this->hasValue(config('filesystems.disks.s3.key')) ? 'success' : 'danger'),

            Stat::make('Secret Key', $this->shortenSecret(config('filesystems.disks.s3.secret')))
                ->description('S3 secret key')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color($this->hasValue(config('filesystems.disks.s3.secret')) ? 'success' : 'danger'),

            Stat::make('Session Security', config('session.secure') ? 'Enabled' : 'Disabled')
                ->description('HTTPS cookies')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color(config('session.secure') ? 'success' : 'warning'),
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