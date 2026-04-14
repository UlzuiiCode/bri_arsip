<?php

require_once BASE_PATH . '/models/UserModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Autentikasi
 * Dengan rate limiting untuk mencegah brute force.
 */
class AuthController
{
    private UserModel $userModel;
    private ActivityLogModel $logModel;

    /** Konfigurasi rate limiting */
    private const MAX_ATTEMPTS    = 5;   // Maksimal percobaan
    private const LOCKOUT_MINUTES = 15;  // Durasi lockout (menit)

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->logModel  = new ActivityLogModel();
    }

    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;
        }

        // Cek apakah sedang terkunci (untuk tampilkan countdown)
        $lockoutRemaining = 0;
        $email = $_SESSION['last_login_email'] ?? '';
        if (!empty($email)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $attempts = $this->userModel->getRecentAttempts($email, $ip, self::LOCKOUT_MINUTES);
            if ($attempts >= self::MAX_ATTEMPTS) {
                $lastAttempt = $this->userModel->getLastAttemptTime($email, $ip);
                if ($lastAttempt) {
                    $unlockTime = strtotime($lastAttempt) + (self::LOCKOUT_MINUTES * 60);
                    $lockoutRemaining = max(0, $unlockTime - time());
                }
            }
        }

        require_once BASE_PATH . '/views/auth/login.php';
    }

    /**
     * Proses form login dengan rate limiting.
     */
    public function processLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = 'Request tidak valid (CSRF).';
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        $email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Simpan email untuk tampilan lockout
        $_SESSION['last_login_email'] = $email;

        if (empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Email dan password wajib diisi.';
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        // --- RATE LIMITING ---
        $attempts = $this->userModel->getRecentAttempts($email, $ip, self::LOCKOUT_MINUTES);
        if ($attempts >= self::MAX_ATTEMPTS) {
            $lastAttempt = $this->userModel->getLastAttemptTime($email, $ip);
            $unlockTime = strtotime($lastAttempt) + (self::LOCKOUT_MINUTES * 60);
            $remaining = $unlockTime - time();

            if ($remaining > 0) {
                $mins = ceil($remaining / 60);
                $_SESSION['flash_error'] = "Terlalu banyak percobaan login. Coba lagi dalam {$mins} menit.";
                header('Location: ' . BASE_URL . '/index.php?page=login');
                exit;
            }
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            // Catat percobaan login gagal
            $this->userModel->recordLoginAttempt($email, $ip);

            // Log ke activity_logs
            try {
                $stmt = (new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                    DB_USER, DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                ))->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (NULL, 'LOGIN_FAILED', :desc, :ip)");
                $stmt->execute([':desc' => "Percobaan login gagal untuk email: $email", ':ip' => $ip]);
            } catch (Exception $e) { /* abaikan error log */ }

            // Hitung sisa percobaan
            $newAttempts = $attempts + 1;
            $remaining = self::MAX_ATTEMPTS - $newAttempts;
            if ($remaining > 0) {
                $_SESSION['flash_error'] = "Email atau password salah. Sisa {$remaining} percobaan.";
            } else {
                $_SESSION['flash_error'] = "Akun terkunci selama " . self::LOCKOUT_MINUTES . " menit karena terlalu banyak percobaan.";
            }

            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        // Cek apakah akun sudah disetujui
        if ($user['status'] === 'pending') {
            $_SESSION['flash_error'] = 'Akun Anda belum disetujui oleh admin. Silakan tunggu persetujuan.';
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }

        // Login berhasil — hapus catatan percobaan
        $this->userModel->clearLoginAttempts($email, $ip);
        unset($_SESSION['last_login_email']);

        // Bersihkan data attempt lama secara periodik
        $this->userModel->purgeOldAttempts();

        // Regenerasi session ID untuk mencegah session fixation
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_foto'] = $user['foto_profil'];

        $this->logModel->log($user['id'], 'LOGIN', 'Pengguna berhasil login.');

        header('Location: ' . BASE_URL . '/index.php?page=dashboard');
        exit;
    }

    /**
     * Proses logout.
     */
    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->logModel->log($_SESSION['user_id'], 'LOGOUT', 'Pengguna logout.');
        }
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegister(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;
        }
        require_once BASE_PATH . '/views/auth/register.php';
    }

    /**
     * Proses form registrasi.
     */
    public function processRegister(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=register');
            exit;
        }

        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = 'Request tidak valid.';
            header('Location: ' . BASE_URL . '/index.php?page=register');
            exit;
        }

        $nama     = trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_SPECIAL_CHARS));
        $email    = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        // Validasi input
        $errors = [];
        if (empty($nama))     $errors[] = 'Nama wajib diisi.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
        if (strlen($password) < 8) $errors[] = 'Password minimal 8 karakter.';
        if ($password !== $confirm)  $errors[] = 'Konfirmasi password tidak cocok.';
        if ($this->userModel->emailExists($email)) $errors[] = 'Email sudah terdaftar.';

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode('<br>', $errors);
            header('Location: ' . BASE_URL . '/index.php?page=register');
            exit;
        }

        $userId = $this->userModel->create($nama, $email, $password, 'pegawai');
        $this->logModel->log($userId, 'REGISTER', "Akun baru dibuat: $email");

        $_SESSION['flash_success'] = 'Akun berhasil dibuat! Silakan tunggu persetujuan dari admin.';
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }
}
