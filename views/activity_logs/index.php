<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="space-y-5 animate-fade-in">
    <div>
        <h2 class="text-xl font-bold text-slate-900">Log Aktivitas</h2>
        <p class="text-sm text-slate-500 mt-0.5">Riwayat lengkap semua aktivitas pengguna</p>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <?php if (empty($logs)): ?>
        <div class="py-16 text-center">
            <p class="text-slate-400">Belum ada log aktivitas.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pengguna</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Deskripsi</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">IP</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">
                                    <?= strtoupper(substr($log['nama_pengguna'] ?? 'S', 0, 1)) ?>
                                </div>
                                <span class="text-slate-700 font-medium"><?= htmlspecialchars($log['nama_pengguna'] ?? 'Sistem') ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                                <?php
                                $action = $log['action'] ?? '';

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

                                if (str_contains($action, 'LOGIN'))    echo 'bg-green-50 text-green-700';
                                elseif (str_contains($action, 'DELETE')) echo 'bg-red-50 text-red-700';
                                elseif (str_contains($action, 'CREATE') || str_contains($action, 'UPLOAD')) echo 'bg-blue-50 text-blue-700';
                                elseif (str_contains($action, 'UPDATE')) echo 'bg-amber-50 text-amber-700';
                                else echo 'bg-slate-100 text-slate-600';
                                ?>">
                                <?= htmlspecialchars($actionLabel) ?>
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-slate-500 hidden md:table-cell max-w-xs">
                            <p class="truncate"><?= htmlspecialchars($log['description'] ?? '-') ?></p>
                        </td>
                        <td class="px-6 py-3.5 text-slate-400 text-xs font-mono hidden lg:table-cell">
                            <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                        </td>
                        <td class="px-6 py-3.5 text-slate-500 text-xs whitespace-nowrap">
                            <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($total) && $total > $perPage): ?>
        <div class="border-t border-slate-100 px-6 py-4 flex items-center justify-between">
            <p class="text-xs text-slate-500">
                Menampilkan <?= min($perPage, $total) ?> dari <?= $total ?> entri
            </p>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                <a href="?page=activity_logs&p=<?= $page - 1 ?>"
                   class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    &larr; Prev
                </a>
                <?php endif; ?>
                <?php if ($page * $perPage < $total): ?>
                <a href="?page=activity_logs&p=<?= $page + 1 ?>"
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
