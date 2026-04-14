<?php

require_once BASE_PATH . '/models/UserModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Profil Pengguna
 */
class ProfileController
{
    private UserModel $userModel;
    private ActivityLogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new ActivityLogModel();
    }

    /**
     * Tampilkan halaman profil pengguna saat ini.
     */
    public function index(): void
    {
        $userId = $_SESSION['user_id'] ?? 0;
        if ($userId <= 0) {
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $_SESSION['flash_error'] = 'Data pengguna tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        $pageTitle = 'Profil Saya';
        require_once BASE_PATH . '/views/profile/index.php';
    }

    /**
     * Update data profil pengguna saat ini.
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=profile');
            exit;
        }

        // Validasi CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = 'Request tidak valid.';
            header('Location: ' . BASE_URL . '/index.php?page=profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $nama   = trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_SPECIAL_CHARS));
        $email  = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        $errors = [];
        if (empty($nama)) {
            $errors[] = 'Nama lengkap wajib diisi.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }
        if ($this->userModel->emailExists($email, $userId)) {
            $errors[] = 'Email sudah digunakan oleh pengguna lain.';
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                $errors[] = 'Password minimal 8 karakter.';
            }
            if ($password !== $confirm) {
                $errors[] = 'Konfirmasi password tidak cocok.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode('<br>', $errors);
            header('Location: ' . BASE_URL . '/index.php?page=profile');
            exit;
        }

        // Persiapkan data
        $data = [
            'nama'  => $nama,
            'email' => $email,
        ];
        if (!empty($password)) {
            // UserModel->update() secara otomatis akan men-hash password jika diberikan
            $data['password'] = $password;
        }

        // --- MANAGE UPLOAD FOTO PROFIL ---
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] !== UPLOAD_ERR_NO_FILE) {
            $fileResult = $this->handleFotoUpload('foto_profil');
            if (isset($fileResult['error'])) {
                $_SESSION['flash_error'] = $fileResult['error'];
                header('Location: ' . BASE_URL . '/index.php?page=profile');
                exit;
            }
            $data['foto_profil'] = $fileResult['file_name'];
            
            // Hapus foto lama jika ada
            $currentUser = $this->userModel->findById($userId);
            if (!empty($currentUser['foto_profil'])) {
                $oldPath = BASE_PATH . '/public/uploads/profiles/' . $currentUser['foto_profil'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            
            $_SESSION['user_foto'] = $data['foto_profil'];
        }

        // Update di database
        $this->userModel->update($userId, $data);

        // Update session jika nama berubah
        $_SESSION['user_nama'] = $nama;

        $this->logModel->log($userId, 'UPDATE_PROFILE', 'Pengguna memperbarui profilnya sendiri.');

        $_SESSION['flash_success'] = 'Profil berhasil diperbarui!';
        header('Location: ' . BASE_URL . '/index.php?page=profile');
        exit;
    }

    /**
     * Helper: Tangani upload foto
     */
    private function handleFotoUpload(string $fieldName): array
    {
        $file = $_FILES[$fieldName];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Gagal mengunggah foto.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed, true)) {
            return ['error' => 'Format foto tidak didukung (harus JPG, PNG, atau WEBP).'];
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            return ['error' => 'Ukuran foto maksimal 2MB.'];
        }

        $uploadDir = BASE_PATH . '/public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $safeName = uniqid('profile_') . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['error' => 'Gagal menyimpan foto di server.'];
        }

        return ['file_name' => $safeName];
    }
}
