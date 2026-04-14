<?php

require_once BASE_PATH . '/config/database.php';

/**
 * Model Dokumen
 * Menangani semua operasi database terkait tabel `documents`.
 * Mendukung soft delete, pagination, dan bulk operations.
 */
class DocumentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    // ===================================================
    // READ — Daftar & Pencarian (exclude soft-deleted)
    // ===================================================

    /**
     * Ambil semua dokumen aktif (belum dihapus) beserta nama kategori (JOIN).
     */
    public function getAll(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
                FROM documents d
                LEFT JOIN categories c ON d.category_id = c.id
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.deleted_at IS NULL
                ORDER BY d.created_at DESC";

        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->db->query($sql);
        }

        return $stmt->fetchAll();
    }

    /**
     * Ambil dokumen aktif dengan pagination, search, dan filter kategori.
     */
    public function getAllPaginated(int $limit, int $offset, string $search = '', int $categoryId = 0): array
    {
        $sql = "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
                FROM documents d
                LEFT JOIN categories c ON d.category_id = c.id
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.judul LIKE :search OR d.deskripsi LIKE :search OR c.nama LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($categoryId > 0) {
            $sql .= " AND d.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $sql .= " ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Hitung total dokumen aktif dengan search dan filter.
     */
    public function countFiltered(string $search = '', int $categoryId = 0): int
    {
        $sql = "SELECT COUNT(*) FROM documents d
                LEFT JOIN categories c ON d.category_id = c.id
                WHERE d.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.judul LIKE :search OR d.deskripsi LIKE :search OR c.nama LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($categoryId > 0) {
            $sql .= " AND d.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil dokumen berdasarkan ID (termasuk soft-deleted untuk keperluan restore).
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_pengunggah
             FROM documents d
             LEFT JOIN categories c ON d.category_id = c.id
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.id = :id LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Cari dokumen aktif berdasarkan judul atau nama kategori.
     */
    public function search(string $keyword): array
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
             FROM documents d
             LEFT JOIN categories c ON d.category_id = c.id
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.deleted_at IS NULL
               AND (d.judul LIKE :keyword OR d.deskripsi LIKE :keyword OR c.nama LIKE :keyword)
             ORDER BY d.created_at DESC"
        );
        $keyword = '%' . $keyword . '%';
        $stmt->bindValue(':keyword', $keyword, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil dokumen aktif berdasarkan kategori.
     */
    public function getByCategory(int $categoryId): array
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
             FROM documents d
             LEFT JOIN categories c ON d.category_id = c.id
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.deleted_at IS NULL AND d.category_id = :category_id
             ORDER BY d.created_at DESC"
        );
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil dokumen berdasarkan array ID.
     */
    public function getByIds(array $ids): array
    {
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
             FROM documents d
             LEFT JOIN categories c ON d.category_id = c.id
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.id IN ($placeholders)"
        );
        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, (int) $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil dokumen yang baru diunggah (aktif).
     */
    public function getRecent(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
             FROM documents d
             LEFT JOIN categories c ON d.category_id = c.id
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.deleted_at IS NULL
             ORDER BY d.created_at DESC LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ===================================================
    // CREATE & UPDATE
    // ===================================================

    /**
     * Buat dokumen baru.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO documents (judul, deskripsi, file_path, file_name, file_size, file_type, category_id, uploaded_by, nominal, pihak_terkait)
             VALUES (:judul, :deskripsi, :file_path, :file_name, :file_size, :file_type, :category_id, :uploaded_by, :nominal, :pihak_terkait)"
        );
        $stmt->execute([
            ':judul'        => $data['judul'],
            ':deskripsi'    => $data['deskripsi'] ?? null,
            ':file_path'    => $data['file_path'],
            ':file_name'    => $data['file_name'],
            ':file_size'    => $data['file_size'] ?? null,
            ':file_type'    => $data['file_type'] ?? null,
            ':category_id'  => $data['category_id'],
            ':uploaded_by'  => $data['uploaded_by'],
            ':nominal'      => $data['nominal'] ?? null,
            ':pihak_terkait' => $data['pihak_terkait'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update data dokumen.
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE documents SET
                judul = :judul,
                deskripsi = :deskripsi,
                category_id = :category_id,
                nominal = :nominal,
                pihak_terkait = :pihak_terkait,
                updated_at = NOW()
             WHERE id = :id"
        );
        return $stmt->execute([
            ':judul'         => $data['judul'],
            ':deskripsi'     => $data['deskripsi'] ?? null,
            ':category_id'   => $data['category_id'],
            ':nominal'       => $data['nominal'] ?? null,
            ':pihak_terkait' => $data['pihak_terkait'] ?? null,
            ':id'            => $id,
        ]);
    }

    // ===================================================
    // SOFT DELETE & TRASH
    // ===================================================

    /**
     * Soft delete: pindahkan ke tempat sampah.
     */
    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE documents SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Soft delete banyak dokumen sekaligus.
     */
    public function softDeleteMultiple(array $ids): int
    {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare(
            "UPDATE documents SET deleted_at = NOW() WHERE id IN ($placeholders) AND deleted_at IS NULL"
        );
        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, (int) $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Restore dokumen dari tempat sampah.
     */
    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE documents SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Hapus permanen dari database.
     */
    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM documents WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Ambil dokumen yang sudah di-soft delete (trash).
     */
    public function getTrashed(): array
    {
        return $this->db->query(
            "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
             FROM documents d
             LEFT JOIN categories c ON d.category_id = c.id
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.deleted_at IS NOT NULL
             ORDER BY d.deleted_at DESC"
        )->fetchAll();
    }

    /**
     * Hitung dokumen di tempat sampah.
     */
    public function countTrashed(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM documents WHERE deleted_at IS NOT NULL")->fetchColumn();
    }

    // ===================================================
    // COUNT
    // ===================================================

    /**
     * Hitung total dokumen aktif.
     */
    public function countAll(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM documents WHERE deleted_at IS NULL")->fetchColumn();
    }

    /**
     * Ambil semua dokumen aktif untuk export (tanpa pagination).
     */
    public function getAllForExport(string $search = '', int $categoryId = 0): array
    {
        $sql = "SELECT d.*, c.nama AS nama_kategori, u.nama AS nama_uploader
                FROM documents d
                LEFT JOIN categories c ON d.category_id = c.id
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.judul LIKE :search OR d.deskripsi LIKE :search OR c.nama LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($categoryId > 0) {
            $sql .= " AND d.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
