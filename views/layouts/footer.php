    </main>
    <!-- ===== PAGE CONTENT ENDS HERE ===== -->

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white px-6 py-3">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-slate-400">
                &copy; <?= date('Y') ?> <span class="font-semibold text-slate-600">PT Bank Rakyat Indonesia (Persero) Tbk</span>.
                <?= APP_NAME ?>.
            </p>
            <p class="text-xs text-slate-400">
                Versi <?= APP_VERSION ?>
                &mdash; Divisi Operasional &amp; Transaksi
            </p>
        </div>
    </footer>

</div><!-- end .flex-1 -->
</div><!-- end #app-wrapper -->

<!-- Custom JS -->
<script src="<?= BASE_URL ?>/public/js/app.js" defer></script>

<!-- Inisialisasi Feather Icons -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') feather.replace();
    });
</script>

</body>
</html>
