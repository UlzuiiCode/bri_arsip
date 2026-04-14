<?php
$pageTitle = 'Manajemen Kategori';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="space-y-5 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Manajemen Kategori</h2>
            <p class="text-sm text-slate-500 mt-0.5"><?= count($categories) ?> kategori tersedia</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=categories.create"
           id="btn-tambah-kategori"
           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php if (empty($categories)): ?>
        <div class="col-span-full rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-3 h-12 w-12 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            <p class="text-slate-500">Belum ada kategori.</p>
        </div>
        <?php else: ?>
        <?php foreach ($categories as $cat): ?>
        <div class="group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
                <div class="flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                    <a href="<?= BASE_URL ?>/index.php?page=categories.edit&id=<?= $cat['id'] ?>"
                       class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?page=categories.delete&id=<?= $cat['id'] ?>"
                       onclick="return confirm('Hapus kategori ini?')"
                       class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-semibold text-slate-900"><?= htmlspecialchars($cat['nama']) ?></h3>
                <?php if ($cat['deskripsi']): ?>
                <p class="mt-0.5 text-xs text-slate-400 line-clamp-2"><?= htmlspecialchars($cat['deskripsi']) ?></p>
                <?php endif; ?>
            </div>
            <div class="mt-3 flex items-center gap-1.5">
                <span class="text-xs font-semibold text-slate-900"><?= $cat['total_dokumen'] ?></span>
                <span class="text-xs text-slate-400">dokumen</span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
