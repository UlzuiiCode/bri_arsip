<?php

/**
 * ============================================================
 * index.php — Entry Point Sistem Manajemen Arsip
 * Bertindak sebagai Front Controller sederhana.
 *
 * URL Contoh:
 *   index.php?page=dashboard
 *   index.php?page=documents
 *   index.php?page=documents.create
 *   index.php?page=login
 * ============================================================
 */

// Load konfigurasi aplikasi (session, timezone, constants)
require_once __DIR__ . '/config/app.php';

// Generate CSRF token jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ===================================================
// ROUTING
// ===================================================

$page   = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'dashboard';
$id     = (int) ($_GET['id'] ?? 0);

/**
 * Middleware: Proteksi halaman yang memerlukan autentikasi.
 * Daftar halaman publik (tidak perlu login).
 */
$publicPages = ['login', 'login.process', 'register', 'register.process'];

if (!in_array($page, $publicPages, true) && !isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
    header('Location: ' . BASE_URL . '/index.php?page=login');
    exit;
}

/**
 * Dispatch ke Controller yang sesuai.
 */
switch ($page) {

    // ===== AUTH =====
    case 'login':
        require_once BASE_PATH . '/controllers/AuthController.php';
        (new AuthController())->showLogin();
        break;

    case 'login.process':
        require_once BASE_PATH . '/controllers/AuthController.php';
        (new AuthController())->processLogin();
        break;

    case 'register':
        require_once BASE_PATH . '/controllers/AuthController.php';
        (new AuthController())->showRegister();
        break;

    case 'register.process':
        require_once BASE_PATH . '/controllers/AuthController.php';
        (new AuthController())->processRegister();
        break;

    case 'logout':
        require_once BASE_PATH . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;


    // ===== DASHBOARD =====
    case 'dashboard':
        require_once BASE_PATH . '/controllers/DashboardController.php';
        (new DashboardController())->index();
        break;


    // ===== DOCUMENTS =====
    case 'documents':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->index();
        break;

    case 'documents.create':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->create();
        break;

    case 'documents.store':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->store();
        break;

    case 'documents.show':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->show($id);
        break;

    case 'documents.edit':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->edit($id);
        break;

    case 'documents.update':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->update($id);
        break;

    case 'documents.delete':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->delete($id);
        break;

    case 'documents.download':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->download($id);
        break;

    case 'documents.trash':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->trash();
        break;

    case 'documents.restore':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->restore($id);
        break;

    case 'documents.force_delete':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->forceDelete($id);
        break;

    case 'documents.empty_trash':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->emptyTrash();
        break;

    case 'documents.bulk_delete':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->bulkDelete();
        break;

    case 'documents.bulk_download':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->bulkDownload();
        break;

    case 'documents.export_csv':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->exportCsv();
        break;

    case 'documents.export_pdf':
        require_once BASE_PATH . '/controllers/DocumentController.php';
        (new DocumentController())->exportPdf();
        break;


    // ===== USERS (Admin Only) =====
    case 'users':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->index();
        break;

    case 'users.create':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->create();
        break;

    case 'users.store':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->store();
        break;

    case 'users.edit':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->edit($id);
        break;

    case 'users.update':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->update($id);
        break;

    case 'users.delete':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->delete($id);
        break;

    case 'users.pending':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->pending();
        break;

    case 'users.approve':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->approve($id);
        break;

    case 'users.reject':
        require_once BASE_PATH . '/controllers/UserController.php';
        (new UserController())->reject($id);
        break;


    // ===== CATEGORIES =====
    case 'categories':
        require_once BASE_PATH . '/controllers/CategoryController.php';
        (new CategoryController())->index();
        break;

    case 'categories.create':
        require_once BASE_PATH . '/controllers/CategoryController.php';
        (new CategoryController())->create();
        break;

    case 'categories.store':
        require_once BASE_PATH . '/controllers/CategoryController.php';
        (new CategoryController())->store();
        break;

    case 'categories.edit':
        require_once BASE_PATH . '/controllers/CategoryController.php';
        (new CategoryController())->edit($id);
        break;

    case 'categories.update':
        require_once BASE_PATH . '/controllers/CategoryController.php';
        (new CategoryController())->update($id);
        break;

    case 'categories.delete':
        require_once BASE_PATH . '/controllers/CategoryController.php';
        (new CategoryController())->delete($id);
        break;


    // ===== ACTIVITY LOGS =====
    case 'activity_logs':
        require_once BASE_PATH . '/controllers/ActivityLogController.php';
        (new ActivityLogController())->index();
        break;

    // ===== PROFILE =====
    case 'profile':
        require_once BASE_PATH . '/controllers/ProfileController.php';
        (new ProfileController())->index();
        break;

    case 'profile.update':
        require_once BASE_PATH . '/controllers/ProfileController.php';
        (new ProfileController())->update();
        break;


    // ===== 404 DEFAULT =====
    default:
        http_response_code(404);
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>404 — Halaman Tidak Ditemukan</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
            <style>body{font-family:'Inter',sans-serif;}</style>
        </head>
        <body class="flex min-h-screen items-center justify-center bg-slate-50 p-6">
            <div class="text-center">
                <p class="text-9xl font-black text-slate-200">404</p>
                <h1 class="mt-4 text-2xl font-bold text-slate-900">Halaman Tidak Ditemukan</h1>
                <p class="mt-2 text-slate-500">Halaman <code class="rounded bg-slate-100 px-2 py-0.5"><?= htmlspecialchars($page) ?></code> tidak tersedia.</p>
                <a href="<?= BASE_URL ?>/index.php?page=dashboard"
                   class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </body>
        </html>
        <?php
        break;
}
