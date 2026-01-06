<?php
/**
 * Advanced Example - License Validation with Custom Options
 * 
 * This example demonstrates advanced usage with custom configuration,
 * different validation modes, and custom error handling.
 */

require_once 'license-check.php';

// =============================================================================
// ADVANCED USAGE WITH CUSTOM OPTIONS
// =============================================================================

$license = new LicenseChecker(
    'https://lisensi.gdvmedia.com',
    'YOUR_API_KEY',
    [
        'cache_duration' => 1800,                        // 30 menit cache
        'support_url' => 'https://gdvmedia.com/contact', // URL support custom
        'purchase_url' => 'https://gdvmedia.com/pricing' // URL pembelian custom
        // 'domain' => 'custom-domain.com'               // Override domain (opsional)
    ]
);

// =============================================================================
// MODE 1: Auto Die dengan Tampilan Cantik (Default)
// =============================================================================
// Gunakan ini untuk implementasi paling sederhana
// Jika lisensi tidak valid, akan tampil halaman error profesional dan berhenti

// $license->validate();


// =============================================================================
// MODE 2: Check tanpa Die - Return Full Response
// =============================================================================
// Gunakan ini untuk custom handling atau logging

$result = $license->check();

if ($result['status'] !== 'valid') {
    // Log error
    error_log('License validation failed: ' . $result['message']);
    
    // Custom handling berdasarkan tipe error
    switch ($result['error_type'] ?? 'unknown') {
        case 'license_expired':
            // Redirect ke halaman renewal
            // header('Location: /renew-license');
            // exit;
            break;
            
        case 'domain_mismatch':
            // Kirim notifikasi ke admin
            // mail('admin@example.com', 'Domain Mismatch', $result['message']);
            break;
            
        case 'limit_exceeded':
            // Tampilkan pesan khusus
            // echo "Request limit exceeded. Please upgrade your plan.";
            // exit;
            break;
    }
    
    // Atau bisa langsung render error page
    // $license->validate(); // Ini akan die dengan error page
}


// =============================================================================
// MODE 3: Simple Boolean Check
// =============================================================================
// Gunakan ini untuk conditional logic sederhana

if (!$license->isValid()) {
    // Redirect ke halaman pembelian
    header('Location: https://gdvmedia.com/pricing');
    exit;
}


