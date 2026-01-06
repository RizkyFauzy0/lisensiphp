<?php

class DashboardController {
    private $licenseModel;
    private $apiLogModel;

    public function __construct() {
        $this->licenseModel = new License();
        $this->apiLogModel = new ApiLog();
    }

    public function index() {
        AuthController::checkAuth();

        // Update expired licenses
        $this->licenseModel->updateExpiredLicenses();

        // Get statistics
        $stats = $this->licenseModel->getStatistics();
        
        // Get recent activities
        $recentActivities = $this->apiLogModel->getRecent(10);
        
        // Get expiring soon licenses
        $expiringSoon = $this->licenseModel->getExpiringSoon(7);
        
        // Get chart data for last 30 days
        $chartData = $this->apiLogModel->getOverallStats(30);

        require_once APP_PATH . '/views/dashboard/index.php';
    }
}
