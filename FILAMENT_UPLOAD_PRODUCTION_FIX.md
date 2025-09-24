# Filament Upload Production Fix

This document outlines the solution for the 401 errors when uploading PI Chart Images in Filament admin on production.

## Problem
The token/signature for file uploads is different between local and production environments, causing 401 errors:
- Local token: `y1ddly86B8Kz2PLmrXEbGPlXpPKjT0w6nMXj0AgQ`
- Production has different signature: `2fcfee8b4d5c3250df0c3318c74c052010fc4ede5940411a6e2012775edf3ab5`

## Root Cause
1. **Different APP_KEY** between environments
2. **Session configuration** differences
3. **HTTPS/HTTP** protocol differences
4. **CSRF token** generation and validation inconsistencies

## Solution Applied

### 1. Custom Middleware (`FixFilamentUploads`)
Created `app/Http/Middleware/FixFilamentUploads.php` to:
- Handle token validation specifically for Filament uploads
- Fix signature validation issues in production
- Ensure proper session management
- Set appropriate headers for AJAX requests

### 2. AppServiceProvider Updates
Modified `app/Providers/AppServiceProvider.php` to:
- Force HTTPS in production
- Configure consistent session settings
- Extend session lifetime for file uploads
- Set consistent cookie configuration

### 3. Filament Panel Provider Updates
Modified `app/Providers/Filament/AdminPanelProvider.php` to:
- Add the custom middleware to the middleware stack
- Disable SPA mode to prevent token issues
- Set proper panel configuration

## Required Production Environment Variables

Add these to your production `.env` file:

```bash
# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=240
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_COOKIE=btcs_coach_session
SESSION_PATH=/
SESSION_ENCRYPT=false

# CSRF Protection
CSRF_COOKIE=csrf_token

# File Upload Configuration
LIVEWIRE_TEMPORARY_FILE_UPLOAD_TIMEOUT=10
```

## Testing the Fix

1. **Deploy the changes** to production
2. **Clear caches**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan session:table # if needed
   php artisan migrate # if session table doesn't exist
   ```
3. **Test file upload** in Filament admin
4. **Check browser network tab** for proper token/signature generation

## Verification Steps

1. Login to Filament admin in production
2. Navigate to Users > Edit User
3. Try uploading a PI Chart Image
4. Verify no 401 errors occur
5. Check that file uploads successfully to S3

## Fallback Options

If the issue persists, try these additional steps:

### Option 1: Regenerate APP_KEY
```bash
php artisan key:generate --force
```
**Warning**: This will invalidate all existing sessions and encrypted data.

### Option 2: Use File Disk Temporarily
In `UserResource.php`, temporarily change:
```php
->disk('local') // instead of 's3'
```

### Option 3: Disable CSRF for Upload Routes
Add to `app/Http/Middleware/VerifyCsrfToken.php`:
```php
protected $except = [
    'livewire/upload-file',
    'admin/*/livewire/upload-file',
];
```

## Files Modified

1. `app/Http/Middleware/FixFilamentUploads.php` (new)
2. `app/Providers/AppServiceProvider.php`
3. `app/Providers/Filament/AdminPanelProvider.php`

## Monitoring

Monitor these logs after deployment:
- Laravel logs for 401 errors
- S3 upload logs
- Session-related errors
- CSRF token validation errors

The solution addresses the core issue of token/signature inconsistencies between environments while maintaining security and proper file upload functionality.