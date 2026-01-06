<?php 
$pageTitle = 'Dokumentasi Integrasi - ' . APP_NAME;
ob_start(); 
?>

<div class="px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-book mr-2"></i>Dokumentasi Integrasi
        </h1>
        <p class="mt-2 text-gray-600">Panduan lengkap integrasi license validation untuk berbagai framework PHP</p>
    </div>

    <!-- Tab Navigation -->
    <div x-data="{ activeTab: 'ci3' }" class="bg-white shadow rounded-lg">
        <!-- Tab Buttons -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex flex-wrap" aria-label="Tabs">
                <button @click="activeTab = 'ci3'" :class="activeTab === 'ci3' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    📘 CodeIgniter 3
                </button>
                <button @click="activeTab = 'ci4'" :class="activeTab === 'ci4' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    📕 CodeIgniter 4
                </button>
                <button @click="activeTab = 'laravel'" :class="activeTab === 'laravel' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    📙 Laravel
                </button>
                <button @click="activeTab = 'native'" :class="activeTab === 'native' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    📗 PHP Native
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- CodeIgniter 3 -->
            <div x-show="activeTab === 'ci3'" x-cloak>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-fire text-orange-500 mr-2"></i>CodeIgniter 3 Integration
                </h2>
                
                <div class="space-y-6">
                    <!-- Step 1 -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 1: Buat Hook File</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">application/hooks/LicenseCheck.php</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LicenseCheck {
    public function validate() {
        $api_key = '<?= $apiKey ?>'; // Ganti dengan API key Anda
        $domain = $_SERVER['HTTP_HOST'];
        $url = "<?= APP_URL ?>/api/validate?api_key=$api_key&domain=$domain";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        if (!$result || $result['status'] !== 'valid') {
            show_error('Lisensi tidak valid: ' . ($result['message'] ?? 'Error koneksi'), 403);
        }
    }
}</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 2: Aktifkan Hook</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">application/config/hooks.php</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>$hook['pre_controller'] = array(
    'class'    => 'LicenseCheck',
    'function' => 'validate',
    'filename' => 'LicenseCheck.php',
    'filepath' => 'hooks'
);</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 3: Enable Hooks</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">application/config/config.php</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>$config['enable_hooks'] = TRUE;</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Troubleshooting -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">💡 Troubleshooting Tips</h4>
                        <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                            <li>Pastikan folder <code>application/hooks/</code> memiliki permission 755</li>
                            <li>Cek apakah cURL sudah terinstall di server</li>
                            <li>Untuk development, set timeout lebih lama (30 detik)</li>
                            <li>Gunakan error log untuk debug: <code>log_message('error', $response);</code></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- CodeIgniter 4 -->
            <div x-show="activeTab === 'ci4'" x-cloak>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-fire text-red-500 mr-2"></i>CodeIgniter 4 Integration
                </h2>
                
                <div class="space-y-6">
                    <!-- Step 1 -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 1: Buat Filter</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">app/Filters/LicenseFilter.php</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LicenseFilter implements FilterInterface {
    public function before(RequestInterface $request, $arguments = null) {
        $api_key = '<?= $apiKey ?>';
        $domain = $request->getServer('HTTP_HOST');
        $url = "<?= APP_URL ?>/api/validate?api_key=$api_key&domain=$domain";
        
        $client = \Config\Services::curlrequest();
        $response = $client->get($url);
        $result = json_decode($response->getBody(), true);
        
        if (!$result || $result['status'] !== 'valid') {
            return redirect()->to('/license-error')->with('message', $result['message'] ?? 'Error');
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        // Do nothing
    }
}</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 2: Register Filter</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">app/Config/Filters.php</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>public $globals = [
    'before' => [
        'licensecheck' => ['except' => ['login', 'logout']]
    ],
];

