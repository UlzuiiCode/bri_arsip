<?php
$pageTitle = 'Tempat Sampah';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="space-y-5 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Tempat Sampah</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                <?= count($trashedDocuments) ?> dokumen tersimpan di sampah
            </p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=documents"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Dokumen
        </a>
    </div>

    <?php if (!empty($trashedDocuments)): ?>
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 flex items-start gap-2.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-sm text-amber-800">
            Dokumen di tempat sampah dapat dipulihkan kapan saja. <strong>Hapus permanen</strong> akan menghilangkan file dari server secara irreversible.
        </p>
    </div>
    <?php endif; ?>

    <!-- Trashed Documents Table -->
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <?php if (empty($trashedDocuments)): ?>
        <div class="py-16 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-slate-800 font-semibold text-lg">Tempat sampah kosong</p>
            <p class="text-sm text-slate-500 mt-1">Tidak ada dokumen yang dihapus saat ini.</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="trash-table">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Judul</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Kategori</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Dihapus Pada</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($trashedDocuments as $i => $doc): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-slate-400"><?= $i + 1 ?></td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800 line-clamp-1"><?= htmlspecialchars($doc['judul']) ?></p>
                            <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($doc['file_name']) ?></p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                <?= htmlspecialchars($doc['nama_kategori'] ?? 'Tanpa Kategori') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs hidden lg:table-cell">
                            <?= date('d M Y, H:i', strtotime($doc['deleted_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= BASE_URL ?>/index.php?page=documents.restore&id=<?= $doc['id'] ?>"
                                   onclick="return confirm('Pulihkan dokumen ini?')"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors ring-1 ring-emerald-200/50"
                                   title="Pulihkan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Pulihkan
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=documents.force_delete&id=<?= $doc['id'] ?>"
                                   onclick="return confirm('PERINGATAN: Dokumen akan dihapus PERMANEN dan tidak bisa dikembalikan. Lanjutkan?')"
                                   class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100 transition-colors ring-1 ring-red-200/50"
                                   title="Hapus Permanen">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Hapus Permanen
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
