<?php
// Application configuration

define('APP_NAME', 'License Management System');
define('APP_URL', 'http://localhost');
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// API configuration
define('API_RATE_LIMIT', 100); // requests per minute
define('API_KEY_LENGTH', 64);

// Pagination
define('PER_PAGE', 10);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
