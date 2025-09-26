-- Update script to add redirect_url column to offers table
USE kamateraho;

-- Add redirect_url column to offers table
ALTER TABLE offers 
ADD COLUMN redirect_url VARCHAR(500) AFTER image;