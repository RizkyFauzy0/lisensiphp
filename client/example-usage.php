<?php
/**
 * Example Usage of LicenseValidator
 * 
 * This file demonstrates various ways to use the LicenseValidator class
 * in your application.
 */

require_once 'license-client.php';

// =============================================================================
// CONFIGURATION
// =============================================================================

// Replace these with your actual values
define('LICENSE_SERVER_URL', 'http://license.local');  // Your license server URL
define('YOUR_API_KEY', 'YOUR_API_KEY_HERE');           // Your API key from dashboard

// =============================================================================
// EXAMPLE 1: Basic Usage - Die with Beautiful Error Page (Default)
// =============================================================================

/*
// This is the simplest way to validate license
// If validation fails, it will display a beautiful error page and stop execution

$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
$license->validate(); // Uses 'die' mode by default

// If we reach here, license is valid - continue with your application
echo "✓ License is valid! Application running normally.<br>";
*/

// =============================================================================
// EXAMPLE 2: Silent Mode - Return Boolean
// =============================================================================

/*
// Use this when you want to handle the validation result yourself

$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
$isValid = $license->validate('silent');

if ($isValid) {
    echo "✓ License is valid!<br>";
    // Continue with your application
} else {
    echo "✗ License is invalid!<br>";
    // Show your custom error message or redirect
    header('Location: /custom-error-page.php');
    exit;
}
*/

// =============================================================================
// EXAMPLE 3: JSON Mode - Get Full Response Data
// =============================================================================

/*
// Use this when you need detailed information about the license

$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
$result = $license->validate('json');

if ($result['status'] === 'valid') {
    echo "✓ License is valid!<br>";
    echo "Domain: " . $result['data']['domain'] . "<br>";
    echo "Expires: " . ($result['data']['expires_at'] ?? 'Never') . "<br>";
    echo "Remaining requests: " . $result['data']['remaining_requests'] . "<br>";
    
    // Check if license is expiring soon
    if (isset($result['data']['remaining_days']) && $result['data']['remaining_days'] <= 7) {
        echo "<div style='background:#fff3cd;padding:10px;margin:10px 0;border-radius:5px;'>";
        echo "⚠️ Warning: Your license expires in " . $result['data']['remaining_days'] . " days!";
        echo "</div>";
    }
} else {
    echo "✗ License validation failed: " . $result['message'] . "<br>";
}
*/

// =============================================================================
// EXAMPLE 4: Redirect Mode - Redirect to Purchase Page
// =============================================================================

/*
// Use this to redirect users to a purchase/renewal page if license is invalid

$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
$license->validate('redirect', 'https://your-site.com/buy-license');

// If we reach here, license is valid
echo "✓ License is valid!<br>";
*/

// =============================================================================
// EXAMPLE 5: Check License in Middleware/Bootstrap
// =============================================================================

/*
// Place this in your application bootstrap file (e.g., index.php, config.php)
// This ensures license is checked before any page loads

// At the top of your index.php or bootstrap file:
require_once __DIR__ . '/license-client.php';

$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
$license->validate('die');

// Rest of your application continues here...
*/

// =============================================================================
// EXAMPLE 6: Check Only on Specific Routes/Pages
// =============================================================================

/*
// Validate license only on admin or premium features

$requestUri = $_SERVER['REQUEST_URI'];

// Only validate on admin pages
if (strpos($requestUri, '/admin') === 0) {
    $license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
    $license->validate('die');
}

// Or only validate on premium features
$premiumPages = ['/premium', '/advanced', '/analytics'];
foreach ($premiumPages as $page) {
    if (strpos($requestUri, $page) === 0) {
        $license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
        $license->validate('die');
        break;
    }
}
*/

// =============================================================================
// EXAMPLE 7: Custom Domain Override
// =============================================================================

/*
// By default, the validator detects the current domain automatically
// But you can override it if needed

$customDomain = 'example.com';
$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY, $customDomain);
$license->validate('die');
*/

// =============================================================================
// EXAMPLE 8: Show License Info Dashboard
// =============================================================================

/*
// Display license information in your admin dashboard

$license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
$result = $license->validate('json');

if ($result['status'] === 'valid') {
    echo '<div style="background:#f0f9ff;border:1px solid #0ea5e9;padding:20px;border-radius:8px;margin:20px 0;">';
    echo '<h3 style="margin:0 0 15px 0;color:#0369a1;">📋 License Information</h3>';
    echo '<table style="width:100%;border-collapse:collapse;">';
    
    $info = [
        'Domain' => $result['data']['domain'],
        'Status' => '<span style="color:#16a34a;font-weight:bold;">✓ Active</span>',
        'Expires' => $result['data']['expires_at'] ?? 'Lifetime',
        'Request Usage' => $result['data']['request_count'] . ' / ' . $result['data']['request_limit'],
        'Remaining Requests' => number_format($result['data']['remaining_requests'])
    ];
    
    if (isset($result['data']['remaining_days'])) {
        $info['Days Remaining'] = $result['data']['remaining_days'] . ' days';
    }
    
    foreach ($info as $label => $value) {
        echo '<tr style="border-bottom:1px solid #e0f2fe;">';
        echo '<td style="padding:8px 0;font-weight:600;color:#0369a1;">' . $label . '</td>';
        echo '<td style="padding:8px 0;text-align:right;">' . $value . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</div>';
}
*/

