<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-key text-5xl text-blue-600 mb-4"></i>
                    <h2 class="text-3xl font-bold text-gray-900"><?= APP_NAME ?></h2>
                    <p class="text-gray-600 mt-2">Masuk ke akun Anda</p>
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

                <?php if (isset($_SESSION['success'])): ?>
                <div x-data="{ show: true }" x-show="show" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700"><?= htmlspecialchars($_SESSION['success']) ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <form action="/login" method="POST">
                    <div class="mb-6">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-user mr-2"></i>Username
                        </label>
                        <input type="text" id="username" name="username" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan username">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Masukkan password">
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Belum punya akun?
                        <a href="/register" class="text-blue-600 hover:text-blue-800 font-semibold">Daftar di sini</a>
                    </p>
                </div>
            </div>
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Default login: admin / admin123
                </p>
            </div>
        </div>
    </div>
</body>
</html>
