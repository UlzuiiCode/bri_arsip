<?php

require_once BASE_PATH . '/config/database.php';

/**
 * Model Kategori
 * Mengambil dan mengelola data tabel `categories`.
 */
class CategoryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    /**
     * Ambil semua kategori, diurutkan berdasarkan nama.
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT id, nama, deskripsi FROM categories ORDER BY nama ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Ambil satu kategori berdasarkan ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, nama, deskripsi FROM categories WHERE id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
