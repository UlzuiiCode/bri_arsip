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
        $sql = "SELECT d.*, u.nama AS nama_uploader
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.deleted_at IS NULL
                ORDER BY COALESCE(d.tanggal_transaksi, d.created_at) DESC";

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
    public function getAllPaginated(int $limit, int $offset, string $search = '', ?int $categoryId = null): array
    {
        $sql = "SELECT d.*, u.nama AS nama_uploader, c.nama AS category_nama
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                LEFT JOIN categories c ON d.category_id = c.id
                WHERE d.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.judul LIKE :s1 OR d.deskripsi LIKE :s2)";
            $params[':s1'] = '%' . $search . '%';
            $params[':s2'] = '%' . $search . '%';
        }

        if ($categoryId !== null) {
            $sql .= " AND d.category_id = :cat_id";
            $params[':cat_id'] = $categoryId;
        }

        $sql .= " ORDER BY COALESCE(d.tanggal_transaksi, d.created_at) DESC LIMIT :limit OFFSET :offset";

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
    public function countFiltered(string $search = '', ?int $categoryId = null): int
    {
        $sql = "SELECT COUNT(*) FROM documents d
                WHERE d.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.judul LIKE :s1 OR d.deskripsi LIKE :s2)";
            $params[':s1'] = '%' . $search . '%';
            $params[':s2'] = '%' . $search . '%';
        }

        if ($categoryId !== null) {
            $sql .= " AND d.category_id = :cat_id";
            $params[':cat_id'] = $categoryId;
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
            "SELECT d.*, u.nama AS nama_pengunggah
             FROM documents d
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
            "SELECT d.*, u.nama AS nama_uploader
             FROM documents d
             LEFT JOIN users u ON d.uploaded_by = u.id
             WHERE d.deleted_at IS NULL
               AND (d.judul LIKE :k1 OR d.deskripsi LIKE :k2)
             ORDER BY COALESCE(d.tanggal_transaksi, d.created_at) DESC"
        );
        $searchKeyword = '%' . $keyword . '%';
        $stmt->bindValue(':k1', $searchKeyword, PDO::PARAM_STR);
        $stmt->bindValue(':k2', $searchKeyword, PDO::PARAM_STR);
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
            "SELECT d.*, u.nama AS nama_uploader
             FROM documents d
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
            "SELECT d.*, u.nama AS nama_uploader, c.nama AS category_nama
             FROM documents d
             LEFT JOIN users u ON d.uploaded_by = u.id
             LEFT JOIN categories c ON d.category_id = c.id
             WHERE d.deleted_at IS NULL
             ORDER BY COALESCE(d.tanggal_transaksi, d.created_at) DESC LIMIT :limit"
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
            "INSERT INTO documents (judul, deskripsi, file_path, file_name, file_size, file_type, uploaded_by, category_id, nominal, pihak_terkait, tanggal_transaksi)
             VALUES (:judul, :deskripsi, :file_path, :file_name, :file_size, :file_type, :uploaded_by, :category_id, :nominal, :pihak_terkait, :tanggal_transaksi)"
        );
        $stmt->execute([
            ':judul'        => $data['judul'],
            ':deskripsi'    => $data['deskripsi'] ?? null,
            ':file_path'    => $data['file_path'],
            ':file_name'    => $data['file_name'],
            ':file_size'    => $data['file_size'] ?? null,
            ':file_type'    => $data['file_type'] ?? null,
            ':uploaded_by'  => $data['uploaded_by'],
            ':category_id'  => $data['category_id'] ?? null,
            ':nominal'      => $data['nominal'] ?? null,
            ':pihak_terkait' => $data['pihak_terkait'] ?? null,
            ':tanggal_transaksi' => $data['tanggal_transaksi'] ?? null,
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
                tanggal_transaksi = :tanggal_transaksi,
                updated_at = NOW()
             WHERE id = :id"
        );
        return $stmt->execute([
            ':judul'         => $data['judul'],
            ':deskripsi'     => $data['deskripsi'] ?? null,
            ':category_id'   => $data['category_id'] ?? null,
            ':nominal'       => $data['nominal'] ?? null,
            ':pihak_terkait' => $data['pihak_terkait'] ?? null,
            ':tanggal_transaksi' => $data['tanggal_transaksi'] ?? null,
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
            "SELECT d.*, u.nama AS nama_uploader
             FROM documents d
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
    public function getAllForExport(string $search = ''): array
    {
        $sql = "SELECT d.*, u.nama AS nama_uploader
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.deleted_at IS NULL";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.judul LIKE :s1 OR d.deskripsi LIKE :s2)";
            $params[':s1'] = '%' . $search . '%';
            $params[':s2'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY COALESCE(d.tanggal_transaksi, d.created_at) DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
