<?php 
$pageTitle = 'Tambah Lisensi - ' . APP_NAME;
ob_start(); 
?>

<div class="px-4 sm:px-0">
    <div class="mb-8">
        <a href="/licenses" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Lisensi
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">
            <i class="fas fa-plus-circle mr-2"></i>Tambah Lisensi Baru
        </h1>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="/licenses/create" method="POST">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-globe mr-2"></i>Domain
                        </label>
                        <input type="text" id="domain" name="domain" required
                               pattern="[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="contoh: example.com">
                        <p class="mt-1 text-sm text-gray-500">Format: example.com (tanpa http/https)</p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </label>
                        <select id="status" name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    <div>
                        <label for="request_limit" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tachometer-alt mr-2"></i>Limit Request (per bulan)
                        </label>
                        <input type="number" id="request_limit" name="request_limit" value="1000" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Jumlah maksimal request API yang diperbolehkan</p>
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Tanggal Kadaluarsa
                        </label>
                        <input type="date" id="expires_at" name="expires_at"
                               min="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <a href="/licenses" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Simpan Lisensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Catatan:</strong> API Key akan di-generate secara otomatis setelah lisensi dibuat.
                </p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
