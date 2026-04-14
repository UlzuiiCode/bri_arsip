<?php

require_once BASE_PATH . '/config/database.php';

/**
 * Model Kategori
 */
class CategoryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    public function getAll(): array
    {
        return $this->db->query(
            "SELECT c.*, COUNT(d.id) AS total_dokumen
             FROM categories c
             LEFT JOIN documents d ON c.id = d.category_id
             GROUP BY c.id
             ORDER BY c.nama ASC"
        )->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create(string $nama, string $deskripsi = ''): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO categories (nama, deskripsi) VALUES (:nama, :deskripsi)"
        );
        $stmt->execute([':nama' => $nama, ':deskripsi' => $deskripsi]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama, string $deskripsi = ''): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE categories SET nama = :nama, deskripsi = :deskripsi, updated_at = NOW() WHERE id = :id"
        );
        return $stmt->execute([':nama' => $nama, ':deskripsi' => $deskripsi, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    }

    public function nameExists(string $nama, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM categories WHERE nama = :nama AND id != :exclude_id"
        );
        $stmt->execute([':nama' => $nama, ':exclude_id' => $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
