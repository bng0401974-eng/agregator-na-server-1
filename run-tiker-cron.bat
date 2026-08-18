@echo off
setlocal
cd /d C:\xampp\htdocs\agregator1
C:\xampp\php\php.exe -f C:\xampp\htdocs\agregator1\fetcher-tiker.php
if errorlevel 1 (
    echo TIKER CRON FAILED
    exit /b 1
)
echo TIKER CRON OK
pause
