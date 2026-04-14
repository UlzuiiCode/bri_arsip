<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="max-w-2xl animate-fade-in">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Tambah Kategori</h2>
            <p class="text-sm text-slate-500 mt-0.5">Buat kategori baru untuk pengelompokan dokumen</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=categories" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
            &larr; Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden p-6">
        <form action="<?= BASE_URL ?>/index.php?page=categories.store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="space-y-5">
                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Kategori</label>
                    <input type="text" name="nama" id="nama" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="Misal: Keuangan, HRD, Surat Masuk">
                </div>

                <div>
                    <label for="deskripsi" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi (Opsional)</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                              class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                              placeholder="Penjelasan singkat mengenai kategori ini..."></textarea>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="<?= BASE_URL ?>/index.php?page=categories" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
