# Remove Admin Check from Admin Pages

## Issue
The admin pages (all_users.php and referral_stats.php) had admin authentication checks that prevented them from being accessed directly or by non-admin users.

## Solution Implemented

### 1. Removed Admin Check from all_users.php
Removed the following code from `admin/all_users.php`:
```php
// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
```

### 2. Removed Admin Check from referral_stats.php
Removed the following code from `admin/referral_stats.php`:
```php
// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
```

### 3. Updated Referral Links in referral_stats.php
Modified the referral links section to not depend on `$_SESSION['user_id']`:
- Replaced `$_SESSION['user_id']` with `[YOUR_USER_ID]` placeholder
- Added instructions to replace the placeholder with actual user ID

## Files Modified

1. **Updated file**: `admin/all_users.php` - Removed admin authentication check
2. **Updated file**: `admin/referral_stats.php` - Removed admin authentication check and updated referral links

## Testing
Both files have been verified with PHP linting and show no syntax errors:
- `admin/all_users.php` - No syntax errors
- `admin/referral_stats.php` - No syntax errors

## Benefits
1. **Direct Access**: Pages can now be accessed directly without admin authentication
2. **Flexibility**: Pages can be accessed by non-admin users if needed
3. **Clear Instructions**: Referral links now have clear placeholders with instructions