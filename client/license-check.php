<?php
/**
 * LicenseChecker - Professional License Validation Client
 * 
 * Modern OOP-based license validation with beautiful Tailwind CSS error pages.
 * Includes session caching, multiple error states, and easy integration.
 * 
 * @version 2.0.0
 * @author License Management System
 * @license MIT
 */

class LicenseChecker {
    private $apiUrl;
    private $apiKey;
    private $domain;
    private $cacheDuration = 3600; // 1 hour default
    private $timeout = 10;
    private $supportUrl = 'mailto:support@example.com';
    private $purchaseUrl = 'https://example.com/pricing';
    
    /**
     * Initialize the License Checker
     * 
     * @param string $apiUrl The URL of your license server (e.g., https://license.example.com)
     * @param string $apiKey Your API key from the license dashboard
     * @param array $options Optional configuration:
     *                       - cache_duration: Cache validity in seconds (default: 3600)
     *                       - support_url: URL for support button (default: mailto:support@example.com)
     *                       - purchase_url: URL for purchase button (default: https://example.com/pricing)
     *                       - domain: Override auto-detected domain
     */
    public function __construct($apiUrl, $apiKey, $options = []) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        
        // Set domain
        $this->domain = $options['domain'] ?? ($_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'unknown');
        
        // Set cache duration
        if (isset($options['cache_duration'])) {
            $this->cacheDuration = (int)$options['cache_duration'];
        }
        
        // Set support URL
        if (isset($options['support_url'])) {
            $this->supportUrl = $options['support_url'];
        }
        
        // Set purchase URL
        if (isset($options['purchase_url'])) {
            $this->purchaseUrl = $options['purchase_url'];
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Validate the license and die with error page if invalid
     * 
     * @return bool Returns true if valid (otherwise dies with error page)
     */
    public function validate() {
        $result = $this->check();
        
        if ($result['status'] === 'valid') {
            return true;
        }
        
        // Show error page and die
        $this->renderErrorPage($result);
        exit;
    }
    
    /**
     * Check license without dying - returns full response
     * 
     * @return array Response array with status, message, and data
     */
    public function check() {
        // Check cache first
        $cached = $this->getCached();
        if ($cached !== null) {
            return $cached;
        }
        
        // Call API
        $response = $this->callApi();
        
        // Cache valid responses
        if ($response['status'] === 'valid') {
            $this->setCache($response);
        }
        
        return $response;
    }
    
    /**
     * Simple boolean check - returns true if valid, false otherwise
     * 
     * @return bool True if license is valid, false otherwise
     */
    public function isValid() {
        $result = $this->check();
        return $result['status'] === 'valid';
    }
    
    /**
     * Call the license validation API
     * 
     * @return array API response
     */
    private function callApi() {
        $url = $this->apiUrl . '/api/validate';
        $params = http_build_query([
            'api_key' => $this->apiKey,
            'domain' => $this->domain
        ]);
        
        // Try cURL first (more reliable)
        if (function_exists('curl_init')) {
            return $this->callApiCurl($url, $params);
        }
        
        // Fallback to file_get_contents
        return $this->callApiFileGetContents($url, $params);
    }
    
    /**
     * Call API using cURL
     */
    private function callApiCurl($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            return [
                'status' => 'error',
                'error_type' => 'connection_error',
                'message' => 'Tidak dapat terhubung ke server lisensi',
                'details' => $error
            ];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'error_type' => 'connection_error',
                'message' => 'Respon server tidak valid',
                'details' => 'HTTP ' . $httpCode
            ];
        }
        
        // Add error type based on status and message
        if ($data['status'] !== 'valid') {
            $data['error_type'] = $this->detectErrorType($data);
        }
        
