<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-user-plus text-5xl text-blue-600 mb-4"></i>
                    <h2 class="text-3xl font-bold text-gray-900">Daftar Akun Baru</h2>
                    <p class="text-gray-600 mt-2">Buat akun admin baru</p>
                </div>

                <?php if (isset($_SESSION['error'])): ?>
                <div x-data="{ show: true }" x-show="show" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?= htmlspecialchars($_SESSION['error']) ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="/register" method="POST">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-user mr-2"></i>Username
                        </label>
                        <input type="text" id="username" name="username" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan username">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan email">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Minimal 6 karakter">
                    </div>

                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-2"></i>Konfirmasi Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ulangi password">
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>Daftar
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Sudah punya akun?
                        <a href="/login" class="text-blue-600 hover:text-blue-800 font-semibold">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
