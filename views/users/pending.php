<?php
$pageTitle = 'Persetujuan Akun';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="space-y-5 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Persetujuan Akun</h2>
            <p class="text-sm text-slate-500 mt-0.5"><?= count($pendingUsers) ?> akun menunggu persetujuan</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=users"
           id="btn-kembali-pengguna"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <?php if (empty($pendingUsers)): ?>
    <!-- Empty State -->
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-50 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Semua akun sudah diproses</h3>
            <p class="text-sm text-slate-500 mt-1">Tidak ada akun yang menunggu persetujuan saat ini.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="pending-users-table">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Tanggal Daftar</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($pendingUsers as $i => $pu): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-slate-400"><?= $i + 1 ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-xs font-bold text-white flex-shrink-0">
                                    <?= strtoupper(substr($pu['nama'], 0, 1)) ?>
                                </div>
                                <span class="font-medium text-slate-800"><?= htmlspecialchars($pu['nama']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($pu['email']) ?></td>
                        <td class="px-6 py-4 text-slate-400 text-xs hidden lg:table-cell">
                            <?= date('d M Y, H:i', strtotime($pu['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/50">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= BASE_URL ?>/index.php?page=users.approve&id=<?= $pu['id'] ?>"
                                   onclick="return confirm('Setujui akun <?= htmlspecialchars(addslashes($pu['nama'])) ?>?')"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-100 transition-colors ring-1 ring-green-200/50"
                                   title="Setujui">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=users.reject&id=<?= $pu['id'] ?>"
                                   onclick="return confirm('Tolak dan hapus akun <?= htmlspecialchars(addslashes($pu['nama'])) ?>? Tindakan ini tidak bisa dibatalkan.')"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors ring-1 ring-red-200/50"
                                   title="Tolak">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tolak
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
