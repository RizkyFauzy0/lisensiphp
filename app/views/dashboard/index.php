<?php 
$pageTitle = 'Dashboard - ' . APP_NAME;
ob_start(); 
?>

<div class="px-4 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">
        <i class="fas fa-chart-line mr-2"></i>Dashboard
    </h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-certificate text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Lisensi
                            </dt>
                            <dd class="text-3xl font-semibold text-gray-900">
                                <?= $stats['total'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Lisensi Aktif
                            </dt>
                            <dd class="text-3xl font-semibold text-gray-900">
                                <?= $stats['active'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-times-circle text-3xl text-red-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Expired
                            </dt>
                            <dd class="text-3xl font-semibold text-gray-900">
                                <?= $stats['expired'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-3xl text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Akan Expired
                            </dt>
                            <dd class="text-3xl font-semibold text-gray-900">
                                <?= $stats['expiring_soon'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i class="fas fa-bolt mr-2"></i>Quick Actions
            </h3>
            <div class="flex flex-wrap gap-4">
                <a href="/licenses/create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Tambah Lisensi Baru
                </a>
                <a href="/licenses" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-list mr-2"></i>Lihat Semua Lisensi
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Expiring Soon Licenses -->
        <?php if (!empty($expiringSoon)): ?>
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-clock text-yellow-500 mr-2"></i>Lisensi Akan Expired (7 Hari)
                </h3>
                <div class="space-y-3">
                    <?php foreach ($expiringSoon as $license): ?>
                        <?php 
                        $daysLeft = ceil((strtotime($license['expires_at']) - time()) / (60 * 60 * 24));
                        ?>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($license['domain']) ?></p>
                                <p class="text-sm text-gray-500">
                                    Expired dalam <?= $daysLeft ?> hari
                                </p>
                            </div>
                            <a href="/licenses/show?id=<?= $license['id'] ?>" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activities -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-history mr-2"></i>Aktivitas Terbaru
                </h3>
                <div class="space-y-3">
                    <?php if (empty($recentActivities)): ?>
                        <p class="text-gray-500 text-sm">Belum ada aktivitas</p>
                    <?php else: ?>
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <?php if ($activity['status'] === 'success'): ?>
                                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                    <?php elseif ($activity['status'] === 'blocked'): ?>
                                        <i class="fas fa-ban text-yellow-500 text-xl"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-red-500 text-xl"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($activity['domain'] ?? 'Unknown') ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        IP: <?= htmlspecialchars($activity['ip_address']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        <?= date('d M Y H:i', strtotime($activity['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
