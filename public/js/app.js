/**
 * app.js — Logik JavaScript utama
 * Sistem Manajemen Arsip
 */

document.addEventListener('DOMContentLoaded', function () {

    // ===================================================
    // 1. Mobile Sidebar Toggle
    // ===================================================
    const sidebar       = document.getElementById('sidebar');
    const overlay       = document.getElementById('sidebar-overlay');
    const openBtn       = document.getElementById('sidebar-open-btn');
    const closeBtn      = document.getElementById('sidebar-close-btn');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('sidebar-open');
        overlay && overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('sidebar-open');
        overlay && overlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    openBtn  && openBtn .addEventListener('click', openSidebar);
    closeBtn && closeBtn.addEventListener('click', closeSidebar);
    overlay  && overlay .addEventListener('click', closeSidebar);

    // Tutup sidebar saat menekan Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });

    // ===================================================
    // 2. User Dropdown Menu
    // ===================================================
    const userMenuBtn  = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-dropdown');

    if (userMenuBtn && userDropdown) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !userDropdown.classList.contains('hidden');
            userDropdown.classList.toggle('hidden');
            userMenuBtn.setAttribute('aria-expanded', String(!isOpen));
            // Close notif dropdown if open
            const notifDropdown = document.getElementById('notif-dropdown');
            if (notifDropdown) notifDropdown.classList.add('hidden');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.add('hidden');
            userMenuBtn.setAttribute('aria-expanded', 'false');
        });

        userDropdown.addEventListener('click', (e) => e.stopPropagation());
    }

    // ===================================================
    // 2b. Notification Dropdown
    // ===================================================
    const notifBtn      = document.getElementById('notification-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    const notifList     = document.getElementById('notif-list');
    const notifBadge    = document.getElementById('notif-badge');
    const notifMarkRead = document.getElementById('notif-mark-read');

    let notifLoaded = false;

    function getLastReadId() {
        return parseInt(localStorage.getItem('notif_last_read_id') || '0', 10);
    }
    function setLastReadId(id) {
        localStorage.setItem('notif_last_read_id', String(id));
    }

    function renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            notifList.innerHTML = `
                <div class="px-4 py-8 text-center text-sm text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Belum ada notifikasi
                </div>`;
            return;
        }

        const lastReadId = getLastReadId();
        let hasUnread = false;
        let maxId = lastReadId;

        notifList.innerHTML = notifications.map(n => {
            const isUnread = n.id > lastReadId;
            if (isUnread) hasUnread = true;
            if (n.id > maxId) maxId = n.id;

            const colorMap = {
                'green': 'bg-green-100 text-green-600',
                'red': 'bg-red-100 text-red-600',
                'blue': 'bg-blue-100 text-blue-600',
                'amber': 'bg-amber-100 text-amber-600',
                'slate': 'bg-slate-100 text-slate-500',
                'emerald': 'bg-emerald-100 text-emerald-600',
            };
            const iconClass = colorMap[n.color] || colorMap['blue'];

            return `
                <div class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors ${isUnread ? 'bg-blue-50/40' : ''}">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full ${iconClass} flex-shrink-0 mt-0.5">
                        <i data-feather="${n.icon}" class="h-3.5 w-3.5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700 leading-snug">${escapeHtml(n.description)}</p>
                        <p class="text-xs text-slate-400 mt-0.5">${escapeHtml(n.user)} &middot; ${escapeHtml(n.time_ago)}</p>
                    </div>
                    ${isUnread ? '<span class="h-2 w-2 rounded-full bg-blue-500 flex-shrink-0 mt-2"></span>' : ''}
                </div>`;
        }).join('');

        // Re-init feather icons for the new content
        if (typeof feather !== 'undefined') feather.replace();

        // Update badge
        if (notifBadge) {
            notifBadge.classList.toggle('hidden', !hasUnread);
        }

        // Store maxId for later
        notifList.dataset.maxId = maxId;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function loadNotifications() {
        fetch(window.BASE_URL + '/api/notifications.php')
            .then(res => res.json())
            .then(data => {
                if (data.notifications) {
                    renderNotifications(data.notifications);
                    notifLoaded = true;
                }
            })
            .catch(() => {
                notifList.innerHTML = `
                    <div class="px-4 py-6 text-center text-sm text-red-400">
                        Gagal memuat notifikasi.
                    </div>`;
            });
    }

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !notifDropdown.classList.contains('hidden');
            notifDropdown.classList.toggle('hidden');

            // Close user dropdown if open
            if (userDropdown) {
                userDropdown.classList.add('hidden');
                userMenuBtn && userMenuBtn.setAttribute('aria-expanded', 'false');
            }

            // Fetch on first open
            if (!isOpen && !notifLoaded) {
                loadNotifications();
            }
        });

        notifDropdown.addEventListener('click', (e) => e.stopPropagation());

        document.addEventListener('click', () => {
            notifDropdown.classList.add('hidden');
        });

        // Mark all as read
        if (notifMarkRead) {
            notifMarkRead.addEventListener('click', (e) => {
                e.stopPropagation();
                const maxId = parseInt(notifList.dataset.maxId || '0', 10);
                if (maxId > 0) setLastReadId(maxId);
                // Re-render
                loadNotifications();
            });
        }

        // Check for new notifications on page load (badge only)
        fetch(window.BASE_URL + '/api/notifications.php')
            .then(res => res.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    const lastReadId = getLastReadId();
                    const hasUnread = data.notifications.some(n => n.id > lastReadId);
                    if (notifBadge) notifBadge.classList.toggle('hidden', !hasUnread);
                }
            })
            .catch(() => {});
    }

    // ===================================================
    // 3. Auto-dismiss Flash Messages setelah 5 detik
    // ===================================================
    const flashMessages = document.querySelectorAll('[role="alert"]');
    flashMessages.forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'all 0.4s ease';
            el.style.opacity    = '0';
            el.style.transform  = 'translateY(-8px)';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });

    // ===================================================
    // 4. Aktifkan Feather Icons (jika ada)
    // ===================================================
    if (typeof feather !== 'undefined') {
        feather.replace({ width: '1em', height: '1em', 'stroke-width': 2 });
    }

    // ===================================================
    // 5. Konfirmasi Submit Form dengan Loading State
    // ===================================================
    const forms = document.querySelectorAll('form[id]');
    forms.forEach((form) => {
        form.addEventListener('submit', function () {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner"></span> Memproses...';
                // Reset jika ada error (halaman tidak redirect)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 8000);
            }
        });
    });

    // ===================================================
    // 6. Format Nominal Input sebagai Rupiah (preview)
    // ===================================================
    const nominalInput = document.getElementById('doc-nominal');
    const nominalPreview = document.createElement('p');
    if (nominalInput) {
        nominalPreview.className = 'text-xs text-emerald-600 font-medium mt-1 hidden';
        nominalInput.parentNode.appendChild(nominalPreview);

        nominalInput.addEventListener('input', function () {
            const val = parseFloat(this.value);
            if (!isNaN(val) && val > 0) {
                nominalPreview.textContent = 'Rp ' + val.toLocaleString('id-ID');
                nominalPreview.classList.remove('hidden');
            } else {
                nominalPreview.classList.add('hidden');
            }
        });
    }

    // ===================================================
    // 7. Highlight Baris Tabel Terpilih
    // ===================================================
    const tableRows = document.querySelectorAll('#documents-table tbody tr');
    tableRows.forEach((row) => {
        row.style.cursor = 'pointer';
    });

    // ===================================================
    // 8. Smooth Scroll untuk anchor links
    // ===================================================
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ===================================================
    // 9. Search input: submit saat tekan Enter
    // ===================================================
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                document.getElementById('filter-form')?.submit();
            }
        });
    }

    console.log('%c SiMArsip v1.0 ', 'background:#2563eb;color:#fff;padding:4px 10px;border-radius:6px;font-weight:bold;');
});
