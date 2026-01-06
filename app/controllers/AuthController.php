<?php

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Username dan password harus diisi';
                header('Location: /login');
                exit;
            }

            $user = $this->userModel->findByUsername($username);

            if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success'] = 'Login berhasil!';
                header('Location: /dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Username atau password salah';
                header('Location: /login');
                exit;
            }
        }

        require_once APP_PATH . '/views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                $_SESSION['error'] = 'Semua field harus diisi';
                header('Location: /register');
                exit;
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Password tidak cocok';
                header('Location: /register');
                exit;
            }

            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password minimal 6 karakter';
                header('Location: /register');
                exit;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Format email tidak valid';
                header('Location: /register');
                exit;
            }

            // Check if username exists
            if ($this->userModel->findByUsername($username)) {
                $_SESSION['error'] = 'Username sudah digunakan';
                header('Location: /register');
                exit;
            }

            // Check if email exists
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Email sudah digunakan';
                header('Location: /register');
                exit;
            }

            // Create user
            $userId = $this->userModel->create($username, $email, $password, 'admin');

            if ($userId) {
                $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
                header('Location: /login');
                exit;
            } else {
                $_SESSION['error'] = 'Registrasi gagal. Silakan coba lagi.';
                header('Location: /register');
                exit;
            }
        }

        require_once APP_PATH . '/views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }

    public static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Silakan login terlebih dahulu';
            header('Location: /login');
            exit;
        }
    }

    public static function checkSuperAdmin() {
        self::checkAuth();
        if ($_SESSION['role'] !== 'super_admin') {
            $_SESSION['error'] = 'Akses ditolak. Hanya super admin yang dapat mengakses halaman ini.';
            header('Location: /dashboard');
            exit;
        }
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
