<?php

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        AuthController::checkSuperAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $users = $this->userModel->getAll($page, PER_PAGE);
        $total = $this->userModel->count();
        $totalPages = ceil($total / PER_PAGE);

        require_once APP_PATH . '/views/users/index.php';
    }

    public function edit() {
        AuthController::checkSuperAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user = $this->userModel->findById($id);

        if (!$user) {
            $_SESSION['error'] = 'User tidak ditemukan';
            header('Location: /users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? 'admin';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($email)) {
                $_SESSION['error'] = 'Username dan email harus diisi';
                header('Location: /users/edit?id=' . $id);
                exit;
            }

            $data = [
                'username' => $username,
                'email' => $email,
                'role' => $role
            ];

            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $_SESSION['error'] = 'Password minimal 6 karakter';
                    header('Location: /users/edit?id=' . $id);
                    exit;
                }
                $data['password'] = $password;
            }

            $result = $this->userModel->update($id, $data);

            if ($result) {
                $_SESSION['success'] = 'User berhasil diupdate!';
                header('Location: /users');
                exit;
            } else {
                $_SESSION['error'] = 'Gagal mengupdate user';
                header('Location: /users/edit?id=' . $id);
                exit;
            }
        }

        require_once APP_PATH . '/views/users/edit.php';
    }

    public function delete() {
        AuthController::checkSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            // Prevent deleting self
            if ($id == $_SESSION['user_id']) {
                $_SESSION['error'] = 'Tidak dapat menghapus user yang sedang login';
                header('Location: /users');
                exit;
            }

            if ($this->userModel->delete($id)) {
                $_SESSION['success'] = 'User berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus user';
            }
        }

        header('Location: /users');
        exit;
    }
}
