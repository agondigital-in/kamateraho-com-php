-- Update users table to add phone, city, and state columns
ALTER TABLE users 
ADD COLUMN phone VARCHAR(20) AFTER email,
ADD COLUMN city VARCHAR(100) AFTER phone,
ADD COLUMN state VARCHAR(100) AFTER city;