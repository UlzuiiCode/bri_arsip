<?php
/**
 * Sidebar Navigation Layout
 * Digunakan di semua halaman yang memerlukan autentikasi.
 */

$currentPage = $_GET['page'] ?? 'dashboard';

// Menentukan apakah link aktif
function isActive(string $page, string $currentPage): string {
    return $currentPage === $page
        ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25'
        : 'text-slate-300 hover:bg-sidebar-hover hover:text-white';
}

function isGroupActive(array $pages, string $currentPage): bool {
    return in_array($currentPage, $pages, true);
}
?>

<!-- ===== SIDEBAR ===== -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-sidebar transition-transform duration-300 ease-in-out
              lg:static lg:translate-x-0"
       aria-label="Sidebar navigasi">

    <!-- Logo & Brand -->
    <div class="flex h-16 items-center justify-between border-b border-slate-700/60 px-5">
        <a href="<?= BASE_URL ?>/index.php?page=dashboard"
           class="flex items-center gap-3 group" id="sidebar-brand-link">
            <div class="flex h-10 px-2.5 items-center justify-center rounded-lg bg-white shadow-lg shadow-white/10 transition-transform group-hover:scale-105">
                <img src="<?= BASE_URL ?>/public/bri_logo.svg" alt="BRI" class="h-5">
            </div>
            <div>
                <p class="text-sm font-bold leading-none text-white">Arsip BRI</p>
                <p class="text-xs text-slate-400 mt-0.5">Manajemen Arsip</p>
            </div>
        </a>
        <!-- Tombol tutup sidebar (mobile) -->
        <button id="sidebar-close-btn"
                class="lg:hidden rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors"
                aria-label="Tutup sidebar">
            <i data-feather="x" class="h-5 w-5"></i>
        </button>
    </div>

    <!-- Profil Pengguna Aktif -->
    <div class="border-b border-slate-700/60 px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-white text-sm font-bold shadow-md flex-shrink-0">
                <?= strtoupper(substr($_SESSION['user_nama'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-white"><?= htmlspecialchars($_SESSION['user_nama'] ?? '-') ?></p>
                <p class="text-xs text-slate-400 capitalize"><?= htmlspecialchars($_SESSION['user_role'] ?? '-') ?></p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 scrollbar-thin" aria-label="Menu navigasi utama">
        <p class="mb-2 px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Utama</p>

        <ul class="space-y-1" role="list">
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=dashboard"
                   id="nav-dashboard"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('dashboard', $currentPage) ?>">
                    <i data-feather="home" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Dokumen dengan sub-menu -->
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=documents"
                   id="nav-documents"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('documents', $currentPage) ?>">
                    <i data-feather="file-text" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Dokumen</span>
                </a>
            </li>

            <li>
                <a href="<?= BASE_URL ?>/index.php?page=documents.create"
                   id="nav-documents-upload"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('documents.create', $currentPage) ?>">
                    <i data-feather="upload" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Upload Dokumen</span>
                </a>
            </li>

            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
            <?php
                if (!class_exists('DocumentModel')) {
                    require_once BASE_PATH . '/models/DocumentModel.php';
                }
                $sidebarTrashCount = (new DocumentModel())->countTrashed();
            ?>
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=documents.trash"
                   id="nav-documents-trash"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('documents.trash', $currentPage) ?>">
                    <i data-feather="trash-2" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Sampah</span>
                    <?php if ($sidebarTrashCount > 0): ?>
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-slate-500 px-1.5 text-xs font-bold text-white">
                        <?= $sidebarTrashCount ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endif; ?>

            <li>
                <a href="<?= BASE_URL ?>/index.php?page=activity_logs.my"
                   id="nav-my-activity"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('activity_logs.my', $currentPage) ?>">
                    <i data-feather="clock" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Riwayat Saya</span>
                </a>
            </li>

        </ul>

        <?php if (($_SESSION['user_role'] ?? '') === 'admin'):
            // Get pending count for sidebar badge
            if (!class_exists('UserModel')) {
                require_once BASE_PATH . '/models/UserModel.php';
            }
            $sidebarPendingCount = (new UserModel())->countPending();
        ?>
        <p class="mb-2 mt-6 px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">Administrasi</p>
        <ul class="space-y-1" role="list">
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=users"
                   id="nav-users"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('users', $currentPage) ?>">
                    <i data-feather="users" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Pengguna</span>
                    <?php if ($sidebarPendingCount > 0): ?>
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-500 px-1.5 text-xs font-bold text-white shadow-sm">
                        <?= $sidebarPendingCount ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if ($sidebarPendingCount > 0): ?>
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=users.pending"
                   id="nav-users-pending"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('users.pending', $currentPage) ?>">
                    <i data-feather="user-check" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Persetujuan Akun</span>
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-bold text-white animate-pulse shadow-sm">
                        <?= $sidebarPendingCount ?>
                    </span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?= BASE_URL ?>/index.php?page=activity_logs"
                   id="nav-activity-logs"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150 <?= isActive('activity_logs', $currentPage) ?>">
                    <i data-feather="activity" class="h-4 w-4 flex-shrink-0"></i>
                    <span>Log Aktivitas</span>
                </a>
            </li>
        </ul>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer / Logout -->
    <div class="border-t border-slate-700/60 px-3 py-3">
        <a href="<?= BASE_URL ?>/index.php?page=logout"
           id="nav-logout"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400
                  hover:bg-red-500/10 hover:text-red-400 transition-all duration-150">
            <i data-feather="log-out" class="h-4 w-4 flex-shrink-0"></i>
            <span>Keluar</span>
        </a>
    </div>
</aside>

<!-- Overlay untuk mobile -->
<div id="sidebar-overlay"
     class="fixed inset-0 z-40 hidden bg-black/60 backdrop-blur-sm lg:hidden"
     aria-hidden="true"></div>

<!-- ===== MAIN CONTENT WRAPPER ===== -->
<div class="flex flex-1 flex-col overflow-hidden">

    <!-- Top Navbar -->
    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200
                   bg-white/90 backdrop-blur-md px-4 shadow-sm lg:px-6">
        <!-- Hamburger (mobile) -->
        <button id="sidebar-open-btn"
                class="lg:hidden rounded-lg p-2 text-slate-500 hover:bg-slate-100 transition-colors"
                aria-label="Buka sidebar">
            <i data-feather="menu" class="h-5 w-5"></i>
        </button>

        <!-- Page Title -->
        <h1 class="text-base font-semibold text-slate-800 lg:text-lg">
            <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
        </h1>

        <!-- Right: Notification + Profile -->
        <div class="flex items-center gap-3">
            <!-- Notifikasi -->
            <div class="relative" id="notif-wrapper">
                <button class="relative rounded-lg p-2 text-slate-500 hover:bg-slate-100 transition-colors"
                        aria-label="Notifikasi" id="notification-btn">
                    <i data-feather="bell" class="h-5 w-5"></i>
                    <span id="notif-badge" class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500 hidden"></span>
                </button>

                <!-- Notification Dropdown -->
                <div id="notif-dropdown"
                     class="absolute right-0 mt-2 hidden w-80 origin-top-right rounded-2xl bg-white
                            p-0 shadow-xl shadow-slate-200/80 ring-1 ring-slate-100 animate-fade-in overflow-hidden"
                     style="z-index: 100;">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-semibold text-slate-800">Notifikasi</h3>
                        <button id="notif-mark-read" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">Tandai semua dibaca</button>
                    </div>
                    <div id="notif-list" class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                        <div class="px-4 py-6 text-center text-sm text-slate-400">
                            <i data-feather="loader" class="h-5 w-5 mx-auto mb-2 animate-spin"></i>
                            Memuat...
                        </div>
                    </div>
                    <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/index.php?page=activity_logs"
                       class="block text-center px-4 py-2.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 border-t border-slate-100 transition-colors">
                        Lihat semua aktivitas &rarr;
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Avatar Dropdown -->
            <div class="relative" id="user-menu-wrapper">
                <button id="user-menu-btn"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br
                               from-primary-400 to-primary-600 text-sm font-bold text-white shadow-md
                               hover:shadow-primary-300/50 transition-all overflow-hidden"
                        aria-expanded="false" aria-haspopup="true">
                        <?= strtoupper(substr($_SESSION['user_nama'] ?? 'U', 0, 1)) ?>
                </button>

                <!-- Dropdown -->
                <div id="user-dropdown"
                     class="absolute right-0 mt-2 hidden w-52 origin-top-right rounded-2xl bg-white
                            p-1.5 shadow-xl shadow-slate-200/80 ring-1 ring-slate-100 animate-fade-in"
                     role="menu" aria-orientation="vertical">
                    <div class="px-4 py-2.5 border-b border-slate-100 mb-1">
                        <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($_SESSION['user_nama'] ?? '') ?></p>
                        <p class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></p>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?page=logout"
                       class="flex items-center gap-2.5 rounded-xl px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                       role="menuitem" id="dropdown-logout">
                        <i data-feather="log-out" class="h-4 w-4"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages via SweetAlert2 -->
    <?php if (isset($_SESSION['flash_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= addslashes(htmlspecialchars($_SESSION['flash_success'])) ?>',
                confirmButtonColor: '#2563eb',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Perhatian!',
                text: '<?= addslashes(htmlspecialchars($_SESSION['flash_error'])) ?>',
                confirmButtonColor: '#2563eb'
            });
        });
    </script>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <!-- ===== PAGE CONTENT STARTS HERE ===== -->
    <main class="flex-1 overflow-y-auto p-4 lg:p-6">
