# Referral Tracking Implementation

## Overview
This implementation adds referral source tracking to the KamateRaho platform, allowing administrators to track where users are coming from (YouTube, Facebook, Instagram, Twitter, or other sources).

## Features Implemented

### 1. Database Schema Changes
- Added `referral_source` column to the `users` table to track where users are coming from
- Added index on `referral_source` column for better query performance

### 2. Registration System Updates
- Modified `register.php` to capture referral source from URL parameters
- Added support for `source` parameter in registration URLs
- Added hidden form field to preserve referral source during form submission

### 3. User Dashboard Enhancements
- Updated `index.php` to show platform-specific referral links
- Added social media buttons for easy sharing on different platforms
- Enhanced referral modal with platform-specific links

### 4. Admin Panel Features
- Created `referral_stats.php` admin page to view referral statistics
- Added referral statistics to admin sidebar navigation
- Implemented referral source breakdown with percentages

## How It Works

### For Users
1. Users can register through platform-specific links:
   - YouTube: `https://kamateraho.com/register.php?ref=USER_ID&source=youtube`
   - Facebook: `https://kamateraho.com/register.php?ref=USER_ID&source=facebook`
   - Instagram: `https://kamateraho.com/register.php?ref=USER_ID&source=instagram`
   - Twitter: `https://kamateraho.com/register.php?ref=USER_ID&source=twitter`

2. The referral source is automatically captured and stored in the database

3. Users can access platform-specific referral links from their dashboard

### For Administrators
1. Navigate to "Referral Statistics" in the admin panel
2. View breakdown of users by referral source
3. See percentages and total counts for each platform

## Technical Details

### Database Changes
```sql
ALTER TABLE users ADD COLUMN referral_source VARCHAR(50) AFTER referral_code;
CREATE INDEX idx_users_referral_source ON users(referral_source);
```

### URL Parameters
- `ref`: Referrer user ID (existing functionality)
- `source`: Referral source (new functionality)

### Supported Sources
- `youtube`
- `facebook`
- `instagram`
- `twitter`
- `other` (default for unspecified sources)

## Files Modified

1. `register.php` - Added referral source capture functionality
2. `index.php` - Added platform-specific referral links in referral modal
3. `admin/includes/admin_layout.php` - Added navigation link to referral stats
4. `admin/referral_stats.php` - New admin page for referral statistics

## Testing

Created `test_referral_source.php` to verify the implementation:
- Inserts test user with referral source
- Verifies referral source is stored correctly
- Cleans up test data

## Usage Examples

### Platform-Specific Registration Links
```
# YouTube referral
https://kamateraho.com/register.php?ref=123&source=youtube

# Facebook referral
https://kamateraho.com/register.php?ref=123&source=facebook

# Instagram referral
https://kamateraho.com/register.php?ref=123&source=instagram

# Twitter referral
https://kamateraho.com/register.php?ref=123&source=twitter
```

### Admin Panel Access
Navigate to `/admin/referral_stats.php` to view referral statistics.