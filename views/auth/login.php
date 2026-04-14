<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke Sistem Manajemen Arsip">
    <title>Login | <?= APP_NAME ?></title>

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
            <h1 class="text-2xl font-bold text-white">Selamat Datang</h1>
            <p class="mt-1 text-sm text-slate-400">Login ke <?= APP_NAME ?></p>
        </div>

        <!-- Flash Error -->
        <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-300" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
        </div>
        <?php unset($_SESSION['flash_error']); endif; ?>

        <!-- Rate Limit Lockout Countdown -->
        <?php if (isset($lockoutRemaining) && $lockoutRemaining > 0): ?>
        <div class="mb-5 rounded-xl border border-amber-500/20 bg-amber-500/10 px-4 py-4 text-center" id="lockout-banner">
            <div class="flex items-center justify-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span class="text-sm font-semibold text-amber-300">Akun Terkunci Sementara</span>
            </div>
            <p class="text-xs text-amber-400/80">Coba lagi dalam</p>
            <p class="text-2xl font-bold text-amber-300 mt-1" id="lockout-timer"><?= gmdate('i:s', $lockoutRemaining) ?></p>
        </div>
        <script>
            (function() {
                let remaining = <?= (int)$lockoutRemaining ?>;
                const timerEl = document.getElementById('lockout-timer');
                const bannerEl = document.getElementById('lockout-banner');
                const submitBtn = document.getElementById('login-submit-btn');
                if (submitBtn) { submitBtn.disabled = true; submitBtn.style.opacity = '0.5'; }
                const tick = setInterval(() => {
                    remaining--;
                    if (remaining <= 0) {
                        clearInterval(tick);
                        if (bannerEl) bannerEl.remove();
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.style.opacity = '1'; }
                        return;
                    }
                    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const s = String(remaining % 60).padStart(2, '0');
                    if (timerEl) timerEl.textContent = m + ':' + s;
                }, 1000);
            })();
        </script>
        <?php endif; ?>

        <!-- Flash Success -->
        <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-300" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
        </div>
        <?php unset($_SESSION['flash_success']); endif; ?>

        <!-- Form Login -->
        <form method="POST" action="<?= BASE_URL ?>/index.php?page=login.process" class="space-y-5" id="login-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div>
                <label for="login-email" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Alamat Email
                </label>
                <input type="email" name="email" id="login-email" required
                       class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500
                              outline-none transition-all focus:border-blue-500 focus:bg-white/8 focus:ring-2 focus:ring-blue-500/20"
                       placeholder="nama@perusahaan.com"
                       autocomplete="email">
            </div>

            <div>
                <label for="login-password" class="mb-1.5 block text-sm font-medium text-slate-300">
                    Password
                </label>
                <div class="relative">
                    <input type="password" name="password" id="login-password" required
                           class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-11 text-sm text-white placeholder-slate-500
                                  outline-none transition-all focus:border-blue-500 focus:bg-white/8 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="••••••••"
                           autocomplete="current-password">
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

            <button type="submit" id="login-submit-btn"
                    class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/30
                           transition-all hover:bg-blue-500 hover:shadow-blue-500/40 hover:scale-[1.01] active:scale-[0.99]
                           focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-transparent">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Register link -->
        <p class="mt-6 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="<?= BASE_URL ?>/index.php?page=register" id="link-register"
               class="font-medium text-blue-400 hover:text-blue-300 transition-colors hover:underline">
                Daftar sekarang
            </a>
        </p>
    </div>
</div>

<script>
    // Toggle password visibility
    const toggleBtn = document.getElementById('toggle-password');
    const passInput = document.getElementById('login-password');
    if (toggleBtn && passInput) {
        toggleBtn.addEventListener('click', () => {
            passInput.type = passInput.type === 'password' ? 'text' : 'password';
        });
    }
</script>

</body>
</html>
