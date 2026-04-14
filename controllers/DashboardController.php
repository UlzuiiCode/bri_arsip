<?php

require_once BASE_PATH . '/models/UserModel.php';
require_once BASE_PATH . '/models/DocumentModel.php';
require_once BASE_PATH . '/models/CategoryModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Dashboard
 */
class DashboardController
{
    private UserModel $userModel;
    private DocumentModel $docModel;
    private CategoryModel $catModel;
    private ActivityLogModel $logModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->docModel  = new DocumentModel();
        $this->catModel  = new CategoryModel();
        $this->logModel  = new ActivityLogModel();
    }

    /**
     * Tampilkan halaman dashboard.
     */
    public function index(): void
    {
        $stats = [
            'total_dokumen'   => $this->docModel->countAll(),
            'total_kategori'  => $this->catModel->countAll(),
            'total_pengguna'  => $this->userModel->countAll(),
            'total_aktivitas' => $this->logModel->countAll(),
            'total_sampah'    => $this->docModel->countTrashed(),
        ];

        $recentDocuments = $this->docModel->getRecent(5);
        $recentLogs      = $this->logModel->getAll(8);
        $categories      = $this->catModel->getAll();

        require_once BASE_PATH . '/views/dashboard/index.php';
    }
}