public $aliases = [
    'licensecheck' => \App\Filters\LicenseFilter::class,
];</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Troubleshooting -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">💡 Troubleshooting Tips</h4>
                        <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                            <li>Pastikan CURLRequest service sudah aktif</li>
                            <li>Tambahkan route untuk /license-error page</li>
                            <li>Gunakan <code>log_message('error', ...)</code> untuk debug</li>
                            <li>Exclude route login/register dari filter</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Laravel -->
            <div x-show="activeTab === 'laravel'" x-cloak>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fab fa-laravel text-red-600 mr-2"></i>Laravel Integration
                </h2>
                
                <div class="space-y-6">
                    <!-- Step 1 -->
                    <div class="border-l-4 border-red-600 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 1: Buat Middleware</h3>
                        <p class="text-sm text-gray-600 mb-3">Terminal:</p>
                        <div class="relative mb-4">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>php artisan make:middleware LicenseCheck</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">app/Http/Middleware/LicenseCheck.php</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LicenseCheck {
    public function handle(Request $request, Closure $next) {
        $api_key = env('LICENSE_API_KEY');
        $domain = $request->getHost();
        $url = "<?= APP_URL ?>/api/validate?api_key=$api_key&domain=$domain";
        
        $response = Http::timeout(10)->get($url);
        $result = $response->json();
        
        if (!$result || $result['status'] !== 'valid') {
            abort(403, 'Lisensi tidak valid: ' . ($result['message'] ?? 'Error'));
        }
        
        return $next($request);
    }
}</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="border-l-4 border-red-600 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 2: Register Middleware</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">app/Http/Kernel.php</code> (Laravel 10 ke bawah) atau <code class="bg-gray-100 px-2 py-1 rounded">bootstrap/app.php</code> (Laravel 11+)</p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>// Laravel 10 dan dibawahnya (Kernel.php)
protected $middleware = [
    \App\Http\Middleware\LicenseCheck::class,
];

// Laravel 11+ (bootstrap/app.php)
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\LicenseCheck::class);
})</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="border-l-4 border-red-600 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 3: Tambah di .env</h3>
                        <p class="text-sm text-gray-600 mb-3">File: <code class="bg-gray-100 px-2 py-1 rounded">.env</code></p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>LICENSE_API_KEY=<?= $apiKey ?></code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Troubleshooting -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">💡 Troubleshooting Tips</h4>
                        <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                            <li>Pastikan HTTP Client Laravel sudah aktif</li>
                            <li>Jalankan <code>php artisan config:clear</code> setelah update .env</li>
                            <li>Gunakan <code>\Log::error($result)</code> untuk debug</li>
                            <li>Untuk exclude route tertentu, gunakan route middleware</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- PHP Native -->
            <div x-show="activeTab === 'native'" x-cloak>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fab fa-php text-purple-600 mr-2"></i>PHP Native Integration
                </h2>
                
                <div class="space-y-6">
                    <!-- Step 1 -->
                    <div class="border-l-4 border-purple-600 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 1: Buat File license-check.php</h3>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
function checkLicense($api_key) {
    $domain = $_SERVER['HTTP_HOST'];
    $url = "<?= APP_URL ?>/api/validate?api_key=$api_key&domain=$domain";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (!$result || $result['status'] !== 'valid') {
        http_response_code(403);
        die('&lt;h1&gt;Lisensi Tidak Valid&lt;/h1&gt;&lt;p&gt;' . ($result['message'] ?? 'Error') . '&lt;/p&gt;');
    }
    
    return true;
}</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="border-l-4 border-purple-600 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">STEP 2: Include di config.php atau index.php</h3>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
require_once 'license-check.php';
checkLicense('<?= $apiKey ?>');

// Lanjut kode aplikasi Anda...
?&gt;</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Alternative: Session Cache -->
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">BONUS: Dengan Session Cache (Recommended)</h3>
                        <p class="text-sm text-gray-600 mb-3">Untuk mengurangi API calls, gunakan session caching:</p>
                        <div class="relative">
                            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>&lt;?php
session_start();

