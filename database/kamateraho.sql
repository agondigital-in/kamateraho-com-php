-- KamateRaho Database Schema
-- Version: 1.0
-- Date: 2025-09-24

-- Create database
CREATE DATABASE IF NOT EXISTS kamateraho;
USE kamateraho;

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create offers table
CREATE TABLE IF NOT EXISTS offers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    redirect_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create wallet_history table
CREATE TABLE IF NOT EXISTS wallet_history (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create withdraw_requests table
CREATE TABLE IF NOT EXISTS withdraw_requests (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    upi_id VARCHAR(255) NOT NULL,
    screenshot VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    offer_title VARCHAR(255),
    offer_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create credit_cards table for storing credit card images and links
CREATE TABLE IF NOT EXISTS credit_cards (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(500) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Indexes for better performance
CREATE INDEX idx_categories_name ON categories(name);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_offers_category ON offers(category_id);
CREATE INDEX idx_wallet_user ON wallet_history(user_id);
CREATE INDEX idx_wallet_status ON wallet_history(status);
CREATE INDEX idx_withdraw_user ON withdraw_requests(user_id);
CREATE INDEX idx_withdraw_status ON withdraw_requests(status);

-- Display success message
SELECT 'Database and tables created successfully!' AS message;