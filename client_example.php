<?php
/**
 * License Validation Client Example
 * 
 * This file demonstrates how to integrate the license validation
 * in your client application.
 */

// Configuration
define('LICENSE_SERVER', 'http://license.local'); // Change to your license server URL
define('API_KEY', 'YOUR_API_KEY_HERE'); // Replace with your actual API key
define('CURRENT_DOMAIN', $_SERVER['HTTP_HOST']); // Current domain

/**
 * Validate license with the license server
 * 
 * @return array Response from license server
 */
function validateLicense() {
    $url = LICENSE_SERVER . "/api/validate";
    
    $params = http_build_query([
        'api_key' => API_KEY,
        'domain' => CURRENT_DOMAIN
    ]);
    
    // Using file_get_contents
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $params,
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return [
            'status' => 'error',
            'message' => 'Cannot connect to license server'
        ];
    }
    
    return json_decode($response, true);
}

/**
 * Validate license using cURL (alternative method)
 * 
 * @return array Response from license server
 */
function validateLicenseCurl() {
    $url = LICENSE_SERVER . "/api/validate";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'api_key' => API_KEY,
        'domain' => CURRENT_DOMAIN
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        return [
            'status' => 'error',
            'message' => 'Cannot connect to license server: ' . $error
        ];
    }
    
    return json_decode($response, true);
}

/**
 * Cache license validation result
 * 
 * @param int $duration Cache duration in seconds (default: 1 hour)
 * @return array License validation result
 */
function validateLicenseWithCache($duration = 3600) {
    $cacheFile = sys_get_temp_dir() . '/license_cache_' . md5(API_KEY);
    
    // Check cache
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if ($cacheData && (time() - $cacheData['timestamp']) < $duration) {
            return $cacheData['result'];
        }
    }
    
    // Validate license
    $result = validateLicense();
    
    // Cache result if valid
    if ($result['status'] === 'valid') {
        file_put_contents($cacheFile, json_encode([
            'timestamp' => time(),
            'result' => $result
        ]));
    }
    
    return $result;
}

// =================================
// USAGE EXAMPLES
// =================================

// Example 1: Basic validation
$result = validateLicense();

if ($result['status'] !== 'valid') {
    // License is invalid
    header('HTTP/1.1 403 Forbidden');
    die('
        <html>
        <head><title>License Error</title></head>
        <body style="font-family: Arial; text-align: center; padding: 50px;">
            <h1>License Validation Failed</h1>
            <p>' . htmlspecialchars($result['message']) . '</p>
            <p>Please contact the administrator.</p>
        </body>
        </html>
    ');
}

// License is valid - continue with application
echo "License is valid! Application can run.<br>";
echo "Domain: " . htmlspecialchars($result['data']['domain']) . "<br>";
echo "Remaining requests: " . number_format($result['data']['remaining_requests']) . "<br>";

if ($result['data']['expires_at']) {
    echo "Expires at: " . htmlspecialchars($result['data']['expires_at']) . "<br>";
    echo "Remaining days: " . $result['data']['remaining_days'] . " days<br>";
}

// =================================
// Example 2: With cache (recommended for production)
// =================================

/*
$result = validateLicenseWithCache(3600); // Cache for 1 hour

if ($result['status'] !== 'valid') {
    die('License invalid: ' . $result['message']);
}

// Continue with your application
*/

// =================================
// Example 3: Using cURL
// =================================

/*
$result = validateLicenseCurl();

if ($result['status'] !== 'valid') {
    die('License invalid: ' . $result['message']);
}
*/

// =================================
// Example 4: Check on specific pages only
// =================================

/*
// Only validate on admin pages
if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) {
    $result = validateLicenseWithCache();
    if ($result['status'] !== 'valid') {
        die('License invalid');
    }
}
*/

// =================================
// Example 5: Show warning when license is expiring
// =================================

/*
$result = validateLicense();

if ($result['status'] === 'valid') {
    $remainingDays = $result['data']['remaining_days'] ?? null;
    
    if ($remainingDays && $remainingDays <= 7) {
        echo '<div style="background: #fff3cd; padding: 10px; border: 1px solid #ffc107; margin: 10px;">
                <strong>Warning:</strong> Your license will expire in ' . $remainingDays . ' days!
                Please renew your license.
              </div>';
    }
}
*/
