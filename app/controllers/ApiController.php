<?php

class ApiController {
    private $licenseModel;
    private $apiLogModel;

    public function __construct() {
        $this->licenseModel = new License();
        $this->apiLogModel = new ApiLog();
    }

    public function validate() {
        header('Content-Type: application/json');

        // Get API key and domain from request
        $apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';
        $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';

        if (empty($apiKey) || empty($domain)) {
            $this->sendResponse([
                'status' => 'invalid',
                'message' => 'API key dan domain harus diisi'
            ], 400);
            return;
        }

        // Get client IP
        $ipAddress = $this->getClientIP();

        // Find license by API key
        $license = $this->licenseModel->findByApiKey($apiKey);

        if (!$license) {
            $this->apiLogModel->create(null, $ipAddress, $domain, 'failed', 'API key tidak ditemukan');
            $this->sendResponse([
                'status' => 'invalid',
                'message' => 'API key tidak valid'
            ], 401);
            return;
        }

        // Check if license is active
        if ($license['status'] !== 'active') {
            $this->apiLogModel->create($license['id'], $ipAddress, $domain, 'failed', 'Lisensi tidak aktif');
            $this->sendResponse([
                'status' => 'invalid',
                'message' => 'Lisensi tidak aktif. Status: ' . $license['status']
            ], 403);
            return;
        }

        // Check if license is expired
        if ($license['expires_at'] && strtotime($license['expires_at']) < time()) {
            // Update status to expired
            $this->licenseModel->update($license['id'], ['status' => 'expired']);
            $this->apiLogModel->create($license['id'], $ipAddress, $domain, 'failed', 'Lisensi expired');
            $this->sendResponse([
                'status' => 'invalid',
                'message' => 'Lisensi sudah kadaluarsa'
            ], 403);
            return;
        }

        // Check domain match (with wildcard support)
        if (!License::isDomainMatch($license['domain'], $domain)) {
            $this->apiLogModel->create($license['id'], $ipAddress, $domain, 'failed', 'Domain tidak cocok');
            $this->sendResponse([
                'status' => 'invalid',
                'message' => 'Domain tidak sesuai dengan lisensi'
            ], 403);
            return;
        }

        // Check request limit
        if ($license['request_count'] >= $license['request_limit']) {
            $this->apiLogModel->create($license['id'], $ipAddress, $domain, 'blocked', 'Limit request tercapai');
            $this->sendResponse([
                'status' => 'blocked',
                'message' => 'Limit request API sudah tercapai',
                'request_count' => $license['request_count'],
                'request_limit' => $license['request_limit']
            ], 429);
            return;
        }

        // Increment request count
        $this->licenseModel->incrementRequestCount($license['id']);

        // Log successful validation
        $this->apiLogModel->create($license['id'], $ipAddress, $domain, 'success', 'Validasi berhasil');

        // Calculate remaining days
        $remainingDays = null;
        if ($license['expires_at']) {
            $remainingDays = ceil((strtotime($license['expires_at']) - time()) / (60 * 60 * 24));
        }

        // Send success response
        $this->sendResponse([
            'status' => 'valid',
            'message' => 'Lisensi valid',
            'data' => [
                'domain' => $license['domain'],
                'expires_at' => $license['expires_at'],
                'remaining_days' => $remainingDays,
                'request_count' => $license['request_count'] + 1,
                'request_limit' => $license['request_limit'],
                'remaining_requests' => $license['request_limit'] - ($license['request_count'] + 1)
            ]
        ], 200);
    }

    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    private function getClientIP() {
        // Get IP address with sanitization
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Only trust proxy headers if behind a known proxy (configure as needed)
        // For security, we primarily use REMOTE_ADDR which cannot be spoofed
        // Uncomment and configure the following lines if behind a trusted proxy:
        /*
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = trim($ipList[0]); // Use first IP in chain
        }
        */
        
        return filter_var($ipAddress, FILTER_VALIDATE_IP) ? $ipAddress : '0.0.0.0';
    }
}
