# Logout Redirect Update

## Change Summary
Updated the logout functionality to redirect users from the main application (`kmt/index.php`) to the public landing page (`kamateraho/index.php`) upon logout.

## File Modified
- `kmt/logout.php` - Updated redirect location

## Before Change
```php
<?php
session_start();
session_destroy();
header("Location: index.php");
exit;
?>
```

## After Change
```php
<?php
session_start();
session_destroy();
header("Location: kamateraho/index.php");
exit;
?>
```

## User Flow Impact
1. User accesses `kmt/index.php` (main application) - requires authentication
2. User clicks logout from the navigation dropdown
3. User is redirected to `kmt/kamateraho/index.php` (public landing page)
4. User can browse public information or choose to login again

## Benefits
- Provides a clear separation between authenticated and public areas
- Enhances user experience by showing the welcome page after logout
- Maintains consistency with the overall site structure
- Allows users to easily re-authenticate if desired