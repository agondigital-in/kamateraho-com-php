-- SQL script to update the credit_cards table to add amount, percentage, and flat_rate fields

ALTER TABLE credit_cards 
ADD COLUMN amount DECIMAL(10, 2) DEFAULT 0.00,
ADD COLUMN percentage DECIMAL(5, 2) DEFAULT 0.00,
ADD COLUMN flat_rate DECIMAL(10, 2) DEFAULT 0.00;