function checkLicense($api_key) {
    // Check if cached and not expired (cache for 1 hour)
    if (isset($_SESSION['license_valid']) && 
        isset($_SESSION['license_check_time']) && 
        (time() - $_SESSION['license_check_time']) < 3600) {
        return true;
    }
    
    $domain = $_SERVER['HTTP_HOST'];
    $url = "<?= APP_URL ?>/api/validate?api_key=$api_key&domain=$domain";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (!$result || $result['status'] !== 'valid') {
        http_response_code(403);
        die('&lt;h1&gt;Lisensi Tidak Valid&lt;/h1&gt;&lt;p&gt;' . ($result['message'] ?? 'Error') . '&lt;/p&gt;');
    }
    
    // Cache the result
    $_SESSION['license_valid'] = true;
    $_SESSION['license_check_time'] = time();
    
    return true;
}

checkLicense('<?= $apiKey ?>');</code></pre>
                            <button onclick="copyCode(this)" class="absolute top-2 right-2 px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                                <i class="fas fa-copy mr-1"></i>Copy Code
                            </button>
                        </div>
                    </div>

                    <!-- Troubleshooting -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">💡 Troubleshooting Tips</h4>
                        <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                            <li>Pastikan cURL extension terinstall: <code>php -m | grep curl</code></li>
                            <li>Gunakan session cache untuk mengurangi API calls</li>
                            <li>Tambahkan error logging: <code>error_log($response);</code></li>
                            <li>Test dengan <code>var_dump($result);</code> untuk debug</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Endpoint Reference -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-xl font-semibold text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>API Endpoint Reference
        </h3>
        <div class="space-y-3">
            <div>
                <p class="text-sm font-medium text-blue-900">Endpoint URL:</p>
                <code class="block bg-white px-3 py-2 rounded text-sm text-gray-800 mt-1"><?= APP_URL ?>/api/validate</code>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-900">Method:</p>
                <code class="bg-white px-3 py-1 rounded text-sm">GET</code> atau <code class="bg-white px-3 py-1 rounded text-sm">POST</code>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-900">Parameters:</p>
                <ul class="mt-2 space-y-1 text-sm text-blue-800">
                    <li>• <code class="bg-white px-2 py-1 rounded">api_key</code> - API key lisensi Anda</li>
                    <li>• <code class="bg-white px-2 py-1 rounded">domain</code> - Domain yang akan divalidasi</li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-900">Response Example:</p>
                <pre class="bg-white px-3 py-2 rounded text-xs text-gray-800 mt-1 overflow-x-auto">{
  "status": "valid",
  "message": "Lisensi valid",
  "data": {
    "domain": "<?= $domain ?>",
    "expires_at": "2026-12-31",
    "remaining_days": 365
  }
}</pre>
            </div>
        </div>
    </div>
</div>

<script>
function copyCode(button) {
    const pre = button.previousElementSibling;
    const code = pre.querySelector('code');
    const text = code.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'bg-red-600', 'hover:bg-red-700', 'bg-purple-600', 'hover:bg-purple-700', 'bg-green-600', 'hover:bg-green-700');
        button.classList.add('bg-green-600');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600');
            // Restore original color based on button context
            if (button.closest('[x-show="activeTab === \'ci3\'"]')) {
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else if (button.closest('[x-show="activeTab === \'ci4\'"]')) {
                button.classList.add('bg-red-600', 'hover:bg-red-700');
            } else if (button.closest('[x-show="activeTab === \'laravel\'"]')) {
                button.classList.add('bg-red-600', 'hover:bg-red-700');
            } else if (button.closest('[x-show="activeTab === \'native\'"]')) {
                const isBonus = button.closest('.border-green-500');
                if (isBonus) {
                    button.classList.add('bg-green-600', 'hover:bg-green-700');
                } else {
                    button.classList.add('bg-purple-600', 'hover:bg-purple-700');
                }
            }
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Gagal menyalin kode. Silakan copy secara manual.');
    });
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
