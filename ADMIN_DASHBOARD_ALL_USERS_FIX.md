# Admin Dashboard All Users Button Fix

## Issue
The "All Users" button/link was not easily accessible from the admin dashboard. Users had to navigate through the sidebar menu to access the All Users page.

## Solution Implemented

### 1. Added "View All Users" Button
Modified `admin/index.php` to add a "View All Users" button directly on the Total Users card:
- Added a button below the Total Users statistics
- Links directly to the all_users.php page
- Uses Bootstrap styling for consistency

## Files Modified

1. **Updated file**: `admin/index.php` - Added "View All Users" button to the Total Users card

## Testing
The file has been verified with PHP linting and shows no syntax errors.

## Benefits
1. **Improved User Experience**: Admins can now access the All Users page directly from the dashboard
2. **Faster Navigation**: No need to scroll through the sidebar menu
3. **Better Discoverability**: Makes the All Users feature more visible to admins