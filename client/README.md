# License Client Integration Guide

Professional PHP client library for integrating license validation into your applications with beautiful error pages and multiple display modes.

## 📦 Features

- **OOP Design** - Clean, modern PHP class-based implementation
- **Beautiful Error Pages** - Professional styled error pages with Tailwind CSS
- **Session-Based Caching** - Automatic caching to reduce API calls
- **Multiple Validation Modes** - Choose how to handle invalid licenses
- **6 Error States** - Comprehensive error handling with specific styling
- **Auto Domain Detection** - Automatically detects the current domain
- **Configurable Options** - Custom cache duration, support URL, purchase URL
- **Zero Dependencies** - Works with pure PHP (no external libraries needed)
- **Fully Responsive** - Error pages look great on all devices
- **Production Ready** - Built-in caching and best practices

## 📁 Available Files

### Main Libraries

1. **`license-check.php`** (NEW - Recommended ⭐)
   - Latest version with Tailwind CSS error pages
   - Session-based caching system
   - 6 distinct error states with unique styling
   - Configurable options (cache duration, URLs)
   - Methods: `validate()`, `check()`, `isValid()`
   - Helper function: `checkLicense()`

2. **`license-client.php`** (Legacy)
   - Original version with inline CSS
   - Multiple display modes (die, silent, json, redirect)
   - Still maintained for backward compatibility

### Example Files

3. **`example-basic.php`** - Simple 2-line integration example
4. **`example-advanced.php`** - Advanced usage with custom options
5. **`example-usage.php`** - Legacy examples (for license-client.php)

### Documentation

6. **`README.md`** - This file - Complete integration guide

---

## 🚀 Quick Start (New - Recommended)

### 1. Download the Client File

Copy the `license-check.php` file to your application directory:

```bash
# Copy to your project root or includes directory
cp client/license-check.php /path/to/your/project/
```

### 2. Get Your API Key

1. Login to your license management dashboard
2. Navigate to **Licenses** section
3. Create a new license or view existing one
4. Copy the **API Key**

### 3. Basic Integration (Just 2 Lines!)

Add this to your application's main file (e.g., `index.php`, `config.php`, or bootstrap file):

```php
<?php
require_once 'license-check.php';

// Just 2 lines for complete protection!
checkLicense('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');

// Your application continues here if valid
echo "Application running!";
?>
```

That's it! Your application is now protected with beautiful error pages.

### 4. See It In Action

- Run `example-basic.php` for simple usage demo
- Run `example-advanced.php` for advanced features demo

---

## 📖 Usage Guide (license-check.php)

### LicenseChecker Class

The new `LicenseChecker` class provides three main methods:

#### 1. validate() - Auto Die with Error Page

The simplest method. Shows beautiful error page and stops execution if invalid:

```php
$license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');
$license->validate(); // Dies with error page if invalid

// Code continues here only if valid
```

#### 2. check() - Get Full Response

Returns complete response array without dying:

```php
$license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');
$result = $license->check();

if ($result['status'] === 'valid') {
    echo "License valid!";
    echo "Expires: " . $result['data']['expires_at'];
    echo "Remaining requests: " . $result['data']['remaining_requests'];
} else {
    // Custom error handling
    error_log('License error: ' . $result['message']);
}
```

#### 3. isValid() - Simple Boolean

Returns true/false for simple conditional logic:

```php
$license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');

if (!$license->isValid()) {
    header('Location: /buy-license');
    exit;
}
```

### Configuration Options

Pass custom options as third parameter:

```php
$license = new LicenseChecker(
    'https://lisensi.gdvmedia.com',
    'YOUR_API_KEY',
    [
        'cache_duration' => 1800,                        // 30 minutes (default: 3600)
        'support_url' => 'https://gdvmedia.com/contact', // Support button URL
        'purchase_url' => 'https://gdvmedia.com/pricing', // Purchase button URL
        'domain' => 'custom-domain.com'                  // Override auto-detected domain
    ]
);
```

### Helper Function

For quick implementation, use the `checkLicense()` helper:

```php
// Simplest possible usage
checkLicense('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');

// With options
checkLicense('https://lisensi.gdvmedia.com', 'YOUR_API_KEY', [
    'cache_duration' => 1800,
    'support_url' => 'https://gdvmedia.com/contact'
]);
```

---

## 🎨 Error States

The new library handles 6 distinct error states with unique styling:

| Error Type | Title | Gradient Colors | Icon | HTTP Code |
|------------|-------|----------------|------|-----------|
| **License Not Found** | Lisensi Tidak Ditemukan | Red to Rose | Exclamation | 403 |
| **Domain Mismatch** | Domain Tidak Sesuai | Orange to Amber | Globe | 403 |
| **License Expired** | Lisensi Kadaluarsa | Yellow to Orange | Clock | 403 |
| **License Suspended** | Lisensi Di-Suspend | Red to Dark Red | Pause | 403 |
| **Limit Exceeded** | Batas Request Tercapai | Blue to Indigo | Ban | 429 |
| **Connection Error** | Kesalahan Koneksi | Gray to Dark Gray | Alert | 503 |

