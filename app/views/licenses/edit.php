<?php 
$pageTitle = 'Edit Lisensi - ' . APP_NAME;
ob_start(); 
?>

<div class="px-4 sm:px-0">
    <div class="mb-8">
        <a href="/licenses/show?id=<?= $license['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail Lisensi
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">
            <i class="fas fa-edit mr-2"></i>Edit Lisensi
        </h1>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="/licenses/edit?id=<?= $license['id'] ?>" method="POST">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-globe mr-2"></i>Domain
                        </label>
                        <input type="text" id="domain" name="domain" value="<?= htmlspecialchars($license['domain']) ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="contoh: domain.com, sub.domain.com, atau *.domain.com">
                        <p class="mt-1 text-sm text-gray-500">Format: domain.com, sub.domain.com, atau *.domain.com untuk wildcard (tanpa http/https)</p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </label>
                        <select id="status" name="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active" <?= $license['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="suspended" <?= $license['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="expired" <?= $license['status'] === 'expired' ? 'selected' : '' ?>>Expired</option>
                        </select>
                    </div>

                    <div>
                        <label for="request_limit" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tachometer-alt mr-2"></i>Limit Request (per bulan)
                        </label>
                        <input type="number" id="request_limit" name="request_limit" value="<?= $license['request_limit'] ?>" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Jumlah maksimal request API yang diperbolehkan</p>
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Tanggal Kadaluarsa
                        </label>
                        <input type="date" id="expires_at" name="expires_at"
                               value="<?= $license['expires_at'] ?>"
                               min="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ada batas waktu</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <a href="/licenses/show?id=<?= $license['id'] ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Update Lisensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
