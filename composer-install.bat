@echo off
REM run from project root: this will install PHPMailer via Composer
cd /d "%~dp0"
echo Checking for Composer...
composer --version >nul 2>&1
if %ERRORLEVEL% neq 0 (
  echo Composer not found. Visit https://getcomposer.org/download/ and install Composer, then re-run this script.
  pause
  exit /b 1
)
echo Installing PHPMailer via Composer...
composer require phpmailer/phpmailer
if %ERRORLEVEL% neq 0 (
  echo Composer failed. Check output above.
  pause
  exit /b 1
)
echo PHPMailer installed successfully.
pause
