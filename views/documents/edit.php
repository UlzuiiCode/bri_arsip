<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="max-w-3xl animate-fade-in">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Edit Dokumen</h2>
            <p class="text-sm text-slate-500 mt-0.5">Ubah metadata dokumen</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=documents" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
            &larr; Kembali
        </a>
    </div>

    <!-- Peringatan Flash Error -->
    <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <span><?= $_SESSION['flash_error'] ?></span>
    </div>
    <?php unset($_SESSION['flash_error']); endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden p-6 md:p-8">
        <form action="<?= BASE_URL ?>/index.php?page=documents.update&id=<?= $document['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom 1 -->
                <div class="space-y-5">
                    <div>
                        <label for="judul" class="mb-1.5 block text-sm font-medium text-slate-700">Judul Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" required value="<?= htmlspecialchars($document['judul']) ?>"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>

                    <div>
                        <label for="category_id" class="mb-1.5 block text-sm font-medium text-slate-700">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $document['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="deskripsi" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi Ringkas</label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"><?= htmlspecialchars($document['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Kolom 2 -->
                <div class="space-y-5">
                    <div>
                        <label for="nominal" class="mb-1.5 block text-sm font-medium text-slate-700">Nominal Transaksi (opsional)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm text-slate-500">Rp</span>
                            <input type="number" step="0.01" name="nominal" id="nominal" value="<?= htmlspecialchars($document['nominal'] ?? '') ?>"
                                   class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                   placeholder="0.00">
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Isi jika dokumen ini berkaitan dengan keuangan.</p>
                    </div>

                    <div>
                        <label for="pihak_terkait" class="mb-1.5 block text-sm font-medium text-slate-700">Pihak Terkait (opsional)</label>
                        <input type="text" name="pihak_terkait" id="pihak_terkait" value="<?= htmlspecialchars($document['pihak_terkait'] ?? '') ?>"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                               placeholder="Nama debitur, mitra, dll">
                    </div>
                    
                    <div class="mt-4 rounded-xl bg-slate-50 border border-slate-100 p-4">
                        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Informasi File Asli</h4>
                        <p class="text-sm text-slate-900 line-clamp-1 mb-1 font-medium"><?= htmlspecialchars($document['file_name']) ?></p>
                        <p class="text-xs text-slate-500">
                            Diunggah: <?= date('d M Y H:i', strtotime($document['created_at'])) ?>
                        </p>
                        <p class="mt-2 text-xs text-slate-400 italic">Catatan: Untuk mengganti file, harap hapus dokumen ini dan buat dokumen baru.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="<?= BASE_URL ?>/index.php?page=documents" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                    Update Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
