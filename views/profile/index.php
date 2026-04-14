<?php
require_once BASE_PATH . '/views/layouts/header.php';
require_once BASE_PATH . '/views/layouts/sidebar.php';
?>

<div class="max-w-2xl animate-fade-in">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Profil Saya</h2>
        <p class="text-sm text-slate-500 mt-0.5">Kelola informasi data diri dan keamanan akun Anda</p>
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

    <!-- Flash Success -->
    <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-600" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
    </div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden p-6 md:p-8">
        
        <form action="<?= BASE_URL ?>/index.php?page=profile.update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- Avatar & Header Section -->
            <div class="mb-8 flex flex-col sm:flex-row items-center sm:items-start gap-6 border-b border-slate-100 pb-8 text-center sm:text-left">
                
                <!-- Avatar Upload Wraper -->
                <div class="relative group cursor-pointer" onclick="document.getElementById('foto_profil').click()">
                    <!-- Hidden File Input -->
                    <input type="file" name="foto_profil" id="foto_profil" accept="image/jpeg, image/png, image/webp" class="hidden" onchange="previewImage(event)">
                    
                    <div id="avatar-container" class="relative h-28 w-28 overflow-hidden rounded-full shadow-md ring-4 ring-slate-50 transition-transform group-hover:scale-105">
                        <?php if (!empty($user['foto_profil']) && file_exists(BASE_PATH . '/public/uploads/profiles/' . $user['foto_profil'])): ?>
                            <img id="avatar-image" src="<?= BASE_URL ?>/public/uploads/profiles/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Foto Profil" class="h-full w-full object-cover">
                        <?php else: ?>
                            <div id="avatar-fallback" class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-100 to-blue-200 text-4xl font-bold text-blue-600">
                                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                            </div>
                            <img id="avatar-image" src="" alt="Foto Profil" class="h-full w-full object-cover hidden">
                        <?php endif; ?>
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-feather="camera" class="h-8 w-8 text-white"></i>
                        </div>
                    </div>
                </div>

                <div class="mt-2 sm:mt-4">
                    <h3 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($user['nama']) ?></h3>
                    <p class="text-sm font-medium text-slate-500 mt-1"><?= htmlspecialchars($user['email']) ?></p>
                    <div class="mt-3 inline-flex items-center rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 uppercase tracking-wider">
                        <?= htmlspecialchars($user['role']) ?>
                    </div>
                </div>
            </div>

            <!-- Form Fields -->
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

                <div class="mt-4 rounded-xl bg-slate-50 border border-slate-100 p-4">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Ganti Password</h4>
                    <p class="text-xs text-slate-500 mb-4">Kosongkan jika Anda tidak ingin mengganti password.</p>

                    <div class="space-y-4">
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password Baru</label>
                            <input type="password" name="password" id="password" autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                   placeholder="Minimal 8 karakter">
                        </div>

                        <div>
                            <label for="confirm_password" class="mb-1.5 block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                   placeholder="Ulangi password baru">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end border-t border-slate-100 pt-5 pr-2">
                <span class="text-xs text-slate-400 mr-auto ml-2 hidden sm:block">Perubahan foto akan tersimpan setelah Anda menekan tombol simpan.</span>
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all hover:scale-105">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const imgElement = document.getElementById('avatar-image');
        const fallbackElement = document.getElementById('avatar-fallback');
        
        imgElement.src = reader.result;
        imgElement.classList.remove('hidden');
        
        if (fallbackElement) {
            fallbackElement.classList.add('hidden');
        }
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
        // Form tidak lagi di-submit otomatis agar tidak bentrok dengan auto-fill password
    }
}
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>
