<?php
// API endpoint for license validation
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Database.php';
require_once __DIR__ . '/../app/models/License.php';
require_once __DIR__ . '/../app/models/ApiLog.php';
require_once __DIR__ . '/../app/controllers/ApiController.php';

// Handle the request
$controller = new ApiController();

// Get the action from the URL
$action = $_GET['action'] ?? 'validate';

switch ($action) {
    case 'validate':
        $controller->validate();
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Invalid API endpoint']);
        break;
}
