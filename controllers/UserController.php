<?php

require_once BASE_PATH . '/models/UserModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Pengguna (Admin only)
 */
class UserController
{
    private UserModel $userModel;
    private ActivityLogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new ActivityLogModel();
    }

    /**
     * Pastikan hanya admin yang bisa mengakses.
     */
    private function requireAdmin(): void
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash_error'] = 'Anda tidak memiliki akses ke halaman ini.';
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();
        $users = $this->userModel->getAll();
        require_once BASE_PATH . '/views/users/index.php';
    }

    public function create(): void
    {
        $this->requireAdmin();
        require_once BASE_PATH . '/views/users/create.php';
    }

    public function store(): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;
        }

        $nama     = trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_SPECIAL_CHARS));
        $email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role'], ['admin', 'pegawai']) ? $_POST['role'] : 'pegawai';

        if (empty($nama) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Semua field wajib diisi.';
            header('Location: ' . BASE_URL . '/index.php?page=users.create');
            exit;
        }

        if ($this->userModel->emailExists($email)) {
            $_SESSION['flash_error'] = 'Email sudah terdaftar.';
            header('Location: ' . BASE_URL . '/index.php?page=users.create');
            exit;
        }

        $userId = $this->userModel->create($nama, $email, $password, $role, 'approved');
        $this->logModel->log($_SESSION['user_id'], 'CREATE_USER', "Membuat pengguna baru: $email (role: $role)");

        $_SESSION['flash_success'] = 'Pengguna berhasil dibuat!';
        header('Location: ' . BASE_URL . '/index.php?page=users');
        exit;
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;
        }
        require_once BASE_PATH . '/views/users/edit.php';
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;
        }

        $data = [
            'nama'  => trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_SPECIAL_CHARS)),
            'email' => trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)),
            'role'  => in_array($_POST['role'], ['admin', 'pegawai']) ? $_POST['role'] : 'pegawai',
        ];

        $this->userModel->update($id, $data);
        $this->logModel->log($_SESSION['user_id'], 'UPDATE_USER', "Memperbarui pengguna ID: $id");

        $_SESSION['flash_success'] = 'Pengguna berhasil diperbarui!';
        header('Location: ' . BASE_URL . '/index.php?page=users');
        exit;
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        if ($id === (int) $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Anda tidak bisa menghapus akun sendiri.';
            header('Location: ' . BASE_URL . '/index.php?page=users');
            exit;
        }
        $this->userModel->delete($id);
        $this->logModel->log($_SESSION['user_id'], 'DELETE_USER', "Menghapus pengguna ID: $id");
        $_SESSION['flash_success'] = 'Pengguna berhasil dihapus.';
        header('Location: ' . BASE_URL . '/index.php?page=users');
        exit;
    }

    /**
     * Tampilkan daftar akun pending.
     */
    public function pending(): void
    {
        $this->requireAdmin();
        $pendingUsers = $this->userModel->getPending();
        $pageTitle = 'Persetujuan Akun';
        require_once BASE_PATH . '/views/users/pending.php';
    }

    /**
     * Setujui akun pending.
     */
    public function approve(int $id): void
    {
        $this->requireAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=users.pending');
            exit;
        }

        $this->userModel->approve($id);
        $this->logModel->log($_SESSION['user_id'], 'APPROVE_USER', "Menyetujui akun: {$user['email']}");

        $_SESSION['flash_success'] = "Akun {$user['nama']} berhasil disetujui!";
        header('Location: ' . BASE_URL . '/index.php?page=users.pending');
        exit;
    }

    /**
     * Tolak/hapus akun pending.
     */
    public function reject(int $id): void
    {
        $this->requireAdmin();
        $user = $this->userModel->findById($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'Pengguna tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=users.pending');
            exit;
        }

        $this->userModel->delete($id);
        $this->logModel->log($_SESSION['user_id'], 'REJECT_USER', "Menolak akun: {$user['email']}");

        $_SESSION['flash_success'] = "Akun {$user['nama']} berhasil ditolak.";
        header('Location: ' . BASE_URL . '/index.php?page=users.pending');
        exit;
    }
}
