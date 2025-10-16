# Spin & Earn Feature Implementation

## Overview
This feature adds a "Spin & Earn" functionality to the offers page, allowing users to spin a wheel for rewards.

## Components

### 1. Database Table
- **File**: [database/create_spin_history_table.sql](file:///C:/xampp/htdocs/kmt/database/create_spin_history_table.sql)
- **Table**: `spin_history`
- **Fields**:
  - `id`: Primary key
  - `user_id`: Reference to user
  - `reward_amount`: Amount won (0 for no reward)
  - `spin_date`: Date of spin
  - `created_at`: Timestamp

### 2. Backend Logic
- **File**: [spin_earn.php](file:///C:/xampp/htdocs/kmt/spin_earn.php)
- Handles spin requests via AJAX
- Enforces 3 spins per day limit
- Implements reward logic
- Updates wallet balance for valid rewards

### 3. Frontend UI
- **File**: [all_offers.php](file:///C:/xampp/htdocs/kmt/all_offers.php)
- Added "Spin & Earn" button
- Created modal popup with spinning wheel
- Implemented wheel animation and result display

## Game Rules

1. Users can spin 3 times per day
2. Out of 3 spins:
   - 2 times should show "Better Luck Next Time"
   - 1 time gives one of ₹5, ₹10, or ₹15 (random)
3. If ₹20 or ₹30 appears, wheel keeps spinning (no stop)
4. Rewards (₹5, ₹10, ₹15) are added to user's wallet balance

## Testing Instructions

1. Make sure the database table is created:
   ```sql
   CREATE TABLE IF NOT EXISTS spin_history (
       id INT(11) AUTO_INCREMENT PRIMARY KEY,
       user_id INT(11) NOT NULL,
       reward_amount DECIMAL(10, 2) DEFAULT 0.00,
       spin_date DATE NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
       INDEX idx_user_date (user_id, spin_date)
   );
   ```

2. Access the offers page and click the "Spin & Earn" button
3. Spin the wheel and observe the results
4. Check that rewards are added to the user's wallet

## Files Modified/Added

- [all_offers.php](file:///C:/xampp/htdocs/kmt/all_offers.php) - Added UI elements and JavaScript
- [spin_earn.php](file:///C:/xampp/htdocs/kmt/spin_earn.php) - Backend logic for spinning and rewards
- [database/create_spin_history_table.sql](file:///C:/xampp/htdocs/kmt/database/create_spin_history_table.sql) - Database schema
- [database/create_spin_history_table_fixed.php](file:///C:/xampp/htdocs/kmt/database/create_spin_history_table_fixed.php) - Script to create table