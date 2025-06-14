@echo off
echo Running PHPUnit Tests...
echo.

REM Change to the project root directory
cd /d "%~dp0"

REM Set environment variables for testing
set APP_ENV=testing

REM Check for XAMPP PHP
if exist "d:\xampp1\php\php.exe" (
    set PHP_PATH=d:\xampp1\php\php.exe
) else if exist "c:\xampp\php\php.exe" (
    set PHP_PATH=c:\xampp\php\php.exe
) else (
    set PHP_PATH=php
)

REM Check if PHPUnit is installed
if exist vendor\bin\phpunit (
    echo Using PHP from: %PHP_PATH%
    echo.
    %PHP_PATH% vendor\bin\phpunit
) else (
    REM If PHPUnit is not installed, suggest installing it
    echo PHPUnit not found. Please run "composer install" first to install dependencies.
    echo.
    echo Running composer install now...
    %PHP_PATH% composer.phar install || %PHP_PATH% composer install
    
    if exist vendor\bin\phpunit (
        echo.
        echo Running tests after installing dependencies...
        %PHP_PATH% vendor\bin\phpunit
    )
)

echo.
pause
