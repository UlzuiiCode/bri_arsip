<?php

require_once BASE_PATH . '/models/CategoryModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Kategori
 */
class CategoryController
{
    private CategoryModel $catModel;
    private ActivityLogModel $logModel;

    public function __construct()
    {
        $this->catModel = new CategoryModel();
        $this->logModel = new ActivityLogModel();
    }

    public function index(): void
    {
        $categories = $this->catModel->getAll();
        $pageTitle  = 'Manajemen Kategori';
        require_once BASE_PATH . '/views/categories/index.php';
    }

    public function create(): void
    {
        $pageTitle = 'Tambah Kategori';
        require_once BASE_PATH . '/views/categories/create.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=categories');
            exit;
        }

        $nama      = trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_SPECIAL_CHARS));
        $deskripsi = trim(filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_SPECIAL_CHARS));

        if (empty($nama)) {
            $_SESSION['flash_error'] = 'Nama kategori wajib diisi.';
            header('Location: ' . BASE_URL . '/index.php?page=categories.create');
            exit;
        }

        if ($this->catModel->nameExists($nama)) {
            $_SESSION['flash_error'] = 'Nama kategori sudah ada.';
            header('Location: ' . BASE_URL . '/index.php?page=categories.create');
            exit;
        }

        $catId = $this->catModel->create($nama, $deskripsi);
        $this->logModel->log($_SESSION['user_id'], 'CREATE_CATEGORY', "Membuat kategori baru: '$nama'");
        $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan!';
        header('Location: ' . BASE_URL . '/index.php?page=categories');
        exit;
    }

    public function edit(int $id): void
    {
        $category  = $this->catModel->findById($id);
        $pageTitle = 'Edit Kategori';
        if (!$category) {
            $_SESSION['flash_error'] = 'Kategori tidak ditemukan.';
            header('Location: ' . BASE_URL . '/index.php?page=categories');
            exit;
        }
        require_once BASE_PATH . '/views/categories/edit.php';
    }

    public function update(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=categories');
            exit;
        }

        $nama      = trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_SPECIAL_CHARS));
        $deskripsi = trim(filter_input(INPUT_POST, 'deskripsi', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($this->catModel->nameExists($nama, $id)) {
            $_SESSION['flash_error'] = 'Nama kategori sudah digunakan.';
            header('Location: ' . BASE_URL . "/index.php?page=categories.edit&id=$id");
            exit;
        }

        $this->catModel->update($id, $nama, $deskripsi);
        $this->logModel->log($_SESSION['user_id'], 'UPDATE_CATEGORY', "Memperbarui kategori menjadi '$nama'");
        $_SESSION['flash_success'] = 'Kategori berhasil diperbarui!';
        header('Location: ' . BASE_URL . '/index.php?page=categories');
        exit;
    }

    public function delete(int $id): void
    {
        $category = $this->catModel->findById($id);
        $catName = $category ? $category['nama'] : 'Kategori';
        $this->catModel->delete($id);
        $this->logModel->log($_SESSION['user_id'], 'DELETE_CATEGORY', "Menghapus kategori: '$catName'");
        $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
        header('Location: ' . BASE_URL . '/index.php?page=categories');
        exit;
    }
}
