-- SQL script to fix the "Data too long for column 'redirect_url'" error
-- Increases the length of redirect_url column from VARCHAR(500) to VARCHAR(2000)

USE kamateraho;

-- Show current table structure
DESCRIBE offers;

-- Modify the redirect_url column to increase its length
ALTER TABLE offers MODIFY redirect_url VARCHAR(2000);

-- Also update the credit_cards table link column
ALTER TABLE credit_cards MODIFY link VARCHAR(2000);

-- Verify the changes
DESCRIBE offers;
DESCRIBE credit_cards;

-- Show any offers with long URLs to verify the fix works
SELECT id, title, CHAR_LENGTH(redirect_url) as url_length FROM offers WHERE CHAR_LENGTH(redirect_url) > 500 ORDER BY url_length DESC LIMIT 5;

-- Show any credit cards with long links
SELECT id, title, CHAR_LENGTH(link) as link_length FROM credit_cards WHERE CHAR_LENGTH(link) > 500 ORDER BY link_length DESC LIMIT 5;