# License Client Integration Guide

Professional PHP client library for integrating license validation into your applications with beautiful error pages and multiple display modes.

## 📦 Features

- **OOP Design** - Clean, modern PHP class-based implementation
- **Beautiful Error Pages** - Professional styled error pages with inline CSS
- **Multiple Display Modes** - Choose how to handle invalid licenses
- **Auto Domain Detection** - Automatically detects the current domain
- **Comprehensive Error Handling** - Handles all error states gracefully
- **Zero Dependencies** - Works with pure PHP (no external libraries needed)
- **Fully Responsive** - Error pages look great on all devices
- **Production Ready** - Includes caching examples and best practices

## 🚀 Quick Start

### 1. Download the Client File

Copy the `license-client.php` file to your application directory:

```bash
# Copy to your project root or includes directory
cp client/license-client.php /path/to/your/project/
```

### 2. Get Your API Key

1. Login to your license management dashboard
2. Navigate to **Licenses** section
3. Create a new license or view existing one
4. Copy the **API Key**

### 3. Basic Integration

Add this to your application's main file (e.g., `index.php`, `config.php`, or bootstrap file):

```php
<?php
require_once 'license-client.php';

$license = new LicenseValidator(
    'https://your-license-server.com',  // Your license server URL
    'YOUR_API_KEY_HERE'                  // Your API key
);

// Validate license - shows error page if invalid
$license->validate();

// Your application continues here if valid
echo "Application running!";
?>
```

That's it! Your application is now protected.

## 📖 Usage Guide

### Display Modes

The `validate()` method accepts different modes:

#### 1. Die Mode (Default)
Shows beautiful error page and stops execution:

```php
$license->validate(); // or explicitly: $license->validate('die');
```

#### 2. Silent Mode
Returns boolean, no output:

```php
$isValid = $license->validate('silent');

if ($isValid) {
    // Continue with application
} else {
    // Handle invalid license
    echo "License invalid!";
}
```

#### 3. JSON Mode
Returns full response array:

```php
$result = $license->validate('json');

if ($result['status'] === 'valid') {
    echo "License valid!";
    echo "Expires: " . $result['data']['expires_at'];
    echo "Remaining requests: " . $result['data']['remaining_requests'];
} else {
    echo "Error: " . $result['message'];
}
```

#### 4. Redirect Mode
Redirects to URL if invalid:

```php
$license->validate('redirect', 'https://your-site.com/buy-license');
```

### Customization Options

#### Set Custom Support Email

By default, error pages show 'support@example.com'. You can customize this:

```php
$license = new LicenseValidator(
    'https://your-license-server.com',
    'YOUR_API_KEY_HERE'
);

// Set custom support email
$license->setSupportEmail('help@your-company.com');

$license->validate();
```

### Advanced Usage

#### Caching Validation Results

To reduce API calls, implement caching:

```php
function validateLicenseWithCache() {
    $cacheFile = sys_get_temp_dir() . '/license_cache_' . md5('YOUR_API_KEY');
    $cacheDuration = 3600; // 1 hour
    
    // Check cache
    if (file_exists($cacheFile)) {
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        if ($cacheData && (time() - $cacheData['timestamp']) < $cacheDuration) {
            return $cacheData['result'];
        }
    }
    
    // Validate license
    $license = new LicenseValidator(
        'https://your-license-server.com',
        'YOUR_API_KEY_HERE'
    );
    $result = $license->validate('json');
    
    // Cache if valid
    if ($result['status'] === 'valid') {
        file_put_contents($cacheFile, json_encode([
            'timestamp' => time(),
            'result' => $result
        ]));
    }
    
    return $result;
}

// Usage
$result = validateLicenseWithCache();
if ($result['status'] !== 'valid') {
    die('License invalid');
}
```

#### Validate Only on Specific Routes

```php
$requestUri = $_SERVER['REQUEST_URI'];

// Only validate on admin pages
if (strpos($requestUri, '/admin') === 0) {
    $license = new LicenseValidator(
        'https://your-license-server.com',
        'YOUR_API_KEY_HERE'
    );
    $license->validate();
}
```

#### Custom Domain Override

```php
// Override auto-detected domain
$license = new LicenseValidator(
    'https://your-license-server.com',
    'YOUR_API_KEY_HERE',
    'custom-domain.com'  // Custom domain
);
```

#### Show Expiry Warning

```php
$license = new LicenseValidator(
    'https://your-license-server.com',
    'YOUR_API_KEY_HERE'
);
$result = $license->validate('json');

if ($result['status'] === 'valid') {
    $remainingDays = $result['data']['remaining_days'] ?? null;
    
    if ($remainingDays && $remainingDays <= 7) {
        echo '<div style="background:#fff3cd;padding:10px;border:1px solid #ffc107;">
                ⚠️ Warning: Your license expires in ' . $remainingDays . ' days!
              </div>';
    }
}
```

## 🎨 Error Page Customization

The error pages include:

- **Gradient Background** - Beautiful gradient colors based on error type
- **SVG Icons** - Professional inline SVG icons
- **Details Card** - Shows domain, status, expiry, request limits, etc.
- **Action Buttons** - Contact support and retry buttons
- **Fully Responsive** - Works on mobile, tablet, and desktop

### Error Types & Colors

| Error Type | Color Scheme | Icon |
|------------|--------------|------|
| Invalid License | Purple Gradient | Exclamation |
| Expired License | Red Gradient | Clock |
| Domain Mismatch | Pink Gradient | Globe |
| Suspended License | Blue Gradient | Pause |
| Limit Exceeded | Pink-Red Gradient | Ban |
| Server Error | Orange Gradient | Alert |

