<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="max-w-4xl animate-fade-in">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Detail Dokumen</h2>
            <p class="text-sm text-slate-500 mt-0.5">Informasi lengkap terkait arsip dokumen</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/index.php?page=documents" class="hidden sm:inline-flex rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                Kembali
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=documents.edit&id=<?= $document['id'] ?>" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=documents.download&id=<?= $document['id'] ?>" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download File
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 mb-3">
                            <?= htmlspecialchars($document['nama_kategori'] ?? 'Tanpa Kategori') ?>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($document['judul']) ?></h1>
                        <p class="mt-2 text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($document['deskripsi'] ?? 'Tidak ada deskripsi.')) ?></p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4 border-t border-slate-100 pt-6 sm:grid-cols-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Diupload Oleh</p>
                        <p class="mt-1 font-medium text-slate-900"><?= htmlspecialchars($document['nama_pengunggah'] ?? 'Unknown') ?></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Upload</p>
                        <p class="mt-1 font-medium text-slate-900"><?= date('d M Y', strtotime($document['created_at'])) ?></p>
                        <p class="text-xs text-slate-500"><?= date('H:i', strtotime($document['created_at'])) ?> WIB</p>
                    </div>
                    <!-- Transaksi Khusus -->
                    <?php if ($document['nominal']): ?>
                    <div>
                        <p class="text-xs font-medium text-emerald-500 uppercase tracking-wider">Nominal Transaksi</p>
                        <p class="mt-1 font-bold text-slate-900">Rp <?= number_format((float)$document['nominal'], 0, ',', '.') ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($document['pihak_terkait']): ?>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pihak Terkait</p>
                        <p class="mt-1 font-medium text-slate-900"><?= htmlspecialchars($document['pihak_terkait']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($document['tanggal_transaksi']): ?>
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Aktivitas</p>
                        <p class="mt-1 font-medium text-slate-900"><?= date('d F Y', strtotime($document['tanggal_transaksi'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Dokumen Preview -->
            <?php 
                $ext = strtolower(pathinfo($document['file_name'], PATHINFO_EXTENSION));
                $fileSrc = BASE_URL . '/public/uploads/' . $document['file_path'];
            ?>
            <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
            <!-- Image Preview -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-3 flex items-center justify-between bg-slate-50/50">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Preview Gambar</span>
                    <a href="<?= $fileSrc ?>" target="_blank" class="text-xs text-blue-600 hover:underline">Buka di tab baru</a>
                </div>
                <div class="p-4 bg-[#f8f8f8] flex items-center justify-center">
                    <img src="<?= htmlspecialchars($fileSrc) ?>" 
                         alt="<?= htmlspecialchars($document['judul']) ?>"
                         class="max-w-full max-h-[500px] rounded-lg shadow-sm object-contain">
                </div>
            </div>
            <?php elseif ($ext === 'pdf'): ?>
            <!-- PDF Preview -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-3 flex items-center justify-between bg-slate-50/50">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Preview PDF</span>
                    <a href="<?= $fileSrc ?>" target="_blank" class="text-xs text-blue-600 hover:underline">Buka di tab baru</a>
                </div>
                <iframe src="<?= htmlspecialchars($fileSrc) ?>" 
                        class="w-full border-0" style="height: 600px;"
                        title="Preview PDF: <?= htmlspecialchars($document['judul']) ?>">
                </iframe>
            </div>
            <?php else: ?>
            <!-- Non-previewable file -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm overflow-hidden flex flex-col items-center justify-center min-h-[200px] bg-slate-50">
               <div class="text-center">
                   <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                       </svg>
                   </div>
                   <p class="text-sm font-medium text-slate-700 mb-1">Preview tidak tersedia untuk tipe file ini</p>
                   <p class="text-xs text-slate-500 mb-4">File <?= strtoupper($ext) ?> — Silakan download untuk membuka.</p>
                   <a href="<?= BASE_URL ?>/index.php?page=documents.download&id=<?= $document['id'] ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50 transition-colors">
                       Download <?= strtoupper($ext) ?>
                   </a>
               </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-slate-900 mb-4">Informasi File</h3>
                
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 mb-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-900" title="<?= htmlspecialchars($document['file_name']) ?>">
                            <?= htmlspecialchars($document['file_name']) ?>
                        </p>
                        <p class="text-xs text-slate-500">
                            <?= number_format($document['file_size'] / 1024, 2) ?> KB &bull; <?= strtoupper(pathinfo($document['file_name'], PATHINFO_EXTENSION)) ?>
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tipe File</span>
                        <span class="font-medium text-slate-900"><?= htmlspecialchars($document['file_type']) ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">ID Server</span>
                        <span class="font-mono text-xs text-slate-400 mt-0.5 truncate max-w-[120px]"><?= htmlspecialchars($document['file_path']) ?></span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm border-dashed">
                <h3 class="font-semibold text-amber-800 mb-2">Pindahkan ke Sampah</h3>
                <p class="text-xs text-amber-600/80 mb-4 leading-relaxed">Dokumen akan dipindahkan ke tempat sampah. Anda dapat memulihkannya kembali nanti melalui menu Sampah.</p>
                <a href="<?= BASE_URL ?>/index.php?page=documents.delete&id=<?= $document['id'] ?>" 
                   onclick="return confirm('Pindahkan dokumen ini ke tempat sampah?');"
                   class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-amber-700 shadow-sm ring-1 ring-amber-200 hover:bg-amber-50 hover:ring-amber-300 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Pindahkan ke Sampah
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
