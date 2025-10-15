# All Users Page Implementation

## Overview
This implementation adds a new admin page that displays all users in a paginated table format with search functionality. The page allows administrators to view user details, including their referral information and source platform.

## Features Implemented

### 1. User Listing with Pagination
- Displays users in a table format with 20 users per page
- Shows user ID, name, contact information, location, wallet balance, referral code, and referral source
- Clean, responsive design that works on all device sizes

### 2. Search Functionality
- Allows administrators to search users by name, email, or phone number
- Real-time filtering of user results
- Clear search button to reset filters

### 3. User Information Display
- Visual user avatars based on first letter of name
- Color-coded referral source badges (YouTube, Facebook, Instagram, Twitter, Other)
- Formatted wallet balance with currency symbol
- Clear date and time formatting for user registration

### 4. Pagination Controls
- Intelligent pagination with previous/next buttons
- Page number links for quick navigation
- Adaptive page range display (shows nearby pages)

## Technical Details

### Database Query
The implementation uses efficient database queries with:
- LIMIT and OFFSET for pagination
- Prepared statements for security
- COUNT query for total user calculation

### File Structure
- New file: `admin/all_users.php` - Main page implementation
- Modified file: `admin/includes/admin_layout.php` - Added navigation link

### Styling
- Custom CSS for user avatars
- Color-coded badges for referral sources
- Responsive table design
- Bootstrap 5 components

## How to Use

1. Navigate to the Admin Panel
2. Click on "All Users" in the sidebar navigation
3. View users in the paginated table
4. Use the search box to filter users by name, email, or phone
5. Navigate between pages using the pagination controls at the bottom

## Data Displayed

Each user row shows:
- **ID**: User's unique identifier
- **User**: Name and email with avatar
- **Contact**: Phone number
- **Location**: City and state
- **Wallet Balance**: Current balance with currency formatting
- **Referral Info**: Referral code if available
- **Source**: Platform where user was referred from
- **Joined**: Registration date and time

## Security Features

- Admin authentication check
- Prepared statements to prevent SQL injection
- HTML escaping to prevent XSS attacks
- Secure session handling

## Performance Considerations

- Efficient database queries with pagination
- Limited data fetching per page
- Optimized search functionality
- Minimal server resource usage