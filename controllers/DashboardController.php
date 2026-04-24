<?php

require_once BASE_PATH . '/models/UserModel.php';
require_once BASE_PATH . '/models/DocumentModel.php';
require_once BASE_PATH . '/models/DocumentModel.php';
require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Dashboard
 */
class DashboardController
{
    private UserModel $userModel;
    private DocumentModel $docModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->docModel  = new DocumentModel();
        $this->logModel  = new ActivityLogModel();
    }

    /**
     * Tampilkan halaman dashboard.
     */
    public function index(): void
    {
        $isAdmin  = (($_SESSION['user_role'] ?? '') === 'admin');
        $userId   = (int) ($_SESSION['user_id'] ?? 0);

        $stats = [
            'total_dokumen'   => $this->docModel->countAll(),
            'total_pengguna'  => $this->userModel->countAll(),
            'total_aktivitas' => $isAdmin
                ? $this->logModel->countAll()
                : $this->logModel->countByUser($userId),
            'total_sampah'    => $this->docModel->countTrashed(),
        ];

        $recentDocuments = $this->docModel->getRecent(5);
        $recentLogs      = $isAdmin
            ? $this->logModel->getAll(8)
            : $this->logModel->getByUser($userId, 8);

        require_once BASE_PATH . '/views/dashboard/index.php';
    }
}
