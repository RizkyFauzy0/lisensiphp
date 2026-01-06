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

    <!-- Sidebar Navigation (Mobile Drawer) -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white shadow-2xl z-40 transform -translate-x-full lg:hidden transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                <a href="/dashboard" class="text-xl font-bold text-blue-600">
                    <i class="fas fa-key mr-2"></i><?= APP_NAME ?>
                </a>
                <button id="close-sidebar-btn" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Menu Items -->
            <nav class="flex-1 overflow-y-auto p-4">
                <a href="/dashboard" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'bg-blue-50 text-blue-600' : '' ?>">
                    <i class="fas fa-chart-line w-5 mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/licenses" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/licenses') !== false ? 'bg-blue-50 text-blue-600' : '' ?>">
                    <i class="fas fa-certificate w-5 mr-3"></i>
                    <span>Lisensi</span>
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                <a href="/users" class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'bg-blue-50 text-blue-600' : '' ?>">
                    <i class="fas fa-users w-5 mr-3"></i>
                    <span>Users</span>
                </a>
                <?php endif; ?>
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

    <!-- Top Navigation (Desktop) -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/dashboard" class="text-xl font-bold text-blue-600">
                            <i class="fas fa-key mr-2"></i><?= APP_NAME ?>
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="/dashboard" class="<?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-chart-line mr-2"></i>Dashboard
                        </a>
                        <a href="/licenses" class="<?= strpos($_SERVER['REQUEST_URI'], '/licenses') !== false ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-certificate mr-2"></i>Lisensi
                        </a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                        <a href="/users" class="<?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-users mr-2"></i>Users
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" type="button" class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition">
                                    <i class="fas fa-user-circle text-xl mr-2"></i>
                                    <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                </span>
                            </button>
                        </div>
                        <div x-show="open" @click.away="open = false" x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                <?= htmlspecialchars($_SESSION['role'] ?? '') ?>
                            </div>
                            <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
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
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
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
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = ''; // Restore body scroll
        }

        // Open menu on hamburger button click
        mobileMenuBtn.addEventListener('click', openSidebar);

        // Close menu on close button click
        closeSidebarBtn.addEventListener('click', closeSidebar);

        // Close menu on overlay click
        overlay.addEventListener('click', closeSidebar);

        // Close menu on menu item click (mobile only)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