// =============================================================================
// EXAMPLE 9: Caching License Validation (Recommended for Production)
// =============================================================================

/*
// To reduce API calls, cache the validation result

function validateLicenseWithCache() {
    $cacheFile = sys_get_temp_dir() . '/license_cache_' . md5(YOUR_API_KEY);
    $cacheDuration = 3600; // 1 hour
    
    // Check cache
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if ($cacheData && (time() - $cacheData['timestamp']) < $cacheDuration) {
            return $cacheData['result'];
        }
    }
    
    // Validate license
    $license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
    $result = $license->validate('json');
    
    // Cache result if valid
    if ($result['status'] === 'valid') {
        file_put_contents($cacheFile, json_encode([
            'timestamp' => time(),
            'result' => $result
        ]));
    }
    
    return $result;
}

$result = validateLicenseWithCache();

if ($result['status'] !== 'valid') {
    die('License is invalid: ' . $result['message']);
}

echo "✓ License is valid (from cache or fresh validation)<br>";
*/

// =============================================================================
// EXAMPLE 10: Error Handling with Try-Catch
// =============================================================================

/*
// Wrap validation in try-catch for production environments

try {
    $license = new LicenseValidator(LICENSE_SERVER_URL, YOUR_API_KEY);
    $result = $license->validate('json');
    
    if ($result['status'] === 'valid') {
        echo "✓ License validated successfully<br>";
    } else {
        // Log error
        error_log('License validation failed: ' . $result['message']);
        
        // Show generic error to user
        echo "License validation failed. Please contact support.";
    }
} catch (Exception $e) {
    error_log('License validation exception: ' . $e->getMessage());
    echo "An error occurred during license validation.";
}
*/

// =============================================================================
// QUICK START EXAMPLE
// =============================================================================

echo "<!DOCTYPE html>
<html>
<head>
    <title>License Validator - Example Usage</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .example {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        h2 {
            color: #666;
            font-size: 18px;
            margin-top: 0;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <h1>🔐 License Validator - Usage Examples</h1>
    
    <div class='note'>
        <strong>⚠️ Important:</strong> Update the configuration values at the top of this file with your actual license server URL and API key.
    </div>
    
    <div class='example'>
        <h2>Quick Start</h2>
        <p>The simplest way to validate your license:</p>
        <pre>require_once 'license-client.php';

\$license = new LicenseValidator(
    'https://your-license-server.com',
    'YOUR_API_KEY_HERE'
);

\$license->validate(); // Dies with beautiful error page if invalid

// Your application continues here if valid
echo 'Application running!';</pre>
    </div>
    
    <div class='example'>
        <h2>Available Modes</h2>
        <ul>
            <li><code>die</code> (default) - Shows beautiful error page and stops execution</li>
            <li><code>silent</code> - Returns boolean (true/false)</li>
            <li><code>json</code> - Returns full response array with details</li>
            <li><code>redirect</code> - Redirects to specified URL if invalid</li>
        </ul>
    </div>
    
    <div class='example'>
        <h2>Error States Handled</h2>
        <ul>
            <li>✓ License Not Found - Invalid API key</li>
            <li>✓ Domain Mismatch - Domain doesn't match license</li>
            <li>✓ License Expired - License has expired</li>
            <li>✓ License Suspended - License suspended by admin</li>
            <li>✓ Request Limit Exceeded - API request limit reached</li>
            <li>✓ Server Error - Cannot connect to license server</li>
        </ul>
    </div>
    
    <div class='example'>
        <h2>Recommended Setup</h2>
        <p>Place this code in your application's bootstrap file (e.g., <code>config.php</code> or <code>index.php</code>):</p>
        <pre>// config.php or index.php
require_once __DIR__ . '/license-client.php';

\$license = new LicenseValidator(
    'https://your-license-server.com',
    'YOUR_API_KEY_HERE'
);

// Validate and show error page if invalid
\$license->validate('die');

// Application continues normally if valid...</pre>
    </div>
    
    <p style='text-align:center;color:#666;margin-top:40px;'>
        See the commented examples in this file for more advanced usage scenarios.
    </p>
</body>
</html>";
