<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="max-w-2xl animate-fade-in">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Tambah Pengguna</h2>
            <p class="text-sm text-slate-500 mt-0.5">Buat akun pengguna baru</p>
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
        <form action="<?= BASE_URL ?>/index.php?page=users.store" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="space-y-5">
                <div>
                    <label for="nama" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="Nama Pengguna">
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" id="email" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="email@perusahaan.com">
                </div>

                <div>
                    <label for="role" class="mb-1.5 block text-sm font-medium text-slate-700">Role</label>
                    <select name="role" id="role" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="pegawai">Pegawai</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="pt-2">
                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="confirm_password" class="mb-1.5 block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="Ulangi password">
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="<?= BASE_URL ?>/index.php?page=users" class="rounded-xl px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                    Buat Akun
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
