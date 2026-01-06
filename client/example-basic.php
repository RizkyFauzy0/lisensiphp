<?php
/**
 * Basic Example - Simple License Validation
 * 
 * This is the simplest way to validate your license.
 * Just 2 lines of code and you're protected!
 */

require_once 'license-check.php';

// Cukup 2 baris untuk validasi!
checkLicense('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');

// Kode aplikasi Anda di bawah ini...
// Jika lisensi tidak valid, kode di bawah ini tidak akan dieksekusi
// karena fungsi checkLicense() akan menampilkan halaman error dan berhenti

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Aplikasi Berjalan</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #10b981;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .checkmark svg {
            width: 50px;
            height: 50px;
            color: white;
        }
        h1 {
            color: #1f2937;
            margin: 0 0 10px 0;
        }
        p {
            color: #6b7280;
            margin: 0 0 30px 0;
            font-size: 18px;
        }
        .code-block {
            background: #1f2937;
            color: #f9fafb;
            padding: 20px;
            border-radius: 10px;
            text-align: left;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin-top: 20px;
            overflow-x: auto;
        }
        .code-block .comment {
            color: #9ca3af;
        }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='success-card'>
        <div class='checkmark'>
            <svg fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M5 13l4 4L19 7'></path>
            </svg>
        </div>
        <h1>✓ Aplikasi Berjalan dengan Lisensi Valid!</h1>
        <p>Validasi lisensi berhasil. Aplikasi Anda dilindungi.</p>
        
        <div class='badge'>Basic Example Mode</div>
        
        <div class='code-block'>
<span class='comment'>// File: example-basic.php</span>
&lt;?php
require_once 'license-check.php';

<span class='comment'>// Cukup 2 baris untuk validasi!</span>
checkLicense('https://lisensi.gdvmedia.com', 'YOUR_API_KEY');

<span class='comment'>// Kode aplikasi Anda di bawah ini...</span>
echo &quot;Aplikasi berjalan dengan lisensi valid!&quot;;
?&gt;
        </div>
        
        <p style='margin-top: 30px; font-size: 14px; color: #9ca3af;'>
            <strong>💡 Tips:</strong> Ganti 'YOUR_API_KEY' dengan API key asli Anda dari dashboard lisensi.
        </p>
    </div>
</body>
</html>";
