<?php

require_once BASE_PATH . '/models/DocumentModel.php';
require_once BASE_PATH . '/models/CategoryModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Dokumen
 * Mendukung: CRUD, pagination, soft delete, trash, bulk ops, export
 */
class DocumentController
{
    private DocumentModel $docModel;
    private CategoryModel $catModel;
    private ActivityLogModel $logModel;

    public function __construct()
    {
        $this->docModel = new DocumentModel();
        $this->catModel = new CategoryModel();
        $this->logModel = new ActivityLogModel();
    }

    /**
     * Tampilkan daftar dokumen dengan pagination.
     */
    public function index(): void
    {
        $keyword    = trim($_GET['search'] ?? '');
        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $page       = max(1, (int) ($_GET['p'] ?? 1));
        $perPage    = 15;
        $offset     = ($page - 1) * $perPage;

        $documents  = $this->docModel->getAllPaginated($perPage, $offset, $keyword, $categoryId);
        $totalDocs  = $this->docModel->countFiltered($keyword, $categoryId);
        $totalPages = max(1, (int) ceil($totalDocs / $perPage));
        $categories = $this->catModel->getAll();

        require_once BASE_PATH . '/views/documents/index.php';
    }

    /**
     * Tampilkan form upload dokumen baru.
     */
    public function create(): void
    {
        $categories = $this->catModel->getAll();
        require_once BASE_PATH . '/views/documents/create.php';
    }

    /**
     * Proses upload dokumen baru.
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        // Validasi CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = 'Request tidak valid.';
            header('Location: ' . BASE_URL . '/index.php?page=documents.create');
            exit;
        }

        $judul       = trim(filter_input(INPUT_POST, 'judul', FILTER_SANITIZE_SPECIAL_CHARS));
        $deskripsi   = trim(filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_SPECIAL_CHARS));
        $categoryId  = (int) filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
        $nominal     = filter_input(INPUT_POST, 'nominal', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $pihakTerkait = trim(filter_input(INPUT_POST, 'pihak_terkait', FILTER_SANITIZE_SPECIAL_CHARS));
        $tanggalTransaksi = trim($_POST['tanggal_transaksi'] ?? '');

        // Validasi
        if (empty($judul) || $categoryId <= 0) {
            $_SESSION['flash_error'] = 'Judul dan kategori wajib diisi.';
            header('Location: ' . BASE_URL . '/index.php?page=documents.create');
            exit;
        }

        // Proses upload file
        $fileData = $this->handleFileUpload('file_dokumen');
        if (isset($fileData['error'])) {
            $_SESSION['flash_error'] = $fileData['error'];
            header('Location: ' . BASE_URL . '/index.php?page=documents.create');
            exit;
        }

        $docId = $this->docModel->create([
            'judul'        => $judul,
            'deskripsi'    => $deskripsi,
            'file_path'    => $fileData['file_path'],
            'file_name'    => $fileData['file_name'],
            'file_size'    => $fileData['file_size'],
            'file_type'    => $fileData['file_type'],
            'category_id'  => $categoryId,
            'uploaded_by'  => $_SESSION['user_id'],
            'nominal'      => $nominal ?: null,
            'pihak_terkait' => $pihakTerkait ?: null,
            'tanggal_transaksi' => $tanggalTransaksi ?: null,
        ]);

        $this->logModel->log(
            $_SESSION['user_id'],
            'UPLOAD_DOCUMENT',
            "Mengunggah dokumen '$judul'"
        );

        $_SESSION['flash_success'] = 'Dokumen berhasil diunggah!';
        header('Location: ' . BASE_URL . '/index.php?page=documents');
        exit;
    }

    /**
     * Tampilkan detail dokumen.
     */
    public function show(int $id): void
    {
        $document = $this->docModel->findById($id);
        if (!$document || $document['deleted_at'] !== null) {
            $_SESSION['flash_error'] = 'Dokumen tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }
        require_once BASE_PATH . '/views/documents/show.php';
    }

    /**
     * Tampilkan form edit dokumen.
     */
    public function edit(int $id): void
    {
        $document   = $this->docModel->findById($id);
        $categories = $this->catModel->getAll();
        if (!$document || $document['deleted_at'] !== null) {
            $_SESSION['flash_error'] = 'Dokumen tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }
        require_once BASE_PATH . '/views/documents/edit.php';
    }

    /**
     * Proses update dokumen.
     */
    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        // Validasi CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = 'Request tidak valid.';
            header('Location: ' . BASE_URL . "/index.php?page=documents.edit&id=$id");
            exit;
        }

        $judul       = trim(filter_input(INPUT_POST, 'judul', FILTER_SANITIZE_SPECIAL_CHARS));
        $deskripsi   = trim(filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_SPECIAL_CHARS));
        $categoryId  = (int) filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
        $nominal     = filter_input(INPUT_POST, 'nominal', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $pihakTerkait = trim(filter_input(INPUT_POST, 'pihak_terkait', FILTER_SANITIZE_SPECIAL_CHARS));
        $tanggalTransaksi = trim($_POST['tanggal_transaksi'] ?? '');

        $this->docModel->update($id, [
            'judul'        => $judul,
            'deskripsi'    => $deskripsi,
            'category_id'  => $categoryId,
            'nominal'      => $nominal ?: null,
            'pihak_terkait' => $pihakTerkait ?: null,
            'tanggal_transaksi' => $tanggalTransaksi ?: null,
        ]);

        $this->logModel->log(
            $_SESSION['user_id'],
            'UPDATE_DOCUMENT',
            "Memperbarui dokumen '$judul'"
        );

        $_SESSION['flash_success'] = 'Dokumen berhasil diperbarui!';
        header('Location: ' . BASE_URL . '/index.php?page=documents');
        exit;
    }

