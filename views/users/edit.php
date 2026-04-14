<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="max-w-2xl animate-fade-in">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Edit Pengguna</h2>
            <p class="text-sm text-slate-500 mt-0.5">Ubah data pengguna, termasuk role dan password</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?page=users" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
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

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden p-6">
        <form action="<?= BASE_URL ?>/index.php?page=users.update&id=<?= $user['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="space-y-5">
                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" required value="<?= htmlspecialchars($user['nama']) ?>"
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($user['email']) ?>"
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label for="role" class="mb-1.5 block text-sm font-medium text-slate-700">Role</label>
                    <select name="role" id="role" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="pegawai" <?= $user['role'] === 'pegawai' ? 'selected' : '' ?>>Pegawai</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                
                <hr class="my-4 border-slate-100">
                <div class="rounded-xl bg-orange-50/50 border border-orange-100 p-4">
                    <div class="flex gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-orange-800">Privasi Akun</h4>
                            <p class="mt-1 text-xs text-orange-600/90 leading-relaxed">Admin tidak memilik akses untuk mengubah password pengguna. Pengguna harus mengganti password mereka sendiri melalui halaman Profil Saya.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="<?= BASE_URL ?>/index.php?page=users" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                    Update Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
