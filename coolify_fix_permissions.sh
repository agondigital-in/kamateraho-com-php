#!/bin/bash
#
# Coolify Permission Fix Script
# Run this script in your Coolify container to fix upload directory permissions

echo "Coolify Permission Fix Script"
echo "============================="

# Define base directory
BASE_DIR="/app"

# Check if we're in the right directory
if [ ! -d "$BASE_DIR" ]; then
    echo "Warning: $BASE_DIR directory not found"
    BASE_DIR="."
fi

echo "Base directory: $BASE_DIR"

# Create upload directories
echo "Creating upload directories..."
mkdir -p "$BASE_DIR/uploads" "$BASE_DIR/uploads/credit_cards" "$BASE_DIR/uploads/offers"

# Set permissions
echo "Setting permissions to 775..."
chmod -R 775 "$BASE_DIR/uploads"

# Try to change ownership to www-data
echo "Changing ownership to www-data..."
chown -R www-data:www-data "$BASE_DIR/uploads" 2>/dev/null || echo "Could not change ownership (might not be root or www-data user doesn't exist)"

# Verify
echo "Verifying permissions..."
for dir in "$BASE_DIR/uploads" "$BASE_DIR/uploads/credit_cards" "$BASE_DIR/uploads/offers"; do
    if [ -d "$dir" ]; then
        echo "Directory: $dir"
        ls -ld "$dir"
    else
        echo "Directory not found: $dir"
    fi
done

echo "Permission fix completed!"
echo "You can now test your upload functionality."