@echo off
title Innovance 6-Head CNC Angle Line HMI
echo ========================================================
echo Starting Innovance 6-Head CNC Angle Line Full-Stack HMI
echo ========================================================

:: Check Node.js
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] Node.js is not installed or not in PATH.
    pause
    exit /b 1
)

:: Run database migration if needed
cd /d "%~dp0\apps\server"
call npx prisma db push --skip-generate >nul 2>nul

:: Start the application
cd /d "%~dp0"
echo Starting Backend & Frontend Servers...
npm run dev

pause
