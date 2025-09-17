# Production File Upload Debugging Guide

## Identified Configuration
- Local environment: ✅ Working (uploads successful)
- Production environment: ❌ Failing (same environment variables)
- S3 connection test: ✅ Successful locally

## Common Production Upload Issues & Solutions

### 1. Web Server Upload Limits
**Problem**: Web server (nginx/Apache) may have lower upload limits than PHP

**Check nginx configuration** (if using nginx):
```nginx
# Add to nginx config:
client_max_body_size 100M;
client_body_timeout 120s;
client_header_timeout 120s;
```

**Check Apache configuration** (if using Apache):
```apache
# Add to .htaccess or httpd.conf:
LimitRequestBody 104857600  # 100MB
```

### 2. Server-side PHP Upload Limits
**Problem**: Production server PHP limits may be different

**Check production PHP settings**:
```bash
# On production server, run:
php -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;"
php -r "echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;"
php -r "echo 'max_execution_time: ' . ini_get('max_execution_time') . PHP_EOL;"
```

**Or check with diagnostic command**:
```bash
php artisan uploads:diagnose
```

### 3. Storage Directory Permissions
**Problem**: Web server user may not have write access to storage directories

**Fix permissions on production**:
```bash
# On production server:
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
sudo chmod -R 755 storage/
sudo chmod -R 755 bootstrap/cache/
```

### 4. Missing Temporary Upload Directory
**Problem**: Livewire temporary upload directory doesn't exist

**Create directory**:
```bash
# On production server:
mkdir -p storage/app/livewire-tmp
sudo chown www-data:www-data storage/app/livewire-tmp
sudo chmod 755 storage/app/livewire-tmp
```

### 5. SSL/TLS Issues with Digital Ocean Spaces
**Problem**: SSL certificate issues when connecting to DO Spaces

**Test S3 connection on production**:
```bash
# On production server:
php artisan uploads:diagnose
```

If SSL issues, add to `.env`:
```env
AWS_USE_PATH_STYLE_ENDPOINT=false
# Ensure proper SSL certificate validation
```

### 6. Firewall/Network Restrictions
**Problem**: Production server can't reach DO Spaces endpoint

**Test connectivity**:
```bash
# On production server:
curl -I https://sfo3.digitaloceanspaces.com
ping sfo3.digitaloceanspaces.com
```

### 7. Laravel Queue Processing
**Problem**: File moves to S3 may be queued and failing

**Check queue status**:
```bash
# On production server:
php artisan queue:work --once
php artisan queue:failed
```

### 8. Livewire Asset Issues
**Problem**: Livewire assets not published or wrong version

**Republish Livewire assets**:
```bash
# On production server:
php artisan livewire:publish --force
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### 9. CSRF Token Issues
**Problem**: Session/CSRF issues in production

**Debug steps**:
1. Check if sessions are working properly
2. Verify CSRF tokens are being generated
3. Check if multiple server instances are causing session issues

## Step-by-Step Production Debugging

### Step 1: Run Diagnostic
```bash
php artisan uploads:diagnose
```

### Step 2: Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Step 3: Test with Minimal File
Try uploading a very small text file (< 1MB) to isolate the issue.

### Step 4: Check Browser Developer Tools
- Look for JavaScript errors
- Check network requests for failed uploads
- Look for 413 (Request Entity Too Large) or 500 errors

### Step 5: Test Direct S3 Upload
Create a test script to directly upload to S3, bypassing Livewire:

```php
<?php
// test-direct-s3.php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Storage;

$disk = Storage::disk('s3');
$testContent = "Test content " . date('Y-m-d H:i:s');
$result = $disk->put('direct-test.txt', $testContent);

echo $result ? "Direct S3 upload: SUCCESS" : "Direct S3 upload: FAILED";
```

## Most Likely Fixes for Your Case

Based on your description, the most likely issues are:

1. **Web server upload limits** - nginx/Apache may have lower limits than PHP
2. **Storage permissions** - Production web server user may not have write access
3. **Missing livewire-tmp directory** - Directory may not exist on production

## Quick Fix Commands for Production

Run these commands on your production server:

```bash
# 1. Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# 2. Ensure livewire temp directory exists
mkdir -p storage/app/livewire-tmp
sudo chown www-data:www-data storage/app/livewire-tmp

# 3. Clear all caches
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 4. Run diagnostic
php artisan uploads:diagnose

# 5. Test file upload in Filament admin
```

If the above doesn't work, check your web server configuration for upload limits.