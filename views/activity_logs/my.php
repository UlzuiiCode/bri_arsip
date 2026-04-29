<?php
$pageTitle = $pageTitle ?? 'Riwayat Aktivitas Saya';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';

// Group logs by date
$groupedLogs = [];
foreach ($logs as $log) {
    $date = date('Y-m-d', strtotime($log['created_at']));
    $groupedLogs[$date][] = $log;
}
?>

<div class="space-y-6 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Riwayat Aktivitas Saya</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Semua aktivitas Anda tercatat di sini &mdash; total <strong><?= $total ?></strong> aktivitas
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <?php
        // Count by action type
        $loginCount = 0; $uploadCount = 0; $editCount = 0; $deleteCount = 0;
        foreach ($logs as $l) {
            $act = $l['action'] ?? '';
            if (str_contains($act, 'LOGIN') || str_contains($act, 'LOGOUT')) $loginCount++;
            elseif (str_contains($act, 'UPLOAD') || str_contains($act, 'CREATE')) $uploadCount++;
            elseif (str_contains($act, 'UPDATE')) $editCount++;
            elseif (str_contains($act, 'DELETE') || str_contains($act, 'RESTORE')) $deleteCount++;
        }
        ?>
        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-4 ring-1 ring-emerald-200/50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-700"><?= $loginCount ?></p>
                    <p class="text-xs text-emerald-600/70 font-medium">Login / Logout</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 ring-1 ring-blue-200/50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-700"><?= $uploadCount ?></p>
                    <p class="text-xs text-blue-600/70 font-medium">Upload</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/50 p-4 ring-1 ring-amber-200/50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-700"><?= $editCount ?></p>
                    <p class="text-xs text-amber-600/70 font-medium">Edit</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-red-50 to-red-100/50 p-4 ring-1 ring-red-200/50">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-700"><?= $deleteCount ?></p>
                    <p class="text-xs text-red-600/70 font-medium">Hapus / Restore</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Activity -->
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <?php if (empty($logs)): ?>
        <div class="py-16 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-slate-800 font-semibold text-lg">Belum ada aktivitas</p>
            <p class="text-sm text-slate-500 mt-1">Aktivitas Anda akan muncul di sini.</p>
        </div>
        <?php else: ?>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500" style="width:50px">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Deskripsi</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">IP Address</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($logs as $i => $log): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-3.5 text-slate-400"><?= ($page - 1) * $perPage + $i + 1 ?></td>
                        <td class="px-6 py-3.5">
                            <?php
                            $action = $log['action'] ?? '';
                            $iconSvg = '';
                            $badgeClass = '';

                            // Mapping nama aksi ke bahasa sederhana
                            $actionLabels = [
                                'LOGIN'              => 'Masuk',
                                'LOGOUT'             => 'Keluar',
                                'REGISTER'           => 'Daftar Akun',
                                'LOGIN_FAILED'       => 'Gagal Masuk',
                                'UPLOAD_DOCUMENT'    => 'Unggah Dokumen',
                                'UPDATE_DOCUMENT'    => 'Edit Dokumen',
                                'DELETE_DOCUMENT'    => 'Hapus Dokumen',
                                'RESTORE_DOCUMENT'   => 'Pulihkan Dokumen',
                                'FORCE_DELETE_DOCUMENT' => 'Hapus Permanen',
                                'DOWNLOAD_DOCUMENT'  => 'Unduh Dokumen',
                                'BULK_DELETE_DOCUMENT' => 'Hapus Massal',
                                'BULK_DOWNLOAD'      => 'Unduh Massal',
                                'EMPTY_TRASH'        => 'Kosongkan Sampah',
                                'EXPORT_CSV'         => 'Ekspor CSV',
                                'EXPORT_PDF'         => 'Ekspor PDF',
                                'APPROVE_USER'       => 'Setujui Akun',
                                'REJECT_USER'        => 'Tolak Akun',
                                'CREATE_USER'        => 'Tambah Pengguna',
                                'UPDATE_USER'        => 'Edit Pengguna',
                                'DELETE_USER'        => 'Hapus Pengguna',
                                'UPDATE_PROFILE'     => 'Edit Profil',
                            ];
                            $actionLabel = $actionLabels[$action] ?? $action;

                            if (str_contains($action, 'LOGIN')) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 ring-emerald-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>';
                            } elseif (str_contains($action, 'LOGOUT')) {
                                $badgeClass = 'bg-slate-100 text-slate-600 ring-slate-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>';
                            } elseif (str_contains($action, 'DELETE')) {
                                $badgeClass = 'bg-red-50 text-red-700 ring-red-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>';
                            } elseif (str_contains($action, 'UPLOAD') || str_contains($action, 'CREATE')) {
                                $badgeClass = 'bg-blue-50 text-blue-700 ring-blue-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>';
                            } elseif (str_contains($action, 'UPDATE')) {
                                $badgeClass = 'bg-amber-50 text-amber-700 ring-amber-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>';
                            } elseif (str_contains($action, 'RESTORE')) {
                                $badgeClass = 'bg-teal-50 text-teal-700 ring-teal-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>';
                            } elseif (str_contains($action, 'DOWNLOAD') || str_contains($action, 'EXPORT')) {
                                $badgeClass = 'bg-indigo-50 text-indigo-700 ring-indigo-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>';
                            } elseif (str_contains($action, 'REGISTER')) {
                                $badgeClass = 'bg-purple-50 text-purple-700 ring-purple-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>';
                            } elseif (str_contains($action, 'APPROVE') || str_contains($action, 'REJECT')) {
                                $badgeClass = 'bg-cyan-50 text-cyan-700 ring-cyan-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                            } else {
                                $badgeClass = 'bg-slate-100 text-slate-600 ring-slate-200/50';
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                            }
                            ?>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 <?= $badgeClass ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <?= $iconSvg ?>
                                </svg>
                                <?= htmlspecialchars($actionLabel) ?>
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-slate-500 hidden md:table-cell max-w-xs">
                            <p class="truncate"><?= htmlspecialchars($log['description'] ?? '-') ?></p>
                        </td>
                        <td class="px-6 py-3.5 text-slate-400 text-xs font-mono hidden lg:table-cell">
                            <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-3.5 text-xs whitespace-nowrap">
                            <p class="text-slate-700 font-medium"><?= date('d M Y', strtotime($log['created_at'])) ?></p>
                            <p class="text-slate-400"><?= date('H:i', strtotime($log['created_at'])) ?> WIB</p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php $totalPages = max(1, (int) ceil($total / $perPage)); ?>
        <?php if ($totalPages > 1): ?>
        <div class="border-t border-slate-100 px-6 py-4 flex items-center justify-between">
            <p class="text-xs text-slate-500">
                Halaman <strong><?= $page ?></strong> dari <strong><?= $totalPages ?></strong>
                &mdash; <?= $total ?> total aktivitas
            </p>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                <a href="?page=activity_logs.my&p=<?= $page - 1 ?>"
                   class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    &larr; Prev
                </a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                <a href="?page=activity_logs.my&p=<?= $page + 1 ?>"
                   class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-500 transition-colors">
                    Next &rarr;
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
