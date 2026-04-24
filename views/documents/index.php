<?php
$pageTitle = 'Daftar Dokumen';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="space-y-5 animate-fade-in">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Daftar Dokumen</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                Total <?= number_format($totalDocs) ?> dokumen ditemukan
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <!-- Export Buttons -->
                <a href="<?= BASE_URL ?>/index.php?page=documents.export_csv<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>"
                   id="btn-export-csv"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                   title="Export CSV">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    CSV
                </a>
                <a href="<?= BASE_URL ?>/index.php?page=documents.export_pdf<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>"
                   id="btn-export-pdf" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                   title="Export PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    PDF
                </a>
            <a href="<?= BASE_URL ?>/index.php?page=documents.create"
               id="btn-upload-dokumen"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white
                      shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Dokumen
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="flex flex-col sm:flex-row gap-3" id="filter-form">
            <input type="hidden" name="page" value="documents">
            <div class="flex-1">
                <input type="text" name="search" id="search-input"
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       placeholder="Cari judul atau deskripsi..."
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800
                               placeholder-slate-400 outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="sm:w-48">
                <select name="category" id="filter-category"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800
                               outline-none transition-all focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (($_GET['category'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" id="btn-filter"
                    class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 transition-colors">
                Filter
            </button>
            <?php if (!empty($_GET['search']) || !empty($_GET['category'])): ?>
            <a href="<?= BASE_URL ?>/index.php?page=documents" id="btn-reset-filter"
               class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                Reset
            </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Bulk Actions Bar (hidden by default, shown when items selected) -->
    <div id="bulk-actions-bar" class="hidden rounded-2xl bg-blue-50 border border-blue-200 p-3 flex items-center justify-between gap-3 animate-fade-in">
        <div class="flex items-center gap-2.5">
            <span class="text-sm font-medium text-blue-800">
                <span id="bulk-count">0</span> dokumen dipilih
            </span>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=documents.bulk_download" id="bulk-download-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div id="bulk-download-ids"></div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200 hover:bg-emerald-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download ZIP
                </button>
            </form>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=documents.bulk_delete" id="bulk-delete-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div id="bulk-delete-ids"></div>
                <button type="submit" onclick="return confirm('Pindahkan dokumen terpilih ke sampah?')"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-red-200 hover:bg-red-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Terpilih
                </button>
            </form>
            <button onclick="uncheckAll()" class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-white transition-colors">
                Batal
            </button>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <?php if (empty($documents)): ?>
        <div class="py-16 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 h-14 w-14 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-slate-500 font-medium">Tidak ada dokumen ditemukan.</p>
            <a href="<?= BASE_URL ?>/index.php?page=documents.create"
               class="mt-3 inline-block rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                Upload Pertama
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="documents-table">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-4 py-3.5 text-left w-10">
                            <input type="checkbox" id="select-all-cb" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Judul</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Pengunggah</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Tanggal</th>
                        <th class="px-4 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    $currentMonthYear = '';
                    $strBulan = [
                        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                        'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                        'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                    ];
                    foreach ($documents as $i => $doc): 
                        $docDate = $doc['tanggal_transaksi'] ?: $doc['created_at'];
                        $enMonth = date('F', strtotime($docDate));
                        $year = date('Y', strtotime($docDate));
                        $idMonthYear = ($strBulan[$enMonth] ?? $enMonth) . ' ' . $year;

                        if ($currentMonthYear !== $idMonthYear):
                            $currentMonthYear = $idMonthYear;
                    ?>
                    <tr class="bg-slate-100/60 border-y border-slate-200">
                        <td colspan="6" class="px-4 py-2.5 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            <?= htmlspecialchars($currentMonthYear) ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr class="hover:bg-slate-50/80 transition-colors" data-doc-id="<?= $doc['id'] ?>">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="doc-checkbox h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" value="<?= $doc['id'] ?>">
                        </td>
                        <td class="px-4 py-4">
                            <p class="font-medium text-slate-800"><?= htmlspecialchars($doc['judul']) ?></p>
                            <div class="mt-1 flex items-center gap-2 flex-wrap">
                                <?php if (!empty($doc['category_nama'])): ?>
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                    <?= htmlspecialchars($doc['category_nama']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if ($doc['nominal']): ?>
                                <span class="text-xs text-emerald-600 font-medium">
                                    Rp <?= number_format($doc['nominal'], 0, ',', '.') ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-slate-500 hidden lg:table-cell">
                            <div class="text-sm font-medium text-slate-700"><?= htmlspecialchars($doc['nama_uploader'] ?? '-') ?></div>
                        </td>
                        <td class="px-4 py-4 text-slate-500 hidden lg:table-cell">
                            <?= date('d M Y', strtotime($docDate)) ?>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="<?= BASE_URL ?>/index.php?page=documents.show&id=<?= $doc['id'] ?>"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-blue-600 transition-colors"
                                   title="Detail Dokumen">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=documents.download&id=<?= $doc['id'] ?>"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-colors"
                                   title="Download">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?page=documents.edit&id=<?= $doc['id'] ?>"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                                   title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button onclick="confirmDelete(<?= $doc['id'] ?>, '<?= htmlspecialchars(addslashes($doc['judul'])) ?>')"
                                        class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                        title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-500">
                Menampilkan <?= $offset + 1 ?>–<?= min($offset + $perPage, $totalDocs) ?> dari <?= number_format($totalDocs) ?> dokumen
            </p>
            <div class="flex items-center gap-1.5">
                <?php
                $queryBase = 'page=documents';
                if (!empty($_GET['search']))      $queryBase .= '&search=' . urlencode($_GET['search']);
                if (!empty($_GET['category']))     $queryBase .= '&category=' . urlencode($_GET['category']);
                ?>
                <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/index.php?<?= $queryBase ?>&p=<?= $page - 1 ?>"
                   class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    &larr; Prev
                </a>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage   = min($totalPages, $page + 2);
                for ($p = $startPage; $p <= $endPage; $p++):
                ?>
                <a href="<?= BASE_URL ?>/index.php?<?= $queryBase ?>&p=<?= $p ?>"
                   class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors
                          <?= $p === $page ? 'bg-blue-600 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                    <?= $p ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/index.php?<?= $queryBase ?>&p=<?= $page + 1 ?>"
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

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl animate-fade-in">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-center text-lg font-bold text-slate-900">Pindahkan ke Sampah</h3>
        <p class="mt-2 text-center text-sm text-slate-500">
            Dokumen "<span id="delete-doc-name" class="font-semibold text-slate-700"></span>"
            akan dipindahkan ke tempat sampah. Anda dapat memulihkannya nanti.
        </p>
        <div class="mt-6 flex gap-3">
            <button onclick="closeDeleteModal()"
                    class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <a id="delete-confirm-link" href="#"
               class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-red-500 transition-colors">
                Ya, Hapus
            </a>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('delete-doc-name').textContent = name;
    document.getElementById('delete-confirm-link').href = '<?= BASE_URL ?>/index.php?page=documents.delete&id=' + id;
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ===== Bulk Select Logic =====
const selectAllCb = document.getElementById('select-all-cb');
const docCheckboxes = document.querySelectorAll('.doc-checkbox');
const bulkBar = document.getElementById('bulk-actions-bar');
const bulkCount = document.getElementById('bulk-count');

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.doc-checkbox:checked')).map(cb => cb.value);
}
function updateBulkBar() {
    const ids = getSelectedIds();
    if (ids.length > 0) {
        bulkBar.classList.remove('hidden');
        bulkCount.textContent = ids.length;
        // Populate hidden inputs for forms
        ['bulk-download-ids', 'bulk-delete-ids'].forEach(containerId => {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'doc_ids[]'; input.value = id;
                container.appendChild(input);
            });
        });
    } else {
        bulkBar.classList.add('hidden');
    }
}
function uncheckAll() {
    docCheckboxes.forEach(cb => cb.checked = false);
    if (selectAllCb) selectAllCb.checked = false;
    updateBulkBar();
}

if (selectAllCb) {
    selectAllCb.addEventListener('change', () => {
        docCheckboxes.forEach(cb => cb.checked = selectAllCb.checked);
        updateBulkBar();
    });
}
docCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
        if (selectAllCb) selectAllCb.checked = document.querySelectorAll('.doc-checkbox:checked').length === docCheckboxes.length;
        updateBulkBar();
    });
});
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
