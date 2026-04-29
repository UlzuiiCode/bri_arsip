# Struktur Tabel Database - SiMArsip

Dokumen ini menjelaskan struktur setiap tabel pada database `pkl_arsip_db` Sistem Informasi Manajemen Arsip.

---

## 1. Tabel `users`

Menyimpan data pengguna sistem (admin dan pegawai).

| No | *Field* | Tipe Data | Panjang | Keterangan | Key |
|----|---------|-----------|---------|------------|-----|
| 1 | id | INT UNSIGNED | - | Auto Increment | PK |
| 2 | nama | VARCHAR | 150 | Nama lengkap pengguna, NOT NULL | - |
| 3 | email | VARCHAR | 255 | Email pengguna, UNIQUE, NOT NULL | UNIQUE |
| 4 | password | VARCHAR | 255 | Password hash (Bcrypt), NOT NULL | - |
| 5 | role | ENUM | - | Role pengguna: 'admin' / 'pegawai', DEFAULT 'pegawai' | - |
| 6 | status | ENUM | - | Status persetujuan akun: 'pending' / 'approved', DEFAULT 'pending' | - |
| 7 | created_at | TIMESTAMP | - | Waktu pembuatan record, DEFAULT CURRENT_TIMESTAMP | - |
| 8 | updated_at | TIMESTAMP | - | Waktu update terakhir, ON UPDATE CURRENT_TIMESTAMP | - |

---

## 2. Tabel `categories`

Kategori pengelompokan dokumen.

| No | *Field* | Tipe Data | Panjang | Keterangan | Key |
|----|---------|-----------|---------|------------|-----|
| 1 | id | INT UNSIGNED | - | Auto Increment | PK |
| 2 | nama | VARCHAR | 100 | Nama kategori, UNIQUE, NOT NULL | UNIQUE |
| 3 | deskripsi | TEXT | - | Deskripsi kategori, nullable | - |
| 4 | created_at | TIMESTAMP | - | Waktu pembuatan record, DEFAULT CURRENT_TIMESTAMP | - |
| 5 | updated_at | TIMESTAMP | - | Waktu update terakhir, ON UPDATE CURRENT_TIMESTAMP | - |

---

## 3. Tabel `documents`

Dokumen arsip utama (mendukung soft delete).

| No | *Field* | Tipe Data | Panjang | Keterangan | Key |
|----|---------|-----------|---------|------------|-----|
| 1 | id | INT UNSIGNED | - | Auto Increment | PK |
| 2 | judul | VARCHAR | 255 | Judul dokumen, NOT NULL | - |
| 3 | deskripsi | TEXT | - | Deskripsi dokumen, nullable | - |
| 4 | file_path | VARCHAR | 500 | Nama file di server (unique ID), NOT NULL | - |
| 5 | file_name | VARCHAR | 255 | Nama asli file dari user, NOT NULL | - |
| 6 | file_size | INT UNSIGNED | - | Ukuran file dalam bytes, nullable | - |
| 7 | file_type | VARCHAR | 100 | MIME type (e.g. application/pdf), nullable | - |
| 8 | category_id | INT UNSIGNED | - | FK ke categories.id, nullable, ON DELETE SET NULL | FK |
| 9 | uploaded_by | INT UNSIGNED | - | FK ke users.id, nullable, ON DELETE SET NULL | FK |
| 10 | nominal | DECIMAL | 20,2 | Nilai nominal transaksi, nullable | - |
| 11 | pihak_terkait | VARCHAR | 255 | Pihak terkait transaksi, nullable | - |
| 12 | deleted_at | TIMESTAMP | - | Soft delete timestamp (NULL = aktif), nullable | - |
| 13 | created_at | TIMESTAMP | - | Waktu pembuatan record, DEFAULT CURRENT_TIMESTAMP | - |
| 14 | updated_at | TIMESTAMP | - | Waktu update terakhir, ON UPDATE CURRENT_TIMESTAMP | - |

---

## 4. Tabel `activity_logs`

Log audit setiap tindakan pengguna.

| No | *Field* | Tipe Data | Panjang | Keterangan | Key |
|----|---------|-----------|---------|------------|-----|
| 1 | id | BIGINT UNSIGNED | - | Auto Increment | PK |
| 2 | user_id | INT UNSIGNED | - | FK ke users.id, nullable (NULL jika pengguna dihapus), ON DELETE SET NULL | FK |
| 3 | action | VARCHAR | 100 | Kode aksi (e.g. LOGIN, UPLOAD_DOCUMENT), NOT NULL | - |
| 4 | description | TEXT | - | Deskripsi detail aksi, nullable | - |
| 5 | ip_address | VARCHAR | 45 | Alamat IP (IPv4/IPv6), nullable | - |
| 6 | created_at | TIMESTAMP | - | Waktu pencatatan log, DEFAULT CURRENT_TIMESTAMP | - |

---

## 5. Tabel `login_attempts`

Pencatatan percobaan login untuk rate limiting.

| No | *Field* | Tipe Data | Panjang | Keterangan | Key |
|----|---------|-----------|---------|------------|-----|
| 1 | id | BIGINT UNSIGNED | - | Auto Increment | PK |
| 2 | email | VARCHAR | 255 | Email yang dicoba login, NOT NULL | - |
| 3 | ip_address | VARCHAR | 45 | Alamat IP pengguna, NOT NULL | - |
| 4 | attempted_at | TIMESTAMP | - | Waktu percobaan login, DEFAULT CURRENT_TIMESTAMP | - |
