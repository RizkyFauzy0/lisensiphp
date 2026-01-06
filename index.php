<?php
// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
session_start();

// Load configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Load models
require_once APP_PATH . '/models/Database.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/License.php';
require_once APP_PATH . '/models/ApiLog.php';

// Load controllers
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/LicenseController.php';
require_once APP_PATH . '/controllers/UserController.php';
require_once APP_PATH . '/controllers/ApiController.php';
require_once APP_PATH . '/controllers/DocumentationController.php';

// Simple router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

// Route handling
switch ($uri) {
    case '':
    case 'login':
        if (AuthController::isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        if (AuthController::isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'licenses':
        $controller = new LicenseController();
        $controller->index();
        break;

    case 'licenses/create':
        $controller = new LicenseController();
        $controller->create();
        break;

    case 'licenses/show':
        $controller = new LicenseController();
        $controller->show();
        break;

    case 'licenses/edit':
        $controller = new LicenseController();
        $controller->edit();
        break;

    case 'licenses/delete':
        $controller = new LicenseController();
        $controller->delete();
        break;

    case 'licenses/regenerate-api-key':
        $controller = new LicenseController();
        $controller->regenerateApiKey();
        break;

    case 'licenses/reset-request-count':
        $controller = new LicenseController();
        $controller->resetRequestCount();
        break;

    case 'users':
        $controller = new UserController();
        $controller->index();
        break;

    case 'users/edit':
        $controller = new UserController();
        $controller->edit();
        break;

    case 'users/delete':
        $controller = new UserController();
        $controller->delete();
        break;

    case 'documentation':
        $controller = new DocumentationController();
        $controller->index();
        break;

    default:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}
