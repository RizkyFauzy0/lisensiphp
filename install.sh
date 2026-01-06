#!/bin/bash

echo "==================================="
echo "License Management System - Installation"
echo "==================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if MySQL is running
echo "Checking MySQL service..."
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}✗ MySQL client not found!${NC}"
    echo "Please install MySQL client first."
    exit 1
fi
echo -e "${GREEN}✓ MySQL client found${NC}"
echo ""

# Get database credentials
echo "Enter database configuration:"
read -p "MySQL Host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Database Name [lisensiphp]: " DB_NAME
DB_NAME=${DB_NAME:-lisensiphp}

read -p "MySQL Username [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "MySQL Password: " DB_PASS
echo ""
echo ""

# Test MySQL connection
echo "Testing MySQL connection..."
if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" &> /dev/null; then
    echo -e "${GREEN}✓ MySQL connection successful${NC}"
else
    echo -e "${RED}✗ MySQL connection failed!${NC}"
    echo "Please check your credentials."
    exit 1
fi
echo ""

# Create database
echo "Creating database..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database created successfully${NC}"
else
    echo -e "${YELLOW}⚠ Database might already exist${NC}"
fi
echo ""

# Import schema
echo "Importing database schema..."
if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql 2>/dev/null; then
    echo -e "${GREEN}✓ Schema imported successfully${NC}"
else
    echo -e "${RED}✗ Failed to import schema${NC}"
    exit 1
fi
echo ""

# Update config file
echo "Updating configuration..."
cat > config/database.php << EOF
<?php
// Database configuration

define('DB_HOST', '$DB_HOST');
define('DB_NAME', '$DB_NAME');
define('DB_USER', '$DB_USER');
define('DB_PASS', '$DB_PASS');
define('DB_CHARSET', 'utf8mb4');
EOF

echo -e "${GREEN}✓ Configuration updated${NC}"
echo ""

# Get APP_URL
read -p "Application URL [http://localhost/lisensiphp]: " APP_URL
APP_URL=${APP_URL:-http://localhost/lisensiphp}

# Update config.php
sed -i "s|define('APP_URL', '.*');|define('APP_URL', '$APP_URL');|g" config/config.php

echo ""
echo "==================================="
echo -e "${GREEN}✓ Installation Complete!${NC}"
echo "==================================="
echo ""
echo "Application Details:"
echo "-----------------------------------"
echo "URL: $APP_URL"
echo "Database: $DB_NAME"
echo ""
echo "Default Login:"
echo "Username: admin"
echo "Password: admin123"
echo ""
echo -e "${YELLOW}⚠ IMPORTANT:${NC}"
echo "1. Change the default admin password after first login"
echo "2. Make sure mod_rewrite is enabled in Apache"
echo "3. Ensure .htaccess files are present"
echo "4. Set proper file permissions (755 for directories, 644 for files)"
echo ""
echo "You can now access the application at:"
echo -e "${GREEN}$APP_URL${NC}"
echo ""
