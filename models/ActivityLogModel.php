<?php

require_once BASE_PATH . '/config/database.php';

/**
 * Model Log Aktivitas
 */
class ActivityLogModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    /**
     * Catat aktivitas baru.
     */
    public function log(int $userId, string $action, string $description, string $ipAddress = ''): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO activity_logs (user_id, action, description, ip_address)
             VALUES (:user_id, :action, :description, :ip_address)"
        );
        $stmt->execute([
            ':user_id'     => $userId,
            ':action'      => $action,
            ':description' => $description,
            ':ip_address'  => $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
        ]);
    }

    /**
     * Ambil semua log dengan informasi user.
     */
    public function getAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT al.*, u.nama AS nama_pengguna, u.role AS role_pengguna
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil log berdasarkan user tertentu.
     */
    public function getByUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM activity_logs WHERE user_id = :user_id
             ORDER BY created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil log berdasarkan user tertentu dengan pagination.
     */
    public function getByUserPaginated(int $userId, int $limit = 25, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM activity_logs WHERE user_id = :user_id
             ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Hitung total log.
     */
    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
    }

    /**
     * Hitung total log milik user tertentu.
     */
    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM activity_logs WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
