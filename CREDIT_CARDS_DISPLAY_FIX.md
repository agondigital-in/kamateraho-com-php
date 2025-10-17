# Credit Cards Display Fix

## Issue
Only 4 credit cards were displaying on the index page, even though there were more cards in the database.

## Root Cause
The SQL query in [index.php](file:///c%3A/xampp/htdocs/kmt/index.php) had a `LIMIT 4` clause that restricted the number of credit cards fetched from the database.

## Fix Applied
Removed the `LIMIT 4` clause from the SQL query in [index.php](file:///c%3A/xampp/htdocs/kmt/index.php) at line 54:

### Before:
```php
$stmt = $pdo->query("SELECT id, title, image, link, amount, percentage, flat_rate, is_active, created_at FROM credit_cards ORDER BY created_at DESC LIMIT 4");
```

### After:
```php
$stmt = $pdo->query("SELECT id, title, image, link, amount, percentage, flat_rate, is_active, created_at FROM credit_cards ORDER BY created_at DESC");
```

## Result
All credit cards in the database will now be displayed on the index page, not just the first 4.

## Additional Notes
- The display section was already properly implemented to handle any number of credit cards
- Image paths are normalized for proper display
- The section has a proper heading for better UX

## Testing
To verify the fix:
1. Visit the main page of your website
2. Check that all credit cards are now displayed
3. If you want to limit the number of cards again, you can add the LIMIT clause back with a different number