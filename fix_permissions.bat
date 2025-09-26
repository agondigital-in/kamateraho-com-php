@echo off
echo Fixing Upload Directory Permissions for KamateRaho
echo =================================================

REM Get the current directory (should be the project root)
set PROJECT_DIR=%~dp0
echo Project directory: %PROJECT_DIR%

REM Remove trailing backslash
set PROJECT_DIR=%PROJECT_DIR:~0,-1%

echo.
echo Granting full permissions to the uploads directories...
echo.

REM Grant full control to Administrators group
echo Granting permissions to Administrators group...
icacls "%PROJECT_DIR%\uploads" /grant "BUILTIN\Administrators:(OI)(CI)F" /T
icacls "%PROJECT_DIR%\uploads\credit_cards" /grant "BUILTIN\Administrators:(OI)(CI)F" /T

echo.
echo Granting permissions to SYSTEM account...
icacls "%PROJECT_DIR%\uploads" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T
icacls "%PROJECT_DIR%\uploads\credit_cards" /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T

echo.
echo Granting permissions to Users group...
icacls "%PROJECT_DIR%\uploads" /grant "BUILTIN\Users:(OI)(CI)F" /T
icacls "%PROJECT_DIR%\uploads\credit_cards" /grant "BUILTIN\Users:(OI)(CI)F" /T

echo.
echo Setting directory permissions to 0755 equivalent...
attrib +R "%PROJECT_DIR%\uploads" /S /D
attrib +R "%PROJECT_DIR%\uploads\credit_cards" /S /D

echo.
echo Permission fix complete!
echo.
echo To verify the fix:
echo 1. Run the fix_upload_permissions.php script through your web browser
echo 2. Try uploading a credit card image again
echo.
echo Press any key to exit...
pause >nul