<?php
$pageTitle = 'Upload Dokumen';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="mx-auto max-w-2xl space-y-5 animate-fade-in">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/index.php?page=documents"
           class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white
                  text-slate-500 hover:bg-slate-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-slate-900">Upload Dokumen Baru</h2>
            <p class="text-sm text-slate-500">Isi detail dokumen di bawah ini</p>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=documents.store"
              enctype="multipart/form-data" id="upload-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="space-y-5">

                <!-- Judul -->
                <div>
                    <label for="doc-judul" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Judul Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" id="doc-judul" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                                  placeholder-slate-400 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100"
                           placeholder="Masukkan judul dokumen...">
                </div>

                <!-- Kategori -->
                <div>
                    <label for="doc-kategori" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Kategori
                    </label>
                    <select name="category_id" id="doc-kategori"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                                   outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="">-- Pilih Kategori (opsional) --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="doc-deskripsi" class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Deskripsi
                    </label>
                    <textarea name="deskripsi" id="doc-deskripsi" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                                     placeholder-slate-400 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100 resize-none"
                              placeholder="Deskripsi singkat dokumen (opsional)..."></textarea>
                </div>

                <!-- Keuangan / Waktu (side by side) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="doc-nominal" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Nominal (Rp)
                        </label>
                        <input type="number" name="nominal" id="doc-nominal" min="0" step="0.01"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                                      placeholder-slate-400 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100"
                               placeholder="0">
                    </div>
                    <div>
                        <label for="doc-pihak" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Pihak Terkait
                        </label>
                        <input type="text" name="pihak_terkait" id="doc-pihak"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                                      placeholder-slate-400 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100"
                               placeholder="Nama pihak terkait...">
                    </div>
                    <div>
                        <label for="doc-tanggal" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Tanggal Aktivitas
                        </label>
                        <input type="date" name="tanggal_transaksi" id="doc-tanggal"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800
                                      placeholder-slate-400 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        File Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div id="file-drop-zone"
                         class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200
                                bg-slate-50 px-6 py-10 transition-all cursor-pointer
                                hover:border-blue-300 hover:bg-blue-50/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p class="text-sm font-medium text-slate-500">
                            <span class="text-blue-600">Klik untuk pilih file</span> atau seret & lepas
                        </p>
                        <p class="mt-1 text-xs text-slate-400">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (maks. 5MB)</p>
                        <p id="file-name-preview" class="mt-2 text-xs font-medium text-blue-700 hidden"></p>
                        <input type="file" name="file_dokumen" id="file-input" required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="hidden">
                    </div>
                </div>

            </div><!-- end space-y-5 -->

            <!-- Submit -->
            <div class="mt-6 flex gap-3">
                <button type="submit" id="btn-submit-upload"
                        class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/30
                               hover:bg-blue-500 transition-all hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Upload Dokumen
                </button>
                <a href="<?= BASE_URL ?>/index.php?page=documents"
                   class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>

<script>
// Drag & drop file zone
const dropZone  = document.getElementById('file-drop-zone');
const fileInput = document.getElementById('file-input');
const preview   = document.getElementById('file-name-preview');

dropZone.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
        preview.textContent = '📄 ' + fileInput.files[0].name;
        preview.classList.remove('hidden');
        dropZone.classList.add('border-blue-300', 'bg-blue-50/60');
    }
});

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-blue-400', 'bg-blue-50/60');
});
dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-blue-400', 'bg-blue-50/60');
});
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    fileInput.files = e.dataTransfer.files;
    if (fileInput.files.length > 0) {
        preview.textContent = '📄 ' + fileInput.files[0].name;
        preview.classList.remove('hidden');
    }
});
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
