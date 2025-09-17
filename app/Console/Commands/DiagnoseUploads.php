<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Exception;

class DiagnoseUploads extends Command
{
    protected $signature = 'uploads:diagnose';
    protected $description = 'Diagnose file upload configuration and connectivity issues';

    public function handle()
    {
        $this->info('=== File Upload Diagnostic ===');
        $this->newLine();

        // 1. PHP Upload Configuration
        $this->info('PHP Upload Settings:');
        $this->line("- upload_max_filesize: " . ini_get('upload_max_filesize'));
        $this->line("- post_max_size: " . ini_get('post_max_size'));
        $this->line("- max_execution_time: " . ini_get('max_execution_time'));
        $this->line("- max_input_time: " . ini_get('max_input_time'));
        $this->line("- memory_limit: " . ini_get('memory_limit'));
        $this->line("- file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled'));
        $this->line("- upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Default'));
        $this->newLine();

        // 2. Laravel Storage Configuration
        $this->info('Laravel Storage Configuration:');
        try {
            $storageConfig = config('filesystems');
            $this->line("- Default disk: " . config('filesystems.default'));
            $this->line("- S3 configuration exists: " . (isset($storageConfig['disks']['s3']) ? 'Yes' : 'No'));

            if (isset($storageConfig['disks']['s3'])) {
                $s3Config = $storageConfig['disks']['s3'];
                $this->line("- S3 Key: " . (strlen($s3Config['key'] ?? '') > 0 ? 'Set (' . strlen($s3Config['key']) . ' chars)' : 'Not set'));
                $this->line("- S3 Secret: " . (strlen($s3Config['secret'] ?? '') > 0 ? 'Set (' . strlen($s3Config['secret']) . ' chars)' : 'Not set'));
                $this->line("- S3 Region: " . ($s3Config['region'] ?? 'Not set'));
                $this->line("- S3 Bucket: " . ($s3Config['bucket'] ?? 'Not set'));
                $this->line("- S3 Endpoint: " . ($s3Config['endpoint'] ?? 'Not set'));
            }
        } catch (Exception $e) {
            $this->error("Error getting Laravel config: " . $e->getMessage());
        }
        $this->newLine();

        // 3. Environment Variables
        $this->info('Environment Variables:');
        $envVars = ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_BUCKET', 'AWS_ENDPOINT', 'FILESYSTEM_DISK'];
        foreach ($envVars as $var) {
            $value = env($var);
            if ($var === 'AWS_SECRET_ACCESS_KEY') {
                $this->line("- $var: " . (strlen($value) > 0 ? 'Set (' . strlen($value) . ' chars)' : 'Not set'));
            } else {
                $this->line("- $var: " . ($value ?: 'Not set'));
            }
        }
        $this->newLine();

        // 4. Temporary Directory Check
        $this->info('Temporary Directory Check:');
        $tempDir = sys_get_temp_dir();
        $this->line("- System temp dir: $tempDir");
        $this->line("- Temp dir writable: " . (is_writable($tempDir) ? 'Yes' : 'No'));
        $this->line("- Temp dir exists: " . (is_dir($tempDir) ? 'Yes' : 'No'));

        $laravelTempDir = storage_path('app/livewire-tmp');
        $this->line("- Laravel temp dir: $laravelTempDir");
        $this->line("- Laravel temp dir exists: " . (is_dir($laravelTempDir) ? 'Yes' : 'No'));
        $this->line("- Laravel temp dir writable: " . (is_writable($laravelTempDir) ? 'Yes' : 'No'));
        $this->newLine();

        // 5. Storage Directory Permissions
        $this->info('Storage Directory Permissions:');
        $storageDir = storage_path();
        $appDir = storage_path('app');
        $this->line("- Storage path: $storageDir");
        $this->line("- Storage writable: " . (is_writable($storageDir) ? 'Yes' : 'No'));
        $this->line("- App storage writable: " . (is_writable($appDir) ? 'Yes' : 'No'));
        $this->newLine();

        // 6. Test S3 Connection
        $this->info('S3 Connection Test:');
        try {
            $disk = Storage::disk('s3');
            $this->line("- S3 disk available: Yes");

            // Test write access
            $testFile = 'test-connection-' . time() . '.txt';
            $testContent = 'This is a test file created at ' . date('Y-m-d H:i:s');

            if ($disk->put($testFile, $testContent)) {
                $this->line("- S3 write test: SUCCESS", 'fg=green');

                // Test read access
                if ($disk->get($testFile) === $testContent) {
                    $this->line("- S3 read test: SUCCESS", 'fg=green');
                } else {
                    $this->line("- S3 read test: FAILED", 'fg=red');
                }

                // Clean up test file
                $disk->delete($testFile);
                $this->line("- Test file cleanup: SUCCESS", 'fg=green');
            } else {
                $this->line("- S3 write test: FAILED", 'fg=red');
            }

        } catch (Exception $e) {
            $this->error("- S3 connection error: " . $e->getMessage());

            // Check if it's a credentials issue
            if (str_contains($e->getMessage(), 'credentials') || str_contains($e->getMessage(), 'SignatureDoesNotMatch')) {
                $this->warn('This appears to be a credentials issue. Check your AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY.');
            }

            // Check if it's a network/endpoint issue
            if (str_contains($e->getMessage(), 'resolve') || str_contains($e->getMessage(), 'connection')) {
                $this->warn('This appears to be a network connectivity issue. Check your AWS_ENDPOINT and internet connection.');
            }
        }

        $this->newLine();

        // 7. Livewire Configuration Check
        $this->info('Livewire Upload Configuration:');
        try {
            $livewireConfig = config('livewire');
            $this->line("- Temporary file uploads enabled: " . (config('livewire.temporary_file_upload.disk') ? 'Yes' : 'No'));
            $this->line("- Temporary upload disk: " . config('livewire.temporary_file_upload.disk', 'default'));
            $this->line("- Upload timeout: " . config('livewire.temporary_file_upload.timeout', 'default') . ' minutes');
            $this->line("- Max upload size: " . config('livewire.temporary_file_upload.max_upload_size', 'default') . ' MB');
        } catch (Exception $e) {
            $this->error("Error getting Livewire config: " . $e->getMessage());
        }

        $this->newLine();
        $this->info('=== End Diagnostic ===');

        return 0;
    }
}