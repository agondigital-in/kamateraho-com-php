# Coolify Deployment Guide

This guide explains how to properly deploy the KamateRaho application to Coolify with correct file upload permissions.

## Common Issues and Solutions

### Permission Denied Error
If you encounter this error:
```
Warning: move_uploaded_file(/app/config/../uploads/credit_cards/xxxx.png): Failed to open stream: Permission denied
```

This happens because the upload directories don't have proper write permissions in the containerized environment.

## Solutions

### 1. Use the Provided Dockerfile
The repository now includes a [Dockerfile](file:///c:/xampp/htdocs/kmt/Dockerfile) that sets proper permissions:

```dockerfile
# Sets proper permissions for upload directories
RUN mkdir -p /var/www/html/uploads/credit_cards /var/www/html/uploads/offers \
    && chmod -R 775 /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads
```

### 2. Run Permission Fix Script
After deployment, run the permission fix script:

1. Access your Coolify container terminal
2. Navigate to your app directory
3. Run:
```bash
php fix_upload_permissions_coolify.php
```

### 3. Manual Permission Fix
If needed, manually set permissions in the Coolify terminal:

```bash
# Create directories if they don't exist
mkdir -p uploads/credit_cards uploads/offers

# Set permissions
chmod -R 775 uploads/
chown -R www-data:www-data uploads/
```

### 4. Environment Variables
Make sure these environment variables are set in Coolify:

```
APP_ENV=production
APP_URL=https://yourdomain.com
DB_HOST=your-db-host
DB_NAME=your-db-name
DB_USER=your-db-user
DB_PASSWORD=your-db-password
```

## Testing Uploads
After fixing permissions:

1. Go to Admin Panel → Manage Credit Cards
2. Try to upload a new credit card image
3. Check that no permission errors occur

## Persistent Storage
If using persistent storage in Coolify:

1. Mount the `uploads` directory to persistent storage
2. Ensure the mounted volume has proper permissions (775)
3. Set the owner to www-data:www-data

## Troubleshooting

### Still Getting Permission Errors?
1. Check that the `uploads` directory exists
2. Verify it has 775 permissions
3. Confirm the owner is www-data:www-data
4. Restart the web server after making changes

### Headers Already Sent Error
This occurs when output is sent before redirects. The application now uses JavaScript redirects to avoid this issue.

### Need Help?
If you continue to have issues:
1. Check the Coolify application logs
2. Run the [test_credit_cards_upload.php](file:///c:/xampp/htdocs/kmt/test_credit_cards_upload.php) script
3. Contact support with the error messages