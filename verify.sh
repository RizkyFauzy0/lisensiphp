#!/bin/bash

echo "==================================="
echo "License Management System - Verification"
echo "==================================="
echo ""

# Check if all required files exist
echo "Checking required files..."

files=(
    "index.php"
    ".htaccess"
    "config/config.php"
    "config/database.php"
    "database/schema.sql"
    "app/models/Database.php"
    "app/models/User.php"
    "app/models/License.php"
    "app/models/ApiLog.php"
    "app/controllers/AuthController.php"
    "app/controllers/DashboardController.php"
    "app/controllers/LicenseController.php"
    "app/controllers/UserController.php"
    "app/controllers/ApiController.php"
    "app/views/layouts/main.php"
    "app/views/auth/login.php"
    "app/views/auth/register.php"
    "app/views/dashboard/index.php"
    "app/views/licenses/index.php"
    "app/views/licenses/create.php"
    "app/views/licenses/edit.php"
    "app/views/licenses/show.php"
    "app/views/users/index.php"
    "app/views/users/edit.php"
    "api/index.php"
    "api/.htaccess"
)

all_exist=true
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ $file"
    else
        echo "✗ $file (MISSING)"
        all_exist=false
    fi
done

echo ""
if [ "$all_exist" = true ]; then
    echo "✓ All required files exist!"
else
    echo "✗ Some files are missing!"
    exit 1
fi

echo ""
echo "Checking PHP syntax..."
php_errors=0
for file in $(find app config api -name "*.php" 2>/dev/null); do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo "✗ Syntax error in $file"
        php_errors=$((php_errors + 1))
    fi
done

if [ $php_errors -eq 0 ]; then
    echo "✓ No PHP syntax errors found!"
else
    echo "✗ Found $php_errors PHP syntax errors!"
    exit 1
fi

echo ""
echo "Checking directory structure..."
dirs=(
    "app/controllers"
    "app/models"
    "app/views/layouts"
    "app/views/auth"
    "app/views/dashboard"
    "app/views/licenses"
    "app/views/users"
    "config"
    "public/css"
    "public/js"
    "api"
    "database"
)

all_dirs_exist=true
for dir in "${dirs[@]}"; do
    if [ -d "$dir" ]; then
        echo "✓ $dir"
    else
        echo "✗ $dir (MISSING)"
        all_dirs_exist=false
    fi
done

echo ""
if [ "$all_dirs_exist" = true ]; then
    echo "✓ All required directories exist!"
else
    echo "✗ Some directories are missing!"
    exit 1
fi

echo ""
echo "==================================="
echo "✓ Verification Complete!"
echo "==================================="
echo ""
echo "Next steps:"
echo "1. Create database: CREATE DATABASE lisensiphp;"
echo "2. Import schema: mysql -u root -p lisensiphp < database/schema.sql"
echo "3. Configure database in config/database.php"
echo "4. Access application: http://localhost/lisensiphp"
echo "5. Default login: admin / admin123"
echo ""
