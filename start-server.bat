@echo off

set PHP=C:\Users\ASUS\.config\herd\bin\php84\php.exe

cd /d "%~dp0"



for /f "tokens=5" %%a in ('netstat -ano ^| findstr ":8000" ^| findstr "LISTENING"') do (

    taskkill /F /PID %%a >nul 2>&1

)



"%PHP%" -c "%~dp0php.ini" artisan config:clear >nul 2>&1

echo.

echo Fashion BD running at http://127.0.0.1:8000

echo Admin: http://127.0.0.1:8000/admin/login

echo Press Ctrl+C to stop.

cd public

"%PHP%" -c "%~dp0php.ini" -S 127.0.0.1:8000 "..\server.php"