    /**
     * Soft delete dokumen (pindah ke sampah).
     */
    public function delete(int $id): void
    {
        $document = $this->docModel->findById($id);
        if ($document && $document['deleted_at'] === null) {
            $this->docModel->softDelete($id);
            $this->logModel->log(
                $_SESSION['user_id'],
                'DELETE_DOCUMENT',
                "Memindahkan dokumen '{$document['judul']}' ke sampah"
            );
        }
        $_SESSION['flash_success'] = 'Dokumen dipindahkan ke tempat sampah.';
        header('Location: ' . BASE_URL . '/index.php?page=documents');
        exit;
    }

    /**
     * Download dokumen.
     */
    public function download(int $id): void
    {
        $document = $this->docModel->findById($id);
        if (!$document) {
            die('Dokumen tidak ditemukan.');
        }

        $filePath = BASE_PATH . '/public/uploads/' . $document['file_path'];
        if (!file_exists($filePath)) {
            die('File tidak ditemukan di server.');
        }

        $this->logModel->log(
            $_SESSION['user_id'],
            'DOWNLOAD_DOCUMENT',
            "Mengunduh dokumen '{$document['judul']}'"
        );

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($document['file_name']) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        readfile($filePath);
        exit;
    }

    // ===================================================
    // TRASH (Tempat Sampah)
    // ===================================================

    /**
     * Tampilkan halaman tempat sampah.
     */
    public function trash(): void
    {
        $trashedDocuments = $this->docModel->getTrashed();
        require_once BASE_PATH . '/views/documents/trash.php';
    }

    /**
     * Restore dokumen dari sampah.
     */
    public function restore(int $id): void
    {
        $document = $this->docModel->findById($id);
        if ($document && $document['deleted_at'] !== null) {
            $this->docModel->restore($id);
            $this->logModel->log(
                $_SESSION['user_id'],
                'RESTORE_DOCUMENT',
                "Memulihkan dokumen '{$document['judul']}' dari sampah"
            );
        }
        $_SESSION['flash_success'] = 'Dokumen berhasil dipulihkan!';
        header('Location: ' . BASE_URL . '/index.php?page=documents.trash');
        exit;
    }

    /**
     * Hapus permanen dari sampah.
     */
    public function forceDelete(int $id): void
    {
        $document = $this->docModel->findById($id);
        if ($document) {
            // Hapus file fisik
            $filePath = BASE_PATH . '/public/uploads/' . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->docModel->forceDelete($id);
            $this->logModel->log(
                $_SESSION['user_id'],
                'FORCE_DELETE_DOCUMENT',
                "Menghapus permanen dokumen '{$document['judul']}'"
            );
        }
        $_SESSION['flash_success'] = 'Dokumen dihapus secara permanen.';
        header('Location: ' . BASE_URL . '/index.php?page=documents.trash');
        exit;
    }

    // ===================================================
    // BULK ACTIONS & EMPTY TRASH
    // ===================================================

    /**
     * Kosongkan tempat sampah permanen.
     */
    public function emptyTrash(): void
    {
        $trashed = $this->docModel->getTrashed();
        if (!empty($trashed)) {
            $count = 0;
            foreach ($trashed as $doc) {
                $filePath = BASE_PATH . '/public/uploads/' . $doc['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $this->docModel->forceDelete((int)$doc['id']);
                $count++;
            }
            $this->logModel->log(
                $_SESSION['user_id'],
                'EMPTY_TRASH',
                "Mengosongkan tempat sampah secara permanen ($count dokumen)"
            );
        }
        $_SESSION['flash_success'] = 'Tempat sampah berhasil dikosongkan.';
        header('Location: ' . BASE_URL . '/index.php?page=documents.trash');
        exit;
    }

    /**
     * Bulk soft delete.
     */
    public function bulkDelete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['flash_error'] = 'Request tidak valid.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $ids = $_POST['doc_ids'] ?? [];
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn($id) => $id > 0);

        if (empty($ids)) {
            $_SESSION['flash_error'] = 'Tidak ada dokumen yang dipilih.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $count = $this->docModel->softDeleteMultiple($ids);
        $this->logModel->log(
            $_SESSION['user_id'],
            'BULK_DELETE_DOCUMENT',
            "Memindahkan $count dokumen sekaligus ke tempat sampah"
        );

        $_SESSION['flash_success'] = "$count dokumen dipindahkan ke tempat sampah.";
        header('Location: ' . BASE_URL . '/index.php?page=documents');
        exit;
    }

