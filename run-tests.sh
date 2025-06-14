#!/bin/bash
echo "Running PHPUnit Tests..."
echo ""

# Change to the project root directory
cd "$(dirname "$0")"

# Check if PHPUnit is installed
if [ -f "vendor/bin/phpunit" ]; then
    # Set environment variables for testing (if needed)
    export APP_ENV=testing
    
    # Point to XAMPP PHP if needed
    if [ -f "/xampp/php/php" ]; then
        /xampp/php/php vendor/bin/phpunit
    else
        php vendor/bin/phpunit
    fi
else
    # If PHPUnit is not installed, suggest installing it
    echo "PHPUnit not found. Please run \"composer install\" first to install dependencies."
fi

echo ""
read -p "Press Enter to continue..."
