# Sistem Manajemen Arsip (SiMArsip)
## PHP Native MVC — Manajemen Dokumen Arsip

---

## 📁 Struktur Proyek

```
pkl-arsip-php/
├── config/
│   ├── app.php              # Konfigurasi aplikasi (session, timezone, constants)
│   └── database.php         # Koneksi PDO MySQL
│
├── controllers/
│   ├── AuthController.php       # Login, Logout, Register
│   ├── DashboardController.php  # Halaman dashboard
│   ├── DocumentController.php   # CRUD dokumen + upload/download
│   ├── CategoryController.php   # CRUD kategori
│   ├── UserController.php       # Manajemen pengguna (admin)
│   └── ActivityLogController.php# Log audit
│
├── models/
│   ├── UserModel.php            # Query tabel users
│   ├── DocumentModel.php        # Query tabel documents
│   ├── CategoryModel.php        # Query tabel categories
│   └── ActivityLogModel.php     # Query tabel activity_logs
│
├── views/
│   ├── layouts/
│   │   ├── header.php           # HTML head + TailwindCSS CDN
│   │   ├── sidebar.php          # Sidebar + top navbar + flash messages
│   │   └── footer.php           # Footer + script JS
│   ├── auth/
│   │   ├── login.php            # Halaman login (glassmorphism dark)
│   │   └── register.php         # Halaman registrasi
│   ├── dashboard/
│   │   └── index.php            # Dashboard dengan stats & recent docs
│   ├── documents/
│   │   ├── index.php            # Daftar dokumen + search/filter
│   │   ├── create.php           # Form upload dokumen (drag & drop)
│   │   ├── show.php             # Detail dokumen
│   │   └── edit.php             # Form edit dokumen
│   ├── categories/
│   │   ├── index.php            # Grid kategori
│   │   ├── create.php           # Form tambah kategori
│   │   └── edit.php             # Form edit kategori
│   ├── users/
│   │   ├── index.php            # Daftar pengguna (admin only)
│   │   ├── create.php           # Form tambah pengguna
│   │   └── edit.php             # Form edit pengguna
│   └── activity_logs/
│       └── index.php            # Log aktivitas dengan pagination
│
├── public/
│   ├── css/
│   │   └── app.css              # CSS custom tambahan
│   ├── js/
│   │   └── app.js               # JavaScript — sidebar, dropdown, dll.
│   └── uploads/                 # File dokumen yang diupload
│
├── database/
│   └── schema.sql               # DDL + seeding data awal
│
├── .htaccess                    # Konfigurasi Apache (security + rewrite)
└── index.php                    # Entry point / Front Controller
```

---

## 🚀 Cara Setup

### 1. Prasyarat
- PHP 8.1+ dengan ekstensi: `pdo`, `pdo_mysql`, `fileinfo`
- MySQL 5.7+ / MariaDB 10.4+
- Apache dengan `mod_rewrite` aktif (XAMPP/Laragon/WAMP)

### 2. Setup Database
```sql
-- Jalankan di phpMyAdmin atau MySQL CLI:
source /path/to/pkl-arsip-php/database/schema.sql
```

### 3. Konfigurasi Koneksi
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'pkl_arsip_db');
define('DB_USER', 'root');
define('DB_PASS', '');    // Sesuaikan password MySQL Anda
```

Edit `config/app.php`:
```php
define('BASE_URL', 'http://localhost/pkl-arsip-php');
```

### 4. Jalankan
Letakkan folder di `htdocs/` (XAMPP) atau `www/` (Laragon), lalu buka:
```
http://localhost/pkl-arsip-php
```

---

## 🔐 Login Default
| Email | Password | Role |
|-------|----------|------|
| `admin@simaarsip.id` | `password` | Admin |

> **⚠️ PENTING:** Ganti password admin segera setelah setup pertama!

---

## 🛡️ Fitur Keamanan
- ✅ **PDO Prepared Statements** — bebas SQL injection
- ✅ **CSRF Token** pada semua form POST
- ✅ **`password_hash()`** Bcrypt untuk password
- ✅ **`session_regenerate_id()`** saat login (anti session fixation)
- ✅ **`htmlspecialchars()`** pada semua output (anti XSS)
- ✅ **`filter_input()`** validasi & sanitasi semua input
- ✅ **File upload validation** — ekstensi & ukuran file
- ✅ **Role-based access control** (Admin vs Pegawai)
- ✅ **Security headers** via `.htaccess`
- ✅ **Folder `config/` diblokir** via `.htaccess`

---

## 🎨 Teknologi
| Teknologi | Digunakan Untuk |
|-----------|----------------|
| PHP 8.1+  | Backend (MVC Procedural) |
| MySQL 8   | Database |
| PDO       | Koneksi database |
| TailwindCSS (CDN) | UI Styling |
| Feather Icons | Ikon UI |
| Google Fonts (Inter) | Typography |
| Vanilla JS | Sidebar toggle, dropdown, form |

---

## 🔄 Alur Routing
```
Browser → index.php (?page=xxx)
    → Cek autentikasi session
    → Switch-case dispatch ke Controller
    → Controller load Model + View
    → View render HTML dengan layout
```
