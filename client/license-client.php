<?php
/**
 * LicenseValidator - Professional License Validation Client
 * 
 * A modern, OOP-based license validation client with beautiful error pages.
 * Easy to integrate into any PHP application.
 * 
 * @version 1.0.0
 * @author License Management System
 */

class LicenseValidator {
    private $apiUrl;
    private $apiKey;
    private $domain;
    private $timeout = 10;
    private $supportEmail = 'support@example.com';
    
    /**
     * Initialize the License Validator
     * 
     * @param string $apiUrl The URL of your license server (e.g., https://license.example.com)
     * @param string $apiKey Your API key from the license dashboard
     * @param string|null $domain Optional: Override the detected domain
     */
    public function __construct($apiUrl, $apiKey, $domain = null) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->domain = $domain ?: ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'unknown');
    }
    
    /**
     * Set custom support email for error pages
     * 
     * @param string $email Support email address
     * @return self
     */
    public function setSupportEmail($email) {
        $this->supportEmail = $email;
        return $this;
    }
    
    /**
     * Validate the license
     * 
     * @param string $mode Display mode: 'die', 'redirect', 'silent', or 'json'
     * @param string|null $redirectUrl Required if mode is 'redirect'
     * @return bool|array Returns boolean in silent mode, array in json mode, exits in die/redirect modes
     */
    public function validate($mode = 'die', $redirectUrl = null) {
        $response = $this->callApi();
        
        if ($response['status'] === 'valid') {
            return $this->handleValid($response, $mode);
        }
        
        return $this->handleInvalid($response, $mode, $redirectUrl);
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
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            return [
                'status' => 'error',
                'message' => 'Cannot connect to license server',
                'details' => $error
            ];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid response from license server',
                'details' => 'HTTP ' . $httpCode
            ];
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
                'message' => 'Cannot connect to license server',
                'details' => 'Connection failed'
            ];
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'status' => 'error',
                'message' => 'Invalid response from license server',
                'details' => 'JSON parse error'
            ];
        }
        
        return $data;
    }
    
    /**
     * Handle valid license
     */
    private function handleValid($response, $mode) {
        if ($mode === 'silent') {
            return true;
        }
        
        if ($mode === 'json') {
            return $response;
        }
        
        // For 'die' and 'redirect' modes, just return true
        return true;
    }
    
    /**
     * Handle invalid license
     */
    private function handleInvalid($response, $mode, $redirectUrl) {
        if ($mode === 'silent') {
            return false;
        }
        
        if ($mode === 'json') {
            return $response;
        }
        
        if ($mode === 'redirect' && $redirectUrl) {
            header('Location: ' . $redirectUrl);
            exit;
        }
        
        // Default: die with beautiful error page
        $this->renderErrorPage($response);
        exit;
    }
    
    /**
     * Render a beautiful error page
     */
    private function renderErrorPage($response) {
        $status = $response['status'] ?? 'error';
        $message = $response['message'] ?? 'License validation failed';
        $data = $response['data'] ?? [];
        
        // Determine error type and styling
        $errorConfig = $this->getErrorConfig($status, $message);
        
        http_response_code($errorConfig['httpCode']);
        
        $html = $this->generateErrorHtml(
            $errorConfig['icon'],
            $errorConfig['title'],
            $message,
            $errorConfig['gradient'],
            $data,
            $response
        );
        
        echo $html;
    }
    
    /**
     * Get error configuration based on status
     */
    private function getErrorConfig($status, $message) {
        $configs = [
            'invalid' => [
                'icon' => $this->getIconSvg('exclamation'),
                'title' => 'License Invalid',
                'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'httpCode' => 403
            ],
            'blocked' => [
                'icon' => $this->getIconSvg('ban'),
                'title' => 'Request Limit Exceeded',
                'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'httpCode' => 429
            ],
            'error' => [
                'icon' => $this->getIconSvg('alert'),
                'title' => 'Connection Error',
                'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'httpCode' => 503
            ]
        ];
        
        // Check for specific error types in message
        if (stripos($message, 'expired') !== false || stripos($message, 'kadaluarsa') !== false) {
            return [
                'icon' => $this->getIconSvg('clock'),
                'title' => 'License Expired',
                'gradient' => 'linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%)',
                'httpCode' => 403
            ];
        }
        
        if (stripos($message, 'domain') !== false) {
            return [
                'icon' => $this->getIconSvg('globe'),
                'title' => 'Domain Mismatch',
                'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'httpCode' => 403
            ];
        }
        
        if (stripos($message, 'suspended') !== false) {
            return [
                'icon' => $this->getIconSvg('pause'),
                'title' => 'License Suspended',
                'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'httpCode' => 403
            ];
        }
        
        return $configs[$status] ?? $configs['invalid'];
    }
    
    /**
     * Get SVG icon based on type
     */
    private function getIconSvg($type) {
        $icons = [
            'exclamation' => '<svg class="w-24 h-24 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            'ban' => '<svg class="w-24 h-24 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>',
            'clock' => '<svg class="w-24 h-24 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'globe' => '<svg class="w-24 h-24 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'pause' => '<svg class="w-24 h-24 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'alert' => '<svg class="w-24 h-24 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ];
        
        return $icons[$type] ?? $icons['exclamation'];
    }
    
    /**
     * Generate the complete HTML for error page
     */
    private function generateErrorHtml($icon, $title, $message, $gradient, $data, $fullResponse) {
        $detailsHtml = $this->generateDetailsHtml($data, $fullResponse);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: {$gradient};
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-header {
            padding: 40px 40px 20px;
            text-align: center;
            color: white;
            background: {$gradient};
        }
        .error-icon {
            color: white;
            opacity: 0.9;
        }
        .error-body {
            padding: 40px;
        }
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            text-align: center;
        }
        .error-message {
            font-size: 16px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .details-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #4b5563;
            font-size: 14px;
        }
        .detail-value {
            color: #1f2937;
            font-size: 14px;
            font-weight: 500;
        }
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            text-align: center;
        }
        .btn-primary {
            background: {$gradient};
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.15);
        }
        .btn-secondary {
            background: white;
            color: #4b5563;
            border: 2px solid #e5e7eb;
        }
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background: #f9fafb;
            color: #9ca3af;
            font-size: 13px;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
        @media (max-width: 640px) {
            .error-container {
                border-radius: 12px;
            }
            .error-header {
                padding: 30px 20px 15px;
            }
            .error-body {
                padding: 30px 20px;
            }
            h1 {
                font-size: 24px;
            }
            .error-message {
                font-size: 14px;
            }
            .detail-item {
                flex-direction: column;
                gap: 4px;
            }
            .action-buttons {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <div class="error-icon">
                {$icon}
            </div>
        </div>
        <div class="error-body">
            <h1>{$title}</h1>
            <p class="error-message">{$message}</p>
            
            {$detailsHtml}
            
            <div class="action-buttons">
                <a href="mailto:{$this->supportEmail}" class="btn btn-primary">Contact Support</a>
                <button onclick="window.location.reload()" class="btn btn-secondary">Retry</button>
            </div>
        </div>
        <div class="footer">
            Powered by <a href="#">License Management System</a>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Generate details HTML section
     */
    private function generateDetailsHtml($data, $fullResponse) {
        $details = [];
        
        // Add domain
        $details['Domain'] = htmlspecialchars($this->domain);
        
        // Add status
        if (isset($fullResponse['status'])) {
            $statusLabels = [
                'invalid' => 'Invalid',
                'blocked' => 'Blocked',
                'error' => 'Error'
            ];
            $details['Status'] = $statusLabels[$fullResponse['status']] ?? ucfirst($fullResponse['status']);
        }
        
        // Add expiry date if available
        if (isset($data['expires_at']) && $data['expires_at']) {
            $details['Expires At'] = htmlspecialchars($data['expires_at']);
        }
        
        // Add remaining days if available
        if (isset($data['remaining_days']) && $data['remaining_days'] !== null) {
            $details['Remaining Days'] = $data['remaining_days'] . ' days';
        }
        
        // Add request info if blocked
        if (isset($fullResponse['request_count']) && isset($fullResponse['request_limit'])) {
            $details['Request Count'] = number_format($fullResponse['request_count']) . ' / ' . number_format($fullResponse['request_limit']);
        } elseif (isset($data['request_count']) && isset($data['request_limit'])) {
            $details['Request Usage'] = number_format($data['request_count']) . ' / ' . number_format($data['request_limit']);
        }
        
        // Add remaining requests if available
        if (isset($data['remaining_requests'])) {
            $details['Remaining Requests'] = number_format($data['remaining_requests']);
        }
        
        // Add error details if available
        if (isset($fullResponse['details'])) {
            $details['Error Details'] = htmlspecialchars($fullResponse['details']);
        }
        
        if (empty($details)) {
            return '';
        }
        
        $html = '<div class="details-card">';
        foreach ($details as $label => $value) {
            $html .= '<div class="detail-item">';
            $html .= '<span class="detail-label">' . htmlspecialchars($label) . '</span>';
            $html .= '<span class="detail-value">' . $value . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        return $html;
    }
}