    /**
     * Bulk download sebagai ZIP.
     */
    public function bulkDownload(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $ids = $_POST['doc_ids'] ?? [];
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn($id) => $id > 0);

        if (empty($ids)) {
            $_SESSION['flash_error'] = 'Tidak ada dokumen yang dipilih.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $documents = $this->docModel->getByIds($ids);
        if (empty($documents)) {
            $_SESSION['flash_error'] = 'Dokumen tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        // Cek ekstensi ZIP
        if (!class_exists('ZipArchive')) {
            $_SESSION['flash_error'] = 'Ekstensi ZIP tidak tersedia di server.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $zipName = 'dokumen_arsip_' . date('Ymd_His') . '.zip';
        $zipPath = sys_get_temp_dir() . '/' . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $_SESSION['flash_error'] = 'Gagal membuat file ZIP.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $addedCount = 0;
        foreach ($documents as $doc) {
            $filePath = BASE_PATH . '/public/uploads/' . $doc['file_path'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $doc['file_name']);
                $addedCount++;
            }
        }
        $zip->close();

        if ($addedCount === 0) {
            unlink($zipPath);
            $_SESSION['flash_error'] = 'Tidak ada file yang ditemukan di server.';
            header('Location: ' . BASE_URL . '/index.php?page=documents');
            exit;
        }

        $this->logModel->log(
            $_SESSION['user_id'],
            'BULK_DOWNLOAD',
            "Mengunduh $addedCount dokumen dalam format ZIP"
        );

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipPath));
        header('Cache-Control: no-cache, must-revalidate');
        readfile($zipPath);
        unlink($zipPath);
        exit;
    }

    // ===================================================
    // EXPORT
    // ===================================================

    /**
     * Export daftar dokumen sebagai CSV.
     */
    public function exportCsv(): void
    {
        $search     = trim($_GET['search'] ?? '');
        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $documents  = $this->docModel->getAllForExport($search, $categoryId);

        $this->logModel->log(
            $_SESSION['user_id'],
            'EXPORT_CSV',
            'Mengekspor daftar dokumen ke CSV (' . count($documents) . ' dokumen)'
        );

        $filename = 'laporan_dokumen_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, ['No', 'Judul', 'Kategori', 'Deskripsi', 'Nominal (Rp)', 'Pihak Terkait', 'Tanggal Aktivitas', 'Pengunggah', 'Tanggal Upload', 'Nama File', 'Ukuran (KB)']);

        foreach ($documents as $i => $doc) {
            fputcsv($output, [
                $i + 1,
                $doc['judul'],
                $doc['nama_kategori'] ?? 'Tanpa Kategori',
                $doc['deskripsi'] ?? '',
                $doc['nominal'] ? number_format($doc['nominal'], 0, ',', '.') : '',
                $doc['pihak_terkait'] ?? '',
                $doc['tanggal_transaksi'] ? date('d/m/Y', strtotime($doc['tanggal_transaksi'])) : '',
                $doc['nama_uploader'] ?? '-',
                date('d/m/Y H:i', strtotime($doc['created_at'])),
                $doc['file_name'],
                $doc['file_size'] ? round($doc['file_size'] / 1024, 2) : '',
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export laporan PDF (menggunakan tampilan HTML siap cetak).
     */
    public function exportPdf(): void
    {
        $search     = trim($_GET['search'] ?? '');
        $categoryId = (int) ($_GET['category_id'] ?? 0);
        $documents  = $this->docModel->getAllForExport($search, $categoryId);
        $categories = $this->catModel->getAll();

        $this->logModel->log(
            $_SESSION['user_id'],
            'EXPORT_PDF',
            'Mengekspor laporan PDF (' . count($documents) . ' dokumen)'
        );

        require_once BASE_PATH . '/views/documents/export_pdf.php';
    }

    // ===================================================
    // HELPER
    // ===================================================

    /**
     * Helper menangani upload file.
     */
    private function handleFileUpload(string $fieldName): array
    {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File gagal diunggah atau tidak dipilih.'];
        }

        $file     = $_FILES[$fieldName];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ALLOWED_EXTENSIONS;

        if (!in_array($ext, $allowed, true)) {
            return ['error' => 'Ekstensi file tidak diizinkan. Diizinkan: ' . implode(', ', $allowed)];
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            return ['error' => 'Ukuran file melebihi batas maksimum 5MB.'];
        }

        $uploadDir = UPLOAD_PATH;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $safeName = uniqid('doc_', true) . '.' . $ext;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['error' => 'Gagal menyimpan file.'];
        }

        return [
            'file_path' => $safeName,
            'file_name' => basename($file['name']),
            'file_size' => $file['size'],
            'file_type' => $file['type'],
        ];
    }
}
