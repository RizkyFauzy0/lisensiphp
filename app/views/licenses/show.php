<?php 
$pageTitle = 'Detail Lisensi - ' . APP_NAME;
ob_start(); 
?>

<div class="px-4 sm:px-0">
    <div class="mb-8">
        <a href="/licenses" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Lisensi
        </a>
        <div class="mt-4 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-certificate mr-2"></i>Detail Lisensi
            </h1>
            <div class="flex space-x-2">
                <a href="/licenses/edit?id=<?= $license['id'] ?>" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <button onclick="confirmDelete(<?= $license['id'] ?>)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- API Key Alert -->
    <?php if (isset($_SESSION['new_api_key'])): ?>
    <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-6 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-medium text-green-800 mb-2">API Key Berhasil Di-generate!</h3>
                <p class="text-sm text-green-700 mb-3">Simpan API Key ini dengan aman. Anda tidak akan bisa melihatnya lagi.</p>
                <div class="bg-white p-4 rounded border border-green-200">
                    <div class="flex items-center justify-between">
                        <code class="text-sm text-gray-800 break-all" id="apiKeyText"><?= htmlspecialchars($_SESSION['new_api_key']) ?></code>
                        <button onclick="copyApiKey('<?= htmlspecialchars($_SESSION['new_api_key']) ?>')" class="ml-4 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['new_api_key']); ?>
    <?php endif; ?>

    <!-- License Info -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Lisensi</h2>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Domain</dt>
                    <dd class="mt-1 text-sm text-gray-900 flex items-center">
                        <i class="fas fa-globe text-blue-500 mr-2"></i>
                        <?= htmlspecialchars($license['domain']) ?>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <?php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800',
                            'expired' => 'bg-red-100 text-red-800',
                            'suspended' => 'bg-yellow-100 text-yellow-800'
                        ];
                        ?>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full <?= $statusColors[$license['status']] ?>">
                            <?= ucfirst($license['status']) ?>
                        </span>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">API Key</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 p-2 rounded break-all">
                        <?= htmlspecialchars(substr($license['api_key'], 0, 20)) ?>...
                        <button onclick="showFullApiKey()" class="text-blue-600 hover:text-blue-800 ml-2">
                            <i class="fas fa-eye"></i>
                        </button>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Request Usage</dt>
                    <dd class="mt-1">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-900">
                                <?= number_format($license['request_count']) ?> / <?= number_format($license['request_limit']) ?>
                            </span>
                            <span class="ml-2 text-sm text-gray-500">
                                (<?= number_format(($license['request_count'] / $license['request_limit']) * 100, 1) ?>%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?= min(100, ($license['request_count'] / $license['request_limit']) * 100) ?>%"></div>
                        </div>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Kadaluarsa</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?php if ($license['expires_at']): ?>
                            <?= date('d M Y', strtotime($license['expires_at'])) ?>
                            <?php
                            $daysLeft = ceil((strtotime($license['expires_at']) - time()) / (60 * 60 * 24));
                            if ($daysLeft > 0):
                            ?>
                                <span class="text-<?= $daysLeft <= 7 ? 'yellow' : 'green' ?>-600">
                                    (<?= $daysLeft ?> hari lagi)
                                </span>
                            <?php elseif ($daysLeft == 0): ?>
                                <span class="text-red-600">(Hari ini)</span>
                            <?php else: ?>
                                <span class="text-red-600">(Sudah kadaluarsa)</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-gray-400">Tidak ada batas waktu</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?= htmlspecialchars($license['created_by_name'] ?? 'N/A') ?>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?= date('d M Y H:i', strtotime($license['created_at'])) ?>
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <?= date('d M Y H:i', strtotime($license['updated_at'])) ?>
                    </dd>
                </div>
            </dl>

            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="/licenses/regenerate-api-key" onsubmit="return confirm('Yakin ingin me-regenerate API Key? API Key lama tidak akan bisa digunakan lagi.');">
                    <input type="hidden" name="id" value="<?= $license['id'] ?>">
                    <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-sync mr-2"></i>Regenerate API Key
                    </button>
                </form>
                <form method="POST" action="/licenses/reset-request-count" onsubmit="return confirm('Yakin ingin mereset request count ke 0?');">
                    <input type="hidden" name="id" value="<?= $license['id'] ?>">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-redo mr-2"></i>Reset Request Count
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- API Usage Example -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Contoh Penggunaan API</h2>
            <div class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto">
                <pre class="text-sm"><code>// PHP Example
$api_key = '<?= htmlspecialchars($license['api_key']) ?>';
$domain = '<?= htmlspecialchars($license['domain']) ?>';

$url = "<?= APP_URL ?>/api/validate?api_key=$api_key&domain=$domain";
$response = file_get_contents($url);
$result = json_decode($response, true);

if ($result['status'] === 'valid') {
    echo "License valid!";
} else {
    die("License invalid: " . $result['message']);
}</code></pre>
            </div>
        </div>
    </div>

    <!-- API Logs -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Log Aktivitas</h2>
            
            <?php if (empty($logs)): ?>
                <p class="text-gray-500 text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-2"></i><br>
                    Belum ada log aktivitas
                </p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domain</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $statusColors = [
                                            'success' => 'text-green-600',
                                            'failed' => 'text-red-600',
                                            'blocked' => 'text-yellow-600'
                                        ];
                                        $statusIcons = [
                                            'success' => 'fa-check-circle',
                                            'failed' => 'fa-times-circle',
                                            'blocked' => 'fa-ban'
                                        ];
                                        ?>
                                        <span class="<?= $statusColors[$log['status']] ?>">
                                            <i class="fas <?= $statusIcons[$log['status']] ?> mr-1"></i>
                                            <?= ucfirst($log['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($log['ip_address']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($log['request_domain']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?= htmlspecialchars($log['message']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('d M Y H:i', strtotime($log['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Log Pagination -->
                <?php if ($totalLogPages > 1): ?>
                <div class="mt-4 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <?php for ($i = 1; $i <= $totalLogPages; $i++): ?>
                            <a href="?id=<?= $license['id'] ?>&log_page=<?= $i ?>" 
                               class="<?= $i === $page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Lisensi</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus lisensi ini? Semua data termasuk log aktivitas akan terhapus.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form id="deleteForm" method="POST" action="/licenses/delete">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Full API Key Modal -->
<div id="apiKeyModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeApiKeyModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Full API Key</h3>
                <div class="bg-gray-100 p-4 rounded break-all font-mono text-sm">
                    <?= htmlspecialchars($license['api_key']) ?>
                </div>
                <button onclick="copyApiKey('<?= htmlspecialchars($license['api_key']) ?>')" class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-copy mr-2"></i>Copy API Key
                </button>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6">
                <button type="button" onclick="closeApiKeyModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

function showFullApiKey() {
    document.getElementById('apiKeyModal').classList.remove('hidden');
}

function closeApiKeyModal() {
    document.getElementById('apiKeyModal').classList.add('hidden');
}

function copyApiKey(apiKey) {
    navigator.clipboard.writeText(apiKey).then(function() {
        alert('API Key berhasil di-copy!');
    });
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
