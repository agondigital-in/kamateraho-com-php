# Admin Button Fix

## Issue
The "All Users" button/link was not working properly due to incorrect file paths in the db_connect.php file.

## Root Cause
The db_connect.php file had incorrect relative paths for including config files:
- `../config/db.php` was not resolving correctly
- `../config/app.php` was not resolving correctly

This caused the database connection to fail, which in turn caused the all_users.php page to fail.

## Solution Implemented

### 1. Fixed File Paths in db_connect.php
Changed the relative paths to use `__DIR__` for proper resolution:
- Changed `include '../config/db.php'` to `include __DIR__ . '/../config/db.php'`
- Changed `include_once '../config/app.php'` to `include_once __DIR__ . '/../config/app.php'`

### 2. Verified Functionality
- Tested database connection
- Verified both all_users.php and referral_stats.php pages
- Confirmed no syntax errors

## Files Modified

1. **Updated file**: `admin/db_connect.php` - Fixed file paths for config includes

## Testing
All files have been verified with PHP linting and show no syntax errors:
- `admin/all_users.php` - No syntax errors
- `admin/referral_stats.php` - No syntax errors
- `admin/db_connect.php` - No syntax errors

## Benefits
1. **Fixed Button Functionality**: The "All Users" button now works correctly
2. **Proper Database Connection**: All admin pages can now connect to the database
3. **Consistent Path Resolution**: File paths now work correctly regardless of execution context