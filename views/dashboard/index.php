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
            <div class="mt-4 flex items-center gap-1.5 text-xs text-emerald-600 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                <span>Sistem aktif</span>
            </div>
        </div>

        <!-- Total Kategori -->
        <div class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100
                    transition-all hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</p>
                    <p class="mt-1.5 text-3xl font-bold text-slate-900" id="stat-total-kategori">
                        <?= number_format($stats['total_kategori']) ?>
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600
                            transition-colors group-hover:bg-violet-600 group-hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                <span>Terorganisir dengan baik</span>
            </div>
        </div>

        <!-- Total Pengguna (Admin only) -->
        <?php if (($_SESSION['user_role'] ?? '') === 'admin'):
            $dashPendingCount = (new UserModel())->countPending();
        ?>
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

        <!-- Total Log Aktivitas -->
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
        <?php endif; ?>

    </div><!-- end stats grid -->

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
                    <!-- File icon -->
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-800">
                            <?= htmlspecialchars($doc['judul']) ?>
                        </p>
                        <p class="text-xs text-slate-400">
                            <?= htmlspecialchars($doc['nama_kategori'] ?? 'Tanpa Kategori') ?> &bull;
                            <?= date('d M Y', strtotime($doc['created_at'])) ?>
                        </p>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?page=documents.show&id=<?= $doc['id'] ?>"
                       class="flex-shrink-0 rounded-lg px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                        Detail
                    </a>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-sm font-semibold text-slate-900">Aktivitas Terkini</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <?php if (empty($recentLogs)): ?>
                <p class="text-center text-sm text-slate-400 py-8">Belum ada aktivitas.</p>
                <?php else: ?>
                <?php foreach (array_slice($recentLogs, 0, 6) as $log): ?>
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

    </div>

</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
