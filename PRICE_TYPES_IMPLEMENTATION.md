# Price Types Implementation

This document describes the implementation of different price types (Fixed, Flat Percent, Upto Percent) for offers in the KamateRaho application.

## Changes Made

### 1. Database Migration
- Added a new `price_type` column to the `offers` table with ENUM values: 'fixed', 'flat_percent', 'upto_percent'
- Set default value to 'fixed' for backward compatibility
- Created migration script: `database/update_offers_table_add_price_type.php`

### 2. Admin Panel Changes
- Modified `admin/upload_offer.php` to include price type selection dropdown
- Modified `admin/edit_offer.php` to include price type selection dropdown
- Updated form processing to handle the new price_type field
- Added JavaScript to dynamically update the price field label based on the selected price type:
  - Fixed (₹) shows "Price (₹)"
  - Flat Percent (%) shows "Percent (%)"
  - Upto Percent (%) shows "Percent (%)"

### 3. Frontend Display
- Created price helper functions in `includes/price_helper.php`:
  - `format_price()`: Formats price based on type
  - `display_price()`: Displays price with appropriate HTML styling
- Updated display in:
  - `index.php`: Trending Promotion Tasks section
  - `all_offers.php`: All offers page
  - `product_details.php`: Product details page
  - `admin/manage_offers.php`: Admin offers management page

### 4. Styling
- Added CSS classes for different price types:
  - `.price-fixed`: Green color for fixed prices
  - `.price-flat-percent`: Blue color for flat percent prices
  - `.price-upto-percent`: Yellow color for upto percent prices

## Usage

### Admin Panel
1. When uploading a new offer, admins can select the price type from the dropdown:
   - Fixed (₹) - Default option for fixed rupee amounts
   - Flat Percent (%) - For flat percentage rewards
   - Upto Percent (%) - For variable percentage rewards up to a maximum

2. When editing an existing offer, admins can change the price type as needed.

3. The price field label will automatically update based on the selected price type:
   - For Fixed (₹): "Price (₹)"
   - For Flat Percent (%): "Percent (%)"
   - For Upto Percent (%): "Percent (%)"

### Frontend Display
Prices will be displayed with appropriate formatting and colors:
- Fixed prices: ₹100.00 (green)
- Flat percent prices: 15.75% (blue)
- Upto percent prices: Upto 250.00% (yellow)

## Testing
A test script `test_price_types.php` was created to verify the functionality:
- Tests price formatting functions
- Tests database queries with price types
- Verifies correct display of different price types

## Backward Compatibility
- Existing offers without a price_type will default to 'fixed'
- All existing functionality remains unchanged
- No breaking changes to the database structure