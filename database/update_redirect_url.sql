-- SQL script to increase the length of redirect_url column in offers table
USE kamateraho;

-- Check current column definition
DESCRIBE offers;

-- Modify the redirect_url column to increase its length
ALTER TABLE offers MODIFY redirect_url VARCHAR(2000);

-- Verify the change
DESCRIBE offers;

-- Show any offers with long URLs to verify the fix works
SELECT id, title, CHAR_LENGTH(redirect_url) as url_length FROM offers WHERE CHAR_LENGTH(redirect_url) > 500 ORDER BY url_length DESC LIMIT 5;