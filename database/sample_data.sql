-- KamateRaho Sample Data
-- Version: 1.0
-- Date: 2025-09-24

USE kamateraho;

-- Insert sample categories
INSERT INTO categories (name) VALUES 
('Amazon - Top Deals'),
('Best Cards for Shopping'),
('Electronics'),
('Fashion'),
('Home & Kitchen'),
('Mobiles & Tablets');

-- Insert sample users (password is 'password123' hashed)
INSERT INTO users (name, email, password, wallet_balance) VALUES 
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 50.00),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 75.00);

-- Insert sample offers
INSERT INTO offers (category_id, title, description, price, image) VALUES 
(1, 'Smartphone XYZ', 'Latest smartphone with 128GB storage and 48MP camera', 15000.00, 'uploads/sample1.jpg'),
(1, 'Laptop ABC', 'High performance laptop for gaming and work', 45000.00, 'uploads/sample2.jpg'),
(2, 'Credit Card Offer', 'Get 10% cashback on all shopping transactions', 0.00, 'uploads/sample3.jpg'),
(3, 'Bluetooth Headphones', 'Noise cancelling wireless headphones', 2500.00, 'uploads/sample4.jpg'),
(4, 'Designer T-Shirt', 'Premium cotton t-shirt for men', 599.00, 'uploads/sample5.jpg'),
(5, 'Kitchen Chimney', '6x4.5 ft chimney with auto clean', 8500.00, 'uploads/sample6.jpg');

-- Insert sample wallet history
INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES 
(1, 50.00, 'credit', 'approved', 'Welcome Bonus'),
(1, 200.00, 'credit', 'pending', 'Cashback from purchase'),
(1, 150.00, 'credit', 'approved', 'Referral Bonus'),
(2, 75.00, 'credit', 'approved', 'Welcome Bonus'),
(2, 100.00, 'credit', 'pending', 'Cashback from purchase');

-- Insert sample withdraw requests
INSERT INTO withdraw_requests (user_id, amount, upi_id, status) VALUES 
(1, 100.00, 'john@upi', 'pending'),
(2, 50.00, 'jane@upi', 'approved');

-- Display success message
SELECT 'Sample data inserted successfully!' AS message;