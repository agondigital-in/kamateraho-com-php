# Trending Promotion Tasks Implementation

## Overview
Implemented the "Trending Promotion Tasks" section in index.php to display uploaded offers with the following layout:
- Image
- Amount/Price below the image
- Title below the amount
- Two buttons below the title
- 4 items per row

## Implementation Details

### Section Added
- Replaced the empty "Trending Promotion Tasks" section with a functional implementation
- Fetches all offers from the database, ordered by creation date (newest first)
- Displays up to 12 offers in the section

### Display Format
1. **Image**: Shows the offer image with a fixed height of 180px
2. **Amount/Price**: Displays the offer price in Indian Rupees (₹) format
3. **Title**: Shows the offer title
4. **Buttons**: 
   - "Earn Amount" button linking to product details
   - "Refer & Earn" button for copying referral links

### Features
- Responsive grid layout (4 items per row on desktop, adjusts for mobile)
- Fallback image display when no image is available
- Price formatting with two decimal places
- User session checking for referral link functionality
- Proper XSS protection with htmlspecialchars

### Files Modified
- index.php: Added the Trending Promotion Tasks section implementation

## Technical Details
- Uses the existing `offers` database table
- Leverages the existing `normalize_image()` function for image path handling
- Maintains consistency with the existing site design and styling
- Follows the same patterns used in other sections of the site

## Database Schema
The implementation uses the following columns from the `offers` table:
- `id`: For product details link
- `image`: For displaying the offer image
- `price`: For displaying the offer price
- `title`: For displaying the offer title
- `redirect_url`: For the referral link functionality
- `created_at`: For ordering offers (newest first)