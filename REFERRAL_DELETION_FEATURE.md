# Referral Deletion Feature

## Overview
This feature allows administrators to delete referral users from the system while automatically deducting the referral bonus amount from the referrer's wallet.

## Implementation Details

### How It Works

1. **Admin navigates to Wallet Management**
   - Selects a user who has referred others
   - Views the "Referral Information" section

2. **Delete Referral Button**
   - Appears next to each referred user in the referral information
   - Clicking the button opens a confirmation modal

3. **Confirmation Modal**
   - Asks "Are you sure you want to delete this referral?"
   - Explains the consequences of deletion:
     - Deletes the referred user and all their data
     - Deducts the referral bonus amount from the referrer's wallet
     - Adds a deduction entry to the referrer's wallet history

4. **Deletion Process**
   - When confirmed, the system:
     - Identifies the referral bonus amount from wallet history
     - Deducts that amount from the referrer's wallet balance
     - Records the deduction in the referrer's wallet history
     - Deletes the referred user (cascading deletes related records)
     - Shows success message
     - Refreshes the page automatically

5. **After Deletion**
   - The deleted referral no longer appears in the list
   - The referrer's wallet balance shows the new (reduced) amount

### Technical Implementation

- **File Modified**: `admin/wallet_management.php`
- **Database Tables Affected**: 
  - `users` (DELETE operation with CASCADE)
  - `wallet_history` (INSERT operation for deduction record)
- **Security**: Uses prepared statements to prevent SQL injection
- **Data Integrity**: Uses database transactions to ensure consistency

### Testing

A test script is available at `admin/test_referral_deletion.php` to verify functionality.