<?php

class DocumentationController {
    public function index() {
        AuthController::checkAuth();
        
        // Get API key from query parameter if provided (from license detail page)
        $apiKey = isset($_GET['api_key']) ? htmlspecialchars($_GET['api_key']) : 'YOUR_API_KEY';
        $domain = isset($_GET['domain']) ? htmlspecialchars($_GET['domain']) : 'yourdomain.com';
        
        require_once APP_PATH . '/views/documentation/index.php';
    }
}
