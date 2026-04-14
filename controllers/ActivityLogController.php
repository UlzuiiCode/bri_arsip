<?php

require_once BASE_PATH . '/models/ActivityLogModel.php';

/**
 * Controller Log Aktivitas
 */
class ActivityLogController
{
    private ActivityLogModel $logModel;

    public function __construct()
    {
        $this->logModel = new ActivityLogModel();
    }

    private function requireAdmin(): void
    {
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = 'Akses ditolak.';
            header('Location: ' . BASE_URL . '/index.php?page=dashboard');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int) ($_GET['p'] ?? 1));
        $perPage = 25;
        $offset  = ($page - 1) * $perPage;

        $logs      = $this->logModel->getAll($perPage, $offset);
        $total     = $this->logModel->countAll();
        $pageTitle = 'Log Aktivitas';

        require_once BASE_PATH . '/views/activity_logs/index.php';
    }
}
