@echo off
cd /d "c:\xampp\htdocs\kamateraho"
echo Generating sample image placeholders...
php generate_images.php
echo.
echo Image placeholders created successfully!
echo.
pause