        return $data;
    }
    
    /**
     * Call API using file_get_contents
     */
    private function callApiFileGetContents($url, $params) {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $params,
                'timeout' => $this->timeout,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return [
                'status' => 'error',
                'error_type' => 'connection_error',
                'message' => 'Tidak dapat terhubung ke server lisensi',
                'details' => 'Connection failed'
            ];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'error_type' => 'connection_error',
                'message' => 'Respon server tidak valid',
                'details' => 'JSON parse error'
            ];
        }
        
        // Add error type based on status and message
        if ($data['status'] !== 'valid') {
            $data['error_type'] = $this->detectErrorType($data);
        }
        
        return $data;
    }
    
    /**
     * Detect error type from API response
     */
    private function detectErrorType($response) {
        $message = strtolower($response['message'] ?? '');
        $status = $response['status'] ?? '';
        
        // Check for specific error patterns
        if ($status === 'blocked' || stripos($message, 'limit') !== false || stripos($message, 'exceeded') !== false) {
            return 'limit_exceeded';
        }
        
        if (stripos($message, 'expired') !== false || stripos($message, 'kadaluarsa') !== false) {
            return 'license_expired';
        }
        
        if (stripos($message, 'suspended') !== false || stripos($message, 'suspend') !== false) {
            return 'license_suspended';
        }
        
        if (stripos($message, 'domain') !== false || stripos($message, 'tidak sesuai') !== false) {
            return 'domain_mismatch';
        }
        
        if (stripos($message, 'tidak ditemukan') !== false || stripos($message, 'not found') !== false || stripos($message, 'tidak valid') !== false) {
            return 'license_not_found';
        }
        
        if ($status === 'error') {
            return 'connection_error';
        }
        
        return 'license_not_found';
    }
    
    /**
     * Get cached validation result
     */
    private function getCached() {
        $cacheKey = 'license_check_' . md5($this->apiKey . $this->domain);
        
        if (isset($_SESSION[$cacheKey])) {
            $cache = $_SESSION[$cacheKey];
            
            // Check if cache is still valid
            if (isset($cache['timestamp']) && (time() - $cache['timestamp']) < $this->cacheDuration) {
                return $cache['data'];
            }
            
            // Cache expired, remove it
            unset($_SESSION[$cacheKey]);
        }
        
        return null;
    }
    
    /**
     * Set cache for validation result
     */
    private function setCache($data) {
        $cacheKey = 'license_check_' . md5($this->apiKey . $this->domain);
        
        $_SESSION[$cacheKey] = [
            'timestamp' => time(),
            'data' => $data
        ];
    }
    
    /**
     * Render professional error page with Tailwind CSS
     */
    private function renderErrorPage($response) {
        $errorType = $response['error_type'] ?? 'license_not_found';
        $errorConfig = $this->getErrorConfig($errorType);
        
        http_response_code($errorConfig['httpCode']);
        
        $html = $this->generateErrorHtml($errorConfig, $response);
        
        echo $html;
    }
    
    /**
     * Get error configuration (colors, icons, titles) for each error type
     */
    private function getErrorConfig($errorType) {
        $configs = [
            'license_not_found' => [
                'title' => 'Lisensi Tidak Ditemukan',
                'gradient' => 'from-red-500 to-rose-600',
                'iconColor' => 'red',
                'icon' => 'exclamation',
                'httpCode' => 403
            ],
            'domain_mismatch' => [
                'title' => 'Domain Tidak Sesuai',
                'gradient' => 'from-orange-500 to-amber-600',
                'iconColor' => 'orange',
                'icon' => 'globe',
                'httpCode' => 403
            ],
            'license_expired' => [
                'title' => 'Lisensi Kadaluarsa',
                'gradient' => 'from-yellow-500 to-orange-500',
                'iconColor' => 'yellow',
                'icon' => 'clock',
                'httpCode' => 403
            ],
            'license_suspended' => [
                'title' => 'Lisensi Di-Suspend',
                'gradient' => 'from-red-600 to-red-800',
                'iconColor' => 'red',
                'icon' => 'pause',
                'httpCode' => 403
            ],
            'limit_exceeded' => [
                'title' => 'Batas Request Tercapai',
                'gradient' => 'from-blue-500 to-indigo-600',
                'iconColor' => 'blue',
                'icon' => 'ban',
                'httpCode' => 429
            ],
            'connection_error' => [
                'title' => 'Kesalahan Koneksi',
                'gradient' => 'from-gray-500 to-gray-700',
                'iconColor' => 'gray',
                'icon' => 'alert',
                'httpCode' => 503
            ]
        ];
        
        return $configs[$errorType] ?? $configs['license_not_found'];
    }
    
    /**
     * Get SVG icon based on type
     */
    private function getIconSvg($type, $color) {
        $icons = [
            'exclamation' => '<svg class="w-16 h-16 text-' . $color . '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            'ban' => '<svg class="w-16 h-16 text-' . $color . '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>',
            'clock' => '<svg class="w-16 h-16 text-' . $color . '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'globe' => '<svg class="w-16 h-16 text-' . $color . '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'pause' => '<svg class="w-16 h-16 text-' . $color . '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'alert' => '<svg class="w-16 h-16 text-' . $color . '-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ];
        
        return $icons[$type] ?? $icons['exclamation'];
    }
    
    /**
     * Generate the complete HTML for error page with Tailwind CSS
     */
    private function generateErrorHtml($errorConfig, $response) {
        $title = htmlspecialchars($errorConfig['title'], ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars($response['message'] ?? 'Validasi lisensi gagal', ENT_QUOTES, 'UTF-8');
        $gradient = htmlspecialchars($errorConfig['gradient'], ENT_QUOTES, 'UTF-8');
        $iconColor = htmlspecialchars($errorConfig['iconColor'], ENT_QUOTES, 'UTF-8');
        $icon = $this->getIconSvg($errorConfig['icon'], $iconColor);
        
        $detailsHtml = $this->generateDetailsHtml($response, $iconColor);
        $buttonsHtml = $this->generateButtonsHtml($iconColor);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br {$gradient} flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-lg w-full">
        <!-- Icon -->
        <div class="w-24 h-24 bg-{$iconColor}-100 rounded-full flex items-center justify-center mx-auto mb-6">
            {$icon}
        </div>
        
        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">{$title}</h1>
        
        <!-- Message -->
        <p class="text-gray-600 text-center mb-6">{$message}</p>
        
        <!-- Details Card -->
        {$detailsHtml}
        
        <!-- Action Buttons -->
        {$buttonsHtml}
        
        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-6">
            Powered by <a href="#" class="text-{$iconColor}-500 hover:underline">License System</a>
        </p>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Generate details card HTML
     */
    private function generateDetailsHtml($response, $color) {
        $details = [];
        
        // Add domain
        $details['Domain'] = htmlspecialchars($this->domain, ENT_QUOTES, 'UTF-8');
        
        // Add status
        $statusLabels = [
            'invalid' => 'Invalid',
            'blocked' => 'Terblokir',
            'error' => 'Error'
        ];
        $statusText = $statusLabels[$response['status'] ?? 'invalid'] ?? 'Tidak Valid';
        $details['Status'] = '<span class="font-semibold text-' . $color . '-600">' . htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') . '</span>';
        
        // Add expiry date if available
        $data = $response['data'] ?? [];
        if (isset($data['expires_at']) && $data['expires_at']) {
            $details['Tanggal Kadaluarsa'] = htmlspecialchars($data['expires_at'], ENT_QUOTES, 'UTF-8');
        }
        
        // Add remaining days if available
        if (isset($data['remaining_days']) && $data['remaining_days'] !== null) {
            $days = is_numeric($data['remaining_days']) ? intval($data['remaining_days']) : 0;
            $details['Sisa Hari'] = $days . ' hari';
        }
        
        // Add request info
        if (isset($response['request_count']) && isset($response['request_limit'])) {
            $reqCount = is_numeric($response['request_count']) ? intval($response['request_count']) : 0;
            $reqLimit = is_numeric($response['request_limit']) ? intval($response['request_limit']) : 0;
            $details['Penggunaan Request'] = number_format($reqCount) . ' / ' . number_format($reqLimit);
        } elseif (isset($data['request_count']) && isset($data['request_limit'])) {
            $reqCount = is_numeric($data['request_count']) ? intval($data['request_count']) : 0;
            $reqLimit = is_numeric($data['request_limit']) ? intval($data['request_limit']) : 0;
            $details['Penggunaan Request'] = number_format($reqCount) . ' / ' . number_format($reqLimit);
        }
        
        // Add remaining requests if available
        if (isset($data['remaining_requests'])) {
            $remaining = is_numeric($data['remaining_requests']) ? intval($data['remaining_requests']) : 0;
            $details['Sisa Request'] = number_format($remaining);
        }
        
        // Add error details if available
        if (isset($response['details'])) {
            $details['Detail Error'] = htmlspecialchars($response['details'], ENT_QUOTES, 'UTF-8');
        }
        
        if (empty($details)) {
            return '';
        }
        
        $html = '<div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2">';
        foreach ($details as $label => $value) {
            $html .= '<div class="flex justify-between">';
            $html .= '<span class="text-gray-500">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
            $html .= '<span class="font-mono text-gray-700">' . $value . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Generate action buttons HTML
     */
    private function generateButtonsHtml($color) {
        $supportUrl = htmlspecialchars($this->supportUrl, ENT_QUOTES, 'UTF-8');
        $purchaseUrl = htmlspecialchars($this->purchaseUrl, ENT_QUOTES, 'UTF-8');
        
        return <<<HTML
<div class="flex flex-col sm:flex-row gap-3">
    <a href="{$supportUrl}" class="flex-1 bg-{$color}-500 hover:bg-{$color}-600 text-white font-semibold py-3 px-6 rounded-xl text-center transition">
        Hubungi Support
    </a>
    <a href="{$purchaseUrl}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl text-center transition">
        Beli Lisensi
    </a>
</div>
HTML;
    }
}

/**
 * Helper function for quick license validation
 * 
 * @param string $apiUrl License server URL
 * @param string $apiKey Your API key
 * @param array $options Optional configuration (see LicenseChecker constructor)
 * @return bool Returns true if valid (otherwise dies with error page)
 */
function checkLicense($apiUrl, $apiKey, $options = []) {
    $checker = new LicenseChecker($apiUrl, $apiKey, $options);
    return $checker->validate();
}
