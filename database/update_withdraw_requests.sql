-- Update script to add offer_title and offer_description columns to withdraw_requests table
-- This script should be run on existing databases that were created before these columns were added

USE kamateraho;

-- Add offer_title column if it doesn't exist
ALTER TABLE withdraw_requests 
ADD COLUMN IF NOT EXISTS offer_title VARCHAR(255) AFTER status;

-- Add offer_description column if it doesn't exist
ALTER TABLE withdraw_requests 
ADD COLUMN IF NOT EXISTS offer_description TEXT AFTER offer_title;

-- Display success message
SELECT 'withdraw_requests table updated successfully!' AS message;