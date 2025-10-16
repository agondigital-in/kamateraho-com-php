-- SQL script to update the credit_cards table to:
-- 1. Increase the size of the link column
-- 2. Add description and sequence_id columns

ALTER TABLE credit_cards 
MODIFY COLUMN link TEXT,
ADD COLUMN description TEXT,
ADD COLUMN sequence_id INT(11) DEFAULT 0;