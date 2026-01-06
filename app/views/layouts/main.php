<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Hamburger Menu Button (Mobile Only) -->
    <button id="mobile-menu-btn" class="lg:hidden fixed top-4 left-4 z-50 bg-blue-600 text-white p-3 rounded-lg shadow-lg hover:bg-blue-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Sidebar Overlay (Mobile Only) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-2xl z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Logo Section -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <a href="/dashboard" class="flex items-center text-xl font-bold text-blue-600">
                    <i class="fas fa-key text-2xl mr-3"></i>
                    <span><?= APP_NAME ?></span>
                </a>
                <button id="close-sidebar-btn" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Menu Items -->
            <nav class="flex-1 overflow-y-auto p-4">
                <a href="/dashboard" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'bg-blue-50 text-blue-600 font-semibold' : '' ?>">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/licenses" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/licenses') !== false ? 'bg-blue-50 text-blue-600 font-semibold' : '' ?>">
                    <i class="fas fa-certificate w-5 mr-3"></i>
                    <span>Lisensi</span>
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                <a href="/users" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'bg-blue-50 text-blue-600 font-semibold' : '' ?>">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>Users</span>
                </a>
                <?php endif; ?>
                <a href="/documentation" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/documentation') !== false ? 'bg-blue-50 text-blue-600 font-semibold' : '' ?>">
                    <i class="fas fa-book w-5 mr-3"></i>
                    <span>Dokumentasi</span>
                </a>
            </nav>

            <!-- Sidebar Footer (User Info) -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center mb-3">
                    <i class="fas fa-user-circle text-3xl text-gray-400 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['role'] ?? '') ?></p>
                    </div>
                </div>
                <a href="/logout" class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area (with left margin for sidebar on desktop) -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-4 mt-4 lg:mx-8">
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700"><?= htmlspecialchars($_SESSION['success']) ?></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="text-green-400 hover:text-green-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mx-4 mt-4 lg:mx-8">
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700"><?= htmlspecialchars($_SESSION['error']) ?></p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button @click="show = false" class="text-red-400 hover:text-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8 mt-16 lg:mt-0">
            <?= $content ?? '' ?>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="py-6 px-4 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');

        function openSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent body scroll
            }
        }

        function closeSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = ''; // Restore body scroll
            }
        }

        // Open menu on hamburger button click
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openSidebar);
        }

        // Close menu on close button click
        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', closeSidebar);
        }

        // Close menu on overlay click
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close menu on menu item click (mobile only)
        if (sidebar) {
            var sidebarLinks = document.querySelectorAll('#sidebar a');
            Array.from(sidebarLinks).forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });
        }

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
