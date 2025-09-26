@echo off
echo Setting permissions for credit cards upload directory...
echo.

REM Create the directory if it doesn't exist
if not exist "uploads\credit_cards" (
    echo Creating uploads\credit_cards directory...
    mkdir "uploads\credit_cards"
)

REM Set permissions (Windows version)
echo Setting permissions for uploads directory...
icacls "uploads" /grant Users:(OI)(CI)F /T
echo.

echo Done! Upload directories should now have proper permissions.
echo You may need to run this as administrator for it to work properly.
pause