## 🔧 API Response Format

### Valid License Response

```json
{
    "status": "valid",
    "message": "Lisensi valid",
    "data": {
        "domain": "example.com",
        "expires_at": "2024-12-31",
        "remaining_days": 120,
        "request_count": 50,
        "request_limit": 1000,
        "remaining_requests": 950
    }
}
```

### Invalid License Response

```json
{
    "status": "invalid",
    "message": "API key tidak valid"
}
```

### Blocked Response

```json
{
    "status": "blocked",
    "message": "Limit request API sudah tercapai",
    "request_count": 1000,
    "request_limit": 1000
}
```

### Error Response

```json
{
    "status": "error",
    "message": "Cannot connect to license server",
    "details": "Connection timeout"
}
```

## 🛡️ Security Best Practices

### 1. Protect Your API Key

Never commit your API key to version control:

```php
// ✗ BAD - Hardcoded
$apiKey = 'abc123def456...';

// ✓ GOOD - Use environment variables
$apiKey = getenv('LICENSE_API_KEY');

// ✓ GOOD - Use config file (add to .gitignore)
require 'config.local.php';
$apiKey = LICENSE_API_KEY;
```

### 2. Use HTTPS

Always use HTTPS for your license server:

```php
// ✗ BAD
$license = new LicenseValidator('http://license-server.com', $apiKey);

// ✓ GOOD
$license = new LicenseValidator('https://license-server.com', $apiKey);
```

### 3. Implement Caching

Reduce API calls with caching to prevent rate limiting:

```php
// Cache validation for 1 hour
$result = validateLicenseWithCache(); // See caching example above
```

### 4. Log Validation Failures

Log failures for security monitoring:

```php
$result = $license->validate('json');

if ($result['status'] !== 'valid') {
    error_log('License validation failed: ' . $result['message']);
}
```

## 📝 Integration Examples

### WordPress Plugin

```php
<?php
/*
Plugin Name: My Premium Plugin
*/

require_once plugin_dir_path(__FILE__) . 'license-client.php';

add_action('init', function() {
    $license = new LicenseValidator(
        'https://your-license-server.com',
        get_option('my_plugin_license_key')
    );
    
    $result = $license->validate('silent');
    
    if (!$result) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>License invalid! Please activate your license.</p></div>';
        });
    }
});
```

### Laravel Application

```php
// app/Http/Middleware/ValidateLicense.php
<?php

namespace App\Http\Middleware;

use Closure;

class ValidateLicense
{
    public function handle($request, Closure $next)
    {
        require_once base_path('license-client.php');
        
        $license = new \LicenseValidator(
            config('license.server_url'),
            config('license.api_key')
        );
        
        $result = $license->validate('json');
        
        if ($result['status'] !== 'valid') {
            return response()->view('errors.license', ['error' => $result], 403);
        }
        
        return $next($request);
    }
}
```

### CodeIgniter Application

```php
// application/hooks/LicenseCheck.php
<?php

class LicenseCheck
{
    public function check()
    {
        require_once APPPATH . 'third_party/license-client.php';
        
        $CI =& get_instance();
        $CI->load->config('license');
        
        $license = new LicenseValidator(
            $CI->config->item('license_server_url'),
            $CI->config->item('license_api_key')
        );
        
        $license->validate('die');
    }
}

// config/hooks.php
$hook['pre_controller'] = array(
    'class'    => 'LicenseCheck',
    'function' => 'check',
    'filename' => 'LicenseCheck.php',
    'filepath' => 'hooks'
);
```

## 🧪 Testing

### Test with Different Domains

```php
// Test different domains
$domains = ['example.com', 'test.com', 'invalid.com'];

foreach ($domains as $domain) {
    $license = new LicenseValidator(
        'https://your-license-server.com',
        'YOUR_API_KEY',
        $domain
    );
    
    $result = $license->validate('json');
    echo "Domain: $domain - Status: " . $result['status'] . "\n";
}
```

### Test Error Pages

```php
// Force error page display
$license = new LicenseValidator(
    'https://your-license-server.com',
    'INVALID_KEY_FOR_TESTING'
);

$license->validate('die'); // Will show error page
```

## ❓ Troubleshooting

### Issue: "Cannot connect to license server"

**Solutions:**
- Check if your server can reach the license server URL
- Verify firewall/network settings
- Ensure cURL or allow_url_fopen is enabled in PHP
- Test connection: `curl https://your-license-server.com/api/validate`

### Issue: "Domain mismatch"

**Solutions:**
- Verify the domain in your license dashboard matches exactly
- Check if using www vs non-www (they're different)
- For local development, add `localhost` as a domain
- Use custom domain override if needed

### Issue: "Request limit exceeded"

**Solutions:**
- Implement caching to reduce API calls
- Contact support to increase your limit
- Reset request count in dashboard
- Check for loops calling validation repeatedly

### Issue: Error page not displaying properly

**Solutions:**
- Check if output buffering is interfering
- Ensure no output before calling `validate()`
- Verify PHP errors are not being displayed
- Check browser console for JavaScript errors

## 📞 Support

For issues or questions:

- **Documentation**: See main README.md and this client documentation
- **GitHub Issues**: Open an issue in the repository
- **Email**: Contact your license provider's support team

## 📄 License

This client library is part of the License Management System and follows the same license as the main project.

---

**Happy Coding! 🚀**