Each error state displays:
- Beautiful Tailwind CSS gradient background
- Matching icon with color coding
- Clear error title and message
- Details card with domain, status, expiry, request limits
- Action buttons (Contact Support, Buy License)
- Fully responsive design

---

## 💾 Session Caching

Built-in session caching reduces API calls:

```php
// Default: Cache for 1 hour (3600 seconds)
$license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');

// Custom: Cache for 30 minutes
$license = new LicenseChecker(
    'https://lisensi.gdvmedia.com',
    'YOUR_API_KEY',
    ['cache_duration' => 1800]
);

// Cache is stored in PHP session
// Automatically cleared after cache_duration expires
// Valid licenses are cached, invalid ones are not
```

---

## 📋 Advanced Usage Examples

### Validate Only on Specific Routes

```php
$requestUri = $_SERVER['REQUEST_URI'];

// Only validate on admin pages
if (strpos($requestUri, '/admin') === 0) {
    $license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');
    $license->validate();
}
```

### Show Expiry Warning

```php
$license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');
$result = $license->check();

if ($result['status'] === 'valid') {
    $remainingDays = $result['data']['remaining_days'] ?? null;
    
    if ($remainingDays && $remainingDays <= 7) {
        echo '<div style="background:#fff3cd;padding:10px;border:1px solid #ffc107;">
                ⚠️ Warning: Your license expires in ' . $remainingDays . ' days!
              </div>';
    }
}
```

### Custom Error Handling by Type

```php
$license = new LicenseChecker('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');
$result = $license->check();

if ($result['status'] !== 'valid') {
    switch ($result['error_type'] ?? 'unknown') {
        case 'license_expired':
            header('Location: /renew-license');
            exit;
            
        case 'domain_mismatch':
            mail('admin@example.com', 'Domain Mismatch', $result['message']);
            break;
            
        case 'limit_exceeded':
            echo "Request limit exceeded. Please upgrade your plan.";
            exit;
    }
}
```

---

## 📖 Legacy Library (license-client.php)

### LicenseValidator Class

The legacy `LicenseValidator` class supports multiple display modes:

#### Display Modes

```php
$license = new LicenseValidator('https://your-server.com', 'YOUR_API_KEY');

// 1. Die Mode (default) - shows error page and stops
$license->validate(); // or $license->validate('die')

// 2. Silent Mode - returns boolean
$isValid = $license->validate('silent');

// 3. JSON Mode - returns full response array
$result = $license->validate('json');

// 4. Redirect Mode - redirects if invalid
$license->validate('redirect', 'https://your-site.com/buy-license');
```

#### Set Custom Support Email

```php
$license = new LicenseValidator('https://your-server.com', 'YOUR_API_KEY');
$license->setSupportEmail('help@your-company.com');
$license->validate();
```

See `example-usage.php` for more legacy examples.

---

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
    "error_type": "connection_error",
    "message": "Cannot connect to license server",
    "details": "Connection timeout"
}
```

---

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
$license = new LicenseChecker('http://license-server.com', $apiKey);

// ✓ GOOD
$license = new LicenseChecker('https://license-server.com', $apiKey);
```

### 3. Implement Caching

Reduce API calls with caching to prevent rate limiting:

```php
// Built-in session caching (recommended)
$license = new LicenseChecker(
    'https://lisensi.gdvmedia.com',
    'YOUR_API_KEY',
    ['cache_duration' => 3600] // 1 hour
);
```

### 4. Log Validation Failures

Log failures for security monitoring:

```php
$result = $license->check();

if ($result['status'] !== 'valid') {
    error_log('License validation failed: ' . $result['message']);
}
```

---

## 📝 Integration Examples

### WordPress Plugin

```php
<?php
/*
Plugin Name: My Premium Plugin
*/

require_once plugin_dir_path(__FILE__) . 'license-check.php';

add_action('init', function() {
    $license = new LicenseChecker(
        'https://your-license-server.com',
        get_option('my_plugin_license_key')
    );
    
    if (!$license->isValid()) {
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
        require_once base_path('license-check.php');
        
        $license = new \LicenseChecker(
            config('license.server_url'),
            config('license.api_key')
        );
        
        if (!$license->isValid()) {
            return response()->view('errors.license', [], 403);
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
        require_once APPPATH . 'third_party/license-check.php';
        
        $CI =& get_instance();
        $CI->load->config('license');
        
        $license = new LicenseChecker(
            $CI->config->item('license_server_url'),
            $CI->config->item('license_api_key')
        );
        
        $license->validate();
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

---

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
- Implement caching to reduce API calls (built-in with license-check.php)
- Contact support to increase your limit
- Reset request count in dashboard
- Check for loops calling validation repeatedly

### Issue: Error page not displaying properly

**Solutions:**
- Check if output buffering is interfering
- Ensure no output before calling `validate()`
- Verify PHP errors are not being displayed
- Check browser console for JavaScript errors

---

## 📞 Support

For issues or questions:

- **Documentation**: See main README.md and this client documentation
- **Example Files**: Check example-basic.php and example-advanced.php
- **GitHub Issues**: Open an issue in the repository
- **Email**: Contact your license provider's support team

---

## 📄 License

This client library is part of the License Management System and follows the same license as the main project.

---

**Happy Coding! 🚀**
