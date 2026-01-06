<?php

class LicenseController {
    private $licenseModel;
    private $apiLogModel;

    public function __construct() {
        $this->licenseModel = new License();
        $this->apiLogModel = new ApiLog();
    }

    public function index() {
        AuthController::checkAuth();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $licenses = $this->licenseModel->getAll($page, PER_PAGE, $search);
        $total = $this->licenseModel->count($search);
        $totalPages = ceil($total / PER_PAGE);

        require_once APP_PATH . '/views/licenses/index.php';
    }

    public function create() {
        AuthController::checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $domain = $_POST['domain'] ?? '';
            $status = $_POST['status'] ?? 'active';
            $requestLimit = $_POST['request_limit'] ?? 1000;
            $expiresAt = $_POST['expires_at'] ?? null;

            if (empty($domain)) {
                $_SESSION['error'] = 'Domain harus diisi';
                header('Location: /licenses/create');
                exit;
            }

            // Validate domain format
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain)) {
                $_SESSION['error'] = 'Format domain tidak valid';
                header('Location: /licenses/create');
                exit;
            }

            $result = $this->licenseModel->create(
                $domain,
                $status,
                $requestLimit,
                $expiresAt ?: null,
                $_SESSION['user_id']
            );

            if ($result) {
                $_SESSION['success'] = 'Lisensi berhasil dibuat!';
                $_SESSION['new_api_key'] = $result['api_key'];
                header('Location: /licenses/show?id=' . $result['id']);
                exit;
            } else {
                $_SESSION['error'] = 'Gagal membuat lisensi';
                header('Location: /licenses/create');
                exit;
            }
        }

        require_once APP_PATH . '/views/licenses/create.php';
    }

    public function show() {
        AuthController::checkAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $license = $this->licenseModel->findById($id);

        if (!$license) {
            $_SESSION['error'] = 'Lisensi tidak ditemukan';
            header('Location: /licenses');
            exit;
        }

        // Get logs for this license
        $page = isset($_GET['log_page']) ? (int)$_GET['log_page'] : 1;
        $logs = $this->apiLogModel->getByLicenseId($id, $page, 10);
        $totalLogs = $this->apiLogModel->countByLicenseId($id);
        $totalLogPages = ceil($totalLogs / 10);

        // Get stats for last 30 days
        $stats = $this->apiLogModel->getStatsByLicense($id, 30);

        require_once APP_PATH . '/views/licenses/show.php';
    }

    public function edit() {
        AuthController::checkAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $license = $this->licenseModel->findById($id);

        if (!$license) {
            $_SESSION['error'] = 'Lisensi tidak ditemukan';
            header('Location: /licenses');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $domain = $_POST['domain'] ?? '';
            $status = $_POST['status'] ?? 'active';
            $requestLimit = $_POST['request_limit'] ?? 1000;
            $expiresAt = $_POST['expires_at'] ?? null;

            if (empty($domain)) {
                $_SESSION['error'] = 'Domain harus diisi';
                header('Location: /licenses/edit?id=' . $id);
                exit;
            }

            // Validate domain format
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $domain)) {
                $_SESSION['error'] = 'Format domain tidak valid';
                header('Location: /licenses/edit?id=' . $id);
                exit;
            }

            $result = $this->licenseModel->update($id, [
                'domain' => $domain,
                'status' => $status,
                'request_limit' => $requestLimit,
                'expires_at' => $expiresAt ?: null
            ]);

            if ($result) {
                $_SESSION['success'] = 'Lisensi berhasil diupdate!';
                header('Location: /licenses/show?id=' . $id);
                exit;
            } else {
                $_SESSION['error'] = 'Gagal mengupdate lisensi';
                header('Location: /licenses/edit?id=' . $id);
                exit;
            }
        }

        require_once APP_PATH . '/views/licenses/edit.php';
    }

    public function delete() {
        AuthController::checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($this->licenseModel->delete($id)) {
                $_SESSION['success'] = 'Lisensi berhasil dihapus!';
            } else {
                $_SESSION['error'] = 'Gagal menghapus lisensi';
            }
        }

        header('Location: /licenses');
        exit;
    }

    public function regenerateApiKey() {
        AuthController::checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            $newApiKey = $this->licenseModel->regenerateApiKey($id);

            if ($newApiKey) {
                $_SESSION['success'] = 'API Key berhasil di-regenerate!';
                $_SESSION['new_api_key'] = $newApiKey;
            } else {
                $_SESSION['error'] = 'Gagal me-regenerate API Key';
            }

            header('Location: /licenses/show?id=' . $id);
            exit;
        }

        header('Location: /licenses');
        exit;
    }

    public function resetRequestCount() {
        AuthController::checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            if ($this->licenseModel->resetRequestCount($id)) {
                $_SESSION['success'] = 'Request count berhasil direset!';
            } else {
                $_SESSION['error'] = 'Gagal mereset request count';
            }

            header('Location: /licenses/show?id=' . $id);
            exit;
        }

        header('Location: /licenses');
        exit;
    }
}
