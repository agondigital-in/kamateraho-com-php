@echo off
cd /d "c:\xampp\htdocs\kamate raho"
echo Generating sample image placeholders...
php generate_images.php
echo.
echo Image placeholders created successfully!
echo.
pause