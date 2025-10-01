-- Update script to add is_active column to offers table
USE kamateraho;

-- Add is_active column to offers table with default value TRUE
ALTER TABLE offers 
ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER redirect_url;