// =============================================================================
// TAMPILAN HASIL - Jika Lisensi Valid
// =============================================================================

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Advanced Example - License Valid</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .success-icon {
            width: 60px;
            height: 60px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #1f2937;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
            font-weight: 600;
        }
        .info-value {
            color: #1f2937;
            font-family: 'Courier New', monospace;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .code-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .code-block {
            background: #1f2937;
            color: #f9fafb;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin-top: 15px;
        }
        .comment {
            color: #9ca3af;
        }
        .string {
            color: #a78bfa;
        }
        .keyword {
            color: #60a5fa;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
        }
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <div class='success-icon'>
                <svg width='32' height='32' fill='none' stroke='white' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M5 13l4 4L19 7'></path>
                </svg>
            </div>
            <h1>✓ Lisensi Valid - Advanced Mode</h1>
            <p class='subtitle'>Aplikasi berjalan dengan konfigurasi advanced dan custom options</p>
            <div style='margin-top: 15px;'>
                <span class='badge badge-success'>Advanced Example</span>
            </div>
        </div>
        
        <div class='grid'>
            <div class='card'>
                <h2>📋 Informasi Lisensi</h2>";

// Display license information
if ($result['status'] === 'valid' && isset($result['data'])) {
    $data = $result['data'];
    
    echo "<div class='info-row'>
            <span class='info-label'>Domain</span>
            <span class='info-value'>" . htmlspecialchars($data['domain'] ?? 'N/A') . "</span>
          </div>";
    
    echo "<div class='info-row'>
            <span class='info-label'>Status</span>
            <span class='badge badge-success'>Active</span>
          </div>";
    
    if (isset($data['expires_at']) && $data['expires_at']) {
        $expiresAt = htmlspecialchars($data['expires_at']);
        echo "<div class='info-row'>
                <span class='info-label'>Tanggal Kadaluarsa</span>
                <span class='info-value'>{$expiresAt}</span>
              </div>";
    }
    
    if (isset($data['remaining_days']) && $data['remaining_days'] !== null) {
        $days = intval($data['remaining_days']);
        $badgeClass = $days <= 7 ? 'badge-warning' : 'badge-success';
        echo "<div class='info-row'>
                <span class='info-label'>Sisa Hari</span>
                <span class='badge {$badgeClass}'>{$days} hari</span>
              </div>";
    }
}

echo "      </div>
            
            <div class='card'>
                <h2>📊 Penggunaan Request API</h2>";

if ($result['status'] === 'valid' && isset($result['data'])) {
    $data = $result['data'];
    $requestCount = intval($data['request_count'] ?? 0);
    $requestLimit = intval($data['request_limit'] ?? 1000);
    $remaining = intval($data['remaining_requests'] ?? 0);
    $percentage = $requestLimit > 0 ? round(($requestCount / $requestLimit) * 100, 1) : 0;
    
    echo "<div class='info-row'>
            <span class='info-label'>Request Digunakan</span>
            <span class='info-value'>" . number_format($requestCount) . "</span>
          </div>
          <div class='info-row'>
            <span class='info-label'>Total Limit</span>
            <span class='info-value'>" . number_format($requestLimit) . "</span>
          </div>
          <div class='info-row'>
            <span class='info-label'>Sisa Request</span>
            <span class='info-value'>" . number_format($remaining) . "</span>
          </div>";
    
    echo "<div class='progress-bar'>
            <div class='progress-fill' style='width: {$percentage}%'></div>
          </div>
          <p style='text-align: center; margin-top: 5px; font-size: 12px; color: #6b7280;'>
            {$percentage}% terpakai
          </p>";
}

echo "      </div>
        </div>
        
        <div class='code-section'>
            <h2 style='margin-bottom: 15px; color: #1f2937;'>💻 Kode yang Digunakan</h2>
            <p style='color: #6b7280; margin-bottom: 10px;'>
                Contoh implementasi advanced dengan berbagai mode validasi:
            </p>
            <div class='code-block'><span class='comment'>// Advanced usage dengan custom options</span>
<span class='keyword'>\$license</span> = <span class='keyword'>new</span> LicenseChecker(
    <span class='string'>'https://lisensi.gdvmedia.com'</span>,
    <span class='string'>'YOUR_API_KEY'</span>,
    [
        <span class='string'>'cache_duration'</span> => 1800,
        <span class='string'>'support_url'</span> => <span class='string'>'https://gdvmedia.com/contact'</span>,
        <span class='string'>'purchase_url'</span> => <span class='string'>'https://gdvmedia.com/pricing'</span>
    ]
);

<span class='comment'>// Mode 1: Auto die dengan tampilan cantik</span>
<span class='keyword'>\$license</span>->validate();

<span class='comment'>// Mode 2: Check tanpa die - return full response</span>
<span class='keyword'>\$result</span> = <span class='keyword'>\$license</span>->check();
<span class='keyword'>if</span> (<span class='keyword'>\$result</span>[<span class='string'>'status'</span>] !== <span class='string'>'valid'</span>) {
    <span class='comment'>// Custom handling</span>
}

<span class='comment'>// Mode 3: Simple boolean check</span>
<span class='keyword'>if</span> (!<span class='keyword'>\$license</span>->isValid()) {
    header(<span class='string'>'Location: /buy-license'</span>);
    exit;
}</div>
        </div>
        
        <div style='background: white; padding: 20px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); margin-top: 20px; text-align: center;'>
            <p style='color: #6b7280; margin-bottom: 10px;'>
                <strong>✨ Fitur Advanced:</strong>
            </p>
            <div style='display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;'>
                <span class='badge badge-success'>Session Caching (30 menit)</span>
                <span class='badge badge-success'>Custom Support URL</span>
                <span class='badge badge-success'>Custom Purchase URL</span>
                <span class='badge badge-success'>Multiple Validation Modes</span>
                <span class='badge badge-success'>Error Type Detection</span>
            </div>
        </div>
    </div>
</body>
</html>";
