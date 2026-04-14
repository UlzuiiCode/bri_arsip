<?php
$pageTitle = 'Daftar Pengguna';
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';

// Hitung jumlah akun pending
require_once BASE_PATH . '/models/UserModel.php';
$pendingCount = (new UserModel())->countPending();
?>

<div class="space-y-5 animate-fade-in">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Manajemen Pengguna</h2>
            <p class="text-sm text-slate-500 mt-0.5"><?= count($users) ?> pengguna terdaftar</p>
        </div>
        <div class="flex items-center gap-2.5">
            <?php if ($pendingCount > 0): ?>
            <a href="<?= BASE_URL ?>/index.php?page=users.pending"
               id="btn-pending-akun"
               class="relative inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 shadow-sm hover:bg-amber-100 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Menunggu Persetujuan
                <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-500 px-1.5 text-xs font-bold text-white">
                    <?= $pendingCount ?>
                </span>
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/index.php?page=users.create"
               id="btn-tambah-pengguna"
               class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Pengguna
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="users-table">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">#</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Role</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Terdaftar</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($users as $i => $user): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-slate-400"><?= $i + 1 ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($user['foto_profil']) && file_exists(BASE_PATH . '/public/uploads/profiles/' . $user['foto_profil'])): ?>
                                    <img src="<?= BASE_URL ?>/public/uploads/profiles/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Avatar" class="h-8 w-8 rounded-full object-cover shadow-sm ring-1 ring-slate-200 flex-shrink-0">
                                <?php else: ?>
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-xs font-bold text-white flex-shrink-0">
                                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="font-medium text-slate-800"><?= htmlspecialchars($user['nama']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="badge <?= $user['role'] === 'admin' ? 'badge-blue' : 'badge-green' ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs hidden lg:table-cell">
                            <?= date('d M Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="<?= BASE_URL ?>/index.php?page=users.edit&id=<?= $user['id'] ?>"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <?php if ($user['id'] !== (int) $_SESSION['user_id']): ?>
                                <a href="<?= BASE_URL ?>/index.php?page=users.delete&id=<?= $user['id'] ?>"
                                   onclick="return confirm('Hapus pengguna <?= htmlspecialchars(addslashes($user['nama'])) ?>?')"
                                   class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
