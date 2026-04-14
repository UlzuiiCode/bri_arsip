<?php

require_once BASE_PATH . '/config/database.php';

/**
 * Model Pengguna
 * Menangani semua operasi database terkait tabel `users`.
 * Termasuk rate limiting untuk login.
 */
class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    /**
     * Temukan pengguna berdasarkan email (untuk login).
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT id, nama, email, password, role, status, foto_profil FROM users WHERE email = :email LIMIT 1"
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Temukan pengguna berdasarkan ID.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT id, nama, email, role, foto_profil, created_at FROM users WHERE id = :id LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Ambil semua pengguna.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT id, nama, email, role, status, foto_profil, created_at FROM users WHERE status = 'approved' ORDER BY created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua pengguna yang menunggu persetujuan.
     */
    public function getPending(): array
    {
        $stmt = $this->db->query(
            "SELECT id, nama, email, role, foto_profil, created_at FROM users WHERE status = 'pending' ORDER BY created_at ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Hitung jumlah pengguna pending.
     */
    public function countPending(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
    }

    /**
     * Setujui akun pengguna (ubah status ke approved).
     */
    public function approve(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET status = 'approved', updated_at = NOW() WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Buat pengguna baru.
     */
    public function create(string $nama, string $email, string $password, string $role = 'pegawai', string $status = 'pending'): int
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (nama, email, password, role, status) VALUES (:nama, :email, :password, :role, :status)"
        );
        $stmt->bindValue(':nama', $nama, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update data pengguna.
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (!empty($data['nama'])) {
            $fields[] = 'nama = :nama';
            $params[':nama'] = $data['nama'];
        }
        if (!empty($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        if (!empty($data['role'])) {
            $fields[] = 'role = :role';
            $params[':role'] = $data['role'];
        }
        if (!empty($data['password'])) {
            $fields[] = 'password = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (isset($data['foto_profil'])) {
            $fields[] = 'foto_profil = :foto_profil';
            $params[':foto_profil'] = $data['foto_profil'];
        }

        if (empty($fields)) return false;

        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Hapus pengguna.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Cek apakah email sudah terdaftar.
     */
    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM users WHERE email = :email AND id != :exclude_id"
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Hitung total pengguna.
     */
    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    // ===================================================
    // RATE LIMITING
    // ===================================================

    /**
     * Catat percobaan login gagal.
     */
    public function recordLoginAttempt(string $email, string $ip): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)"
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
    }

    /**
     * Hitung percobaan login gagal dalam N menit terakhir.
     */
    public function getRecentAttempts(string $email, string $ip, int $minutes = 15): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE email = :email AND ip_address = :ip
               AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)"
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':minutes', $minutes, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil waktu percobaan login terakhir.
     */
    public function getLastAttemptTime(string $email, string $ip): ?string
    {
        $stmt = $this->db->prepare(
            "SELECT attempted_at FROM login_attempts
             WHERE email = :email AND ip_address = :ip
             ORDER BY attempted_at DESC LIMIT 1"
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        $result = $stmt->fetchColumn();
        return $result ?: null;
    }

    /**
     * Hapus catatan percobaan login (setelah login berhasil).
     */
    public function clearLoginAttempts(string $email, string $ip): void
    {
        $stmt = $this->db->prepare(
            "DELETE FROM login_attempts WHERE email = :email AND ip_address = :ip"
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
    }

    /**
     * Bersihkan data login_attempts yang sudah expired (lebih dari 1 jam).
     */
    public function purgeOldAttempts(): void
    {
        $this->db->exec("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    }
}
