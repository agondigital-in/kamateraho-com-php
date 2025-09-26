# KamateRaho - CashKaro.com Clone

A CashKaro.com-like cashback website built with PHP and MySQL.

## Features

### Admin Workflow
1. **Add Category** (`admin/add_category.php`)
   - Add new categories by name (e.g., "Amazon - Top Deals", "Best Cards for Shopping")
   - Categories are saved to the database

2. **Manage Categories** (`admin/manage_categories.php`)
   - View all categories
   - Delete categories with ❌ Delete button
   - Deleted categories are removed from the database

3. **Upload Offer** (`admin/upload_offer.php`)
   - Select category from dropdown
   - Add title, description, price
   - Upload offer image
   - Offers are displayed in user dashboard under their category

### User Workflow
1. **Hero Section**
   - Banner with "Start Shopping" button

2. **Categories Section**
   - Dynamic loading of categories from database
   - Each category shows its offers

3. **Offers**
   - Display of offer image, title, price, description
   - "Buy Now" button for each offer

4. **Wallet System**
   - ₹50 bonus on first login
   - "Buy Now" adds entry to wallet history as "pending"
   - Admin approval adds amount to wallet

5. **Withdraw**
   - Users can request withdrawal (min ₹200)
   - Requires UPI ID and screenshot
   - Admin can Accept/Reject requests

## Installation

1. Place all files in your web server directory (e.g., `htdocs/kmt` for XAMPP)
2. Copy `.env.example` to `.env` and configure your environment variables
3. Test the database connection with `test_remote_db.php`
4. If the connection succeeds but registration fails, run `apply_database_updates.php` to update the database schema
5. Run `fix_permissions.php` or `fix_permissions.bat` (as Administrator) to ensure upload directories have correct permissions
6. Access the application through your web browser

## Fixing Upload Permission Issues

If you encounter "Upload directory is not writable" errors:

1. Run `fix_upload_permissions.php` through your web browser to diagnose the issue
2. On Windows with XAMPP, run `fix_permissions.bat` as Administrator
3. Ensure the Apache service has write permissions to the `uploads` and `uploads/credit_cards` directories

## Environment Configuration

The application uses environment variables for configuration. Copy `.env.example` to `.env` and modify the values as needed.

Key environment variables:
- `DB_HOST` - Database host (remote server IP)
- `DB_PORT` - Database port (default: 3307)
- `DB_DATABASE` - Database name (default: kamateraho)
- `DB_USERNAME` - Database username (default: root)
- `DB_PASSWORD` - Database password (complex password provided)

## Admin Access
- Admin Login: `https://kamateraho1.agondev.space/admin/login.php`
- Default credentials: `admin` / `admin123`

## User Access
- User Registration: `https://kamateraho1.agondev.space/register.php`
- User Login: `https://kamateraho1.agondev.space/login.php`

## File Structure
```
kmt/
├── config/
│   ├── app.php         # Application configuration
│   ├── db.php          # Database connection
│   └── env.php         # Environment loader
├── create_db.php       # Database and table creation
└── test_db.php         # Database connection test
├── admin/
│   ├── login.php       # Admin login
│   ├── index.php       # Admin dashboard
│   ├── add_category.php
│   ├── manage_categories.php
│   ├── upload_offer.php
│   ├── approve_wallet.php
│   └── approve_withdraw.php
├── includes/
│   └── navbar.php      # Navigation bar
├── css/
│   └── style.css       # Custom styles
├── uploads/            # Uploaded images (created automatically)
├── index.php           # Homepage
├── category.php        # Category offers page
├── register.php        # User registration
├── login.php           # User login
├── dashboard.php       # User dashboard
├── withdraw.php        # Withdrawal request
└── logout.php          # Logout
```

## Database Tables
1. `categories` - Store offer categories
2. `offers` - Store individual offers
3. `users` - Store user information
4. `wallet_history` - Track wallet transactions
5. `withdraw_requests` - Track withdrawal requests

## Technologies Used
- PHP 7+
- MySQL
- Bootstrap 5
- HTML5 & CSS3
- JavaScript (Bootstrap components)

## Notes
- This is a demo application for educational purposes
- In a production environment, additional security measures should be implemented
- Passwords are properly hashed using PHP's `password_hash()` function
- File uploads are validated for security