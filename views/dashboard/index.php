<?php
$pageTitle = 'Dashboard';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';

// Format angka Rupiah
function formatRupiah(float $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>

<!-- Halaman Dashboard -->
<div class="space-y-6 animate-fade-in">

    <!-- Greeting -->
    <div>
        <h2 class="text-2xl font-bold text-slate-900">
            <?php
            $hour = (int) date('G');
            if ($hour < 11)      echo 'Selamat Pagi';
            elseif ($hour < 15)  echo 'Selamat Siang';
            elseif ($hour < 18)  echo 'Selamat Sore';
            else                 echo 'Selamat Malam';
            ?>, <?= htmlspecialchars(explode(' ', $_SESSION['user_nama'] ?? 'Pengguna')[0]) ?> 👋
        </h2>
        <p class="mt-1 text-sm text-slate-500"><?= date('l, d F Y') ?> &mdash; <?= date('H:i') ?> WIB</p>
    </div>

    <!-- Stats Cards -->
    <?php if (($_SESSION['user_role'] ?? '') === 'admin'):
        $dashPendingCount = (new UserModel())->countPending();
    ?>
    <!-- Admin: 4 kolom -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

        <!-- Total Dokumen -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Dokumen</p>
                    <p class="mt-1.5 text-3xl font-bold text-slate-900" id="stat-total-dokumen">
                        <?= number_format($stats['total_dokumen']) ?>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600
                            transition-colors group-hover:bg-blue-600 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/index.php?page=documents"
                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat semua &rarr;
                </a>
            </div>
        </div>

        <!-- Total Pengguna -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pengguna</p>
                    <p class="mt-1.5 text-3xl font-bold text-slate-900" id="stat-total-pengguna">
                        <?= number_format($stats['total_pengguna']) ?>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600
                            transition-colors group-hover:bg-amber-600 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <a href="<?= BASE_URL ?>/index.php?page=users"
                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Kelola pengguna &rarr;
                </a>
                <?php if ($dashPendingCount > 0): ?>
                <a href="<?= BASE_URL ?>/index.php?page=users.pending"
                   class="ml-auto inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/50 hover:bg-amber-100 transition-colors">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <?= $dashPendingCount ?> menunggu
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Log Aktivitas -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Log Aktivitas</p>
                    <p class="mt-1.5 text-3xl font-bold text-slate-900" id="stat-total-log">
                        <?= number_format($stats['total_aktivitas']) ?>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600
                            transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/index.php?page=activity_logs"
                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat semua log &rarr;
                </a>
            </div>
        </div>

        <!-- Sampah -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sampah</p>
                    <p class="mt-1.5 text-3xl font-bold text-slate-900" id="stat-total-sampah">
                        <?= number_format($stats['total_sampah']) ?>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-500
                            transition-colors group-hover:bg-red-500 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/index.php?page=documents.trash"
                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Kelola sampah &rarr;
                </a>
            </div>
        </div>

    </div><!-- end stats grid (admin) -->

    <?php else: ?>
    <!-- ===== PEGAWAI: 3 kolom penuh ===== -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

        <!-- Total Dokumen (Pegawai) -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-lg hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Total Dokumen</p>
                    <p class="mt-3 text-5xl font-extrabold text-slate-900" id="stat-total-dokumen">
                        <?= number_format($stats['total_dokumen']) ?>
                    </p>
                    <p class="mt-2 text-sm text-slate-500">Dokumen aktif di sistem</p>
                </div>
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600
                            transition-colors group-hover:bg-blue-600 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-6 border-t border-slate-100 pt-4">
                <a href="<?= BASE_URL ?>/index.php?page=documents"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat semua dokumen
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Dokumen Sampah (Pegawai) -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-lg hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Dokumen Sampah</p>
                    <p class="mt-3 text-5xl font-extrabold text-slate-900" id="stat-total-sampah">
                        <?= number_format($stats['total_sampah']) ?>
                    </p>
                    <p class="mt-2 text-sm text-slate-500">Dapat dipulihkan kembali</p>
                </div>
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-500
                            transition-colors group-hover:bg-red-500 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
            <div class="mt-6 border-t border-slate-100 pt-4">
                <a href="<?= BASE_URL ?>/index.php?page=documents.trash"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Kelola sampah
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Aktivitas Saya (Pegawai) -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-lg hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Aktivitas Saya</p>
                    <p class="mt-3 text-5xl font-extrabold text-slate-900" id="stat-total-aktivitas">
                        <?= number_format($stats['total_aktivitas']) ?>
                    </p>
                    <p class="mt-2 text-sm text-slate-500">Riwayat login &amp; aksi</p>
                </div>
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600
                            transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-6 border-t border-slate-100 pt-4">
                <div class="flex items-center gap-2 text-sm text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Total sejak akun dibuat</span>
                </div>
            </div>
        </div>

    </div><!-- end stats grid (pegawai) -->
    <?php endif; ?>

    <!-- Recent Documents + Activity Feed -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <!-- Recent Documents (col-span-2) -->
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Dokumen Terbaru</h3>
                <a href="<?= BASE_URL ?>/index.php?page=documents"
                   class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    Lihat semua &rarr;
                </a>
            </div>
            <div class="divide-y divide-slate-50">
                <?php if (empty($recentDocuments)): ?>
                <div class="px-6 py-12 text-center text-sm text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>Belum ada dokumen yang diunggah.</p>
                    <a href="<?= BASE_URL ?>/index.php?page=documents.create"
                       class="mt-2 inline-block text-blue-600 hover:underline font-medium">Upload sekarang &rarr;</a>
                </div>
                <?php else: ?>
                <?php foreach ($recentDocuments as $doc): ?>
                <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-slate-50/80 transition-colors">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-800">
                            <?= htmlspecialchars($doc['judul']) ?>
                        </p>
                        <div class="mt-0.5 flex items-center gap-2 flex-wrap">
                            <?php if (!empty($doc['category_nama'])): ?>
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                <?= htmlspecialchars($doc['category_nama']) ?>
                            </span>
                            <?php endif; ?>
                            <span class="text-xs text-slate-400">
                                <?= date('d M Y', strtotime($doc['created_at'])) ?>
                                <?php if (!empty($doc['pihak_terkait'])): ?>
                                &bull; <?= htmlspecialchars($doc['pihak_terkait']) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?page=documents.show&id=<?= $doc['id'] ?>"
                       class="flex-shrink-0 rounded-lg px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                        Detail
                    </a>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($recentDocuments)): ?>
            <div class="border-t border-slate-50 px-6 py-3">
                <a href="<?= BASE_URL ?>/index.php?page=documents"
                   class="text-xs font-medium text-slate-400 hover:text-blue-600 transition-colors">
                    Tampilkan selengkapnya &rarr;
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Activity Feed + Info Panel -->
        <div class="flex flex-col gap-6">

            <!-- Recent Activity Feed -->
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Aktivitas Terkini</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <?php if (empty($recentLogs)): ?>
                    <p class="text-center text-sm text-slate-400 py-8">Belum ada aktivitas.</p>
                    <?php else: ?>
                    <?php foreach (array_slice($recentLogs, 0, 5) as $log): ?>
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full
                                    bg-slate-100 text-slate-500 text-xs font-bold">
                            <?= strtoupper(substr($log['nama_pengguna'] ?? 'S', 0, 1)) ?>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-700 leading-snug">
                                <?= htmlspecialchars($log['description']) ?>
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                <?= date('d M, H:i', strtotime($log['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (($_SESSION['user_role'] ?? '') !== 'admin'): ?>
            <!-- Info / Tips Panel (Pegawai only) -->
            <div class="rounded-2xl bg-slate-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-white">Panduan Singkat</p>
                </div>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p class="text-xs text-slate-300 leading-relaxed">Gunakan <span class="font-semibold text-white">Upload Dokumen</span> untuk menambah arsip baru.</p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <p class="text-xs text-slate-300 leading-relaxed">Klik <span class="font-semibold text-white">Detail</span> pada dokumen untuk melihat pratinjau & download.</p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <p class="text-xs text-slate-300 leading-relaxed">Dokumen yang dihapus masuk ke <span class="font-semibold text-white">Sampah</span> dan bisa dipulihkan.</p>
                    </li>
                </ul>
            </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
