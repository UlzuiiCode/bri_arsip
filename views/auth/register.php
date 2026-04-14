<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar akun baru di Sistem Manajemen Arsip">
    <title>Daftar | <?= APP_NAME ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    animation: { 'fade-in': 'fadeIn 0.5s ease-out' },
                    keyframes: { fadeIn: { from: { opacity: '0', transform: 'translateY(16px)' }, to: { opacity: '1', transform: 'translateY(0)' } } }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/app.css">
</head>
<body class="flex min-h-full items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 font-sans p-4">

<!-- Background pattern -->
<div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-blue-600/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-indigo-600/10 blur-3xl"></div>
</div>

<div class="relative w-full max-w-md animate-fade-in">

    <!-- Card -->
    <div class="rounded-3xl bg-white/5 backdrop-blur-xl border border-white/10 p-8 shadow-2xl">

        <!-- Logo -->
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 px-5 max-w-[150px] items-center justify-center rounded-2xl bg-white shadow-lg shadow-white/10">
                <img src="<?= BASE_URL ?>/public/bri_logo.svg" alt="BRI Logo" class="h-6">
            </div>
            <h1 class="text-2xl font-bold text-white">Buat Akun Baru</h1>
            <p class="mt-1 text-sm text-slate-400">Daftar ke <?= APP_NAME ?></p>
        </div>

        <!-- Flash Error -->
        <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="mb-5 flex items-start gap-2.5 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span><?= $_SESSION['flash_error'] ?></span>
        </div>
        <?php unset($_SESSION['flash_error']); endif; ?>

        <!-- Form Register -->
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=register.process" class="space-y-4" id="register-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div>
                <label for="register-nama" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Nama Lengkap
                </label>
                <input type="text" name="nama" id="register-nama" required
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500
                              outline-none transition-all focus:border-blue-500 focus:bg-white/8 focus:ring-2 focus:ring-blue-500/20"
                       placeholder="Nama lengkap Anda"
                       autocomplete="name">
            </div>

            <div>
                <label for="register-email" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Alamat Email
                </label>
                <input type="email" name="email" id="register-email" required
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500
                              outline-none transition-all focus:border-blue-500 focus:bg-white/8 focus:ring-2 focus:ring-blue-500/20"
                       placeholder="nama@perusahaan.com"
                       autocomplete="email">
            </div>

            <div>
                <label for="register-password" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Password
                </label>
                <div class="relative">
                    <input type="password" name="password" id="register-password" required
                           class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-11 text-sm text-white placeholder-slate-500
                                  outline-none transition-all focus:border-blue-500 focus:bg-white/8 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="Minimal 8 karakter"
                           autocomplete="new-password">
                    <button type="button" id="toggle-password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors"
                            aria-label="Tampilkan password">
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="register-confirm" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Konfirmasi Password
                </label>
                <input type="password" name="confirm_password" id="register-confirm" required
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500
                              outline-none transition-all focus:border-blue-500 focus:bg-white/8 focus:ring-2 focus:ring-blue-500/20"
                       placeholder="Ulangi password Anda"
                       autocomplete="new-password">
            </div>

            <button type="submit" id="register-submit-btn"
                    class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/30
                           transition-all hover:bg-blue-500 hover:shadow-blue-500/40 hover:scale-[1.01] active:scale-[0.99]
                           focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent mt-2">
                Buat Akun
            </button>
        </form>

        <!-- Login link -->
        <p class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="<?= BASE_URL ?>/index.php?page=login" id="link-login"
               class="font-medium text-blue-400 hover:text-blue-300 transition-colors hover:underline">
                Masuk sekarang
            </a>
        </p>
    </div>
</div>

<script>
    // Toggle password visibility
    const toggleBtn = document.getElementById('toggle-password');
    const passInput = document.getElementById('register-password');
    if (toggleBtn && passInput) {
        toggleBtn.addEventListener('click', () => {
            passInput.type = passInput.type === 'password' ? 'text' : 'password';
        });
    }
</script>

</body>
</html>
