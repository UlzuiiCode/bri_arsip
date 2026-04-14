-- ============================================================
-- DATABASE: pkl_arsip_db
-- Sistem Manajemen Arsip
-- Dibuat: 2026-04-11
-- Menggunakan: MySQL 8.x | InnoDB | utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS pkl_arsip_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pkl_arsip_db;

-- ============================================================
-- 1. TABEL: users
-- Menyimpan data pengguna sistem (admin dan pegawai)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(150)    NOT NULL,
    email       VARCHAR(255)    NOT NULL,
    foto_profil VARCHAR(255)        NULL          COMMENT 'Nama file foto (unik)',
    password    VARCHAR(255)    NOT NULL          COMMENT 'Bcrypt hash',
    role        ENUM('admin', 'pegawai')
                                NOT NULL DEFAULT 'pegawai',
    status      ENUM('pending', 'approved')
                                NOT NULL DEFAULT 'pending'
                                COMMENT 'Status persetujuan akun',
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY  uq_users_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabel pengguna sistem';

-- ============================================================
-- 2. TABEL: categories
-- Kategori pengelompokan dokumen
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nama        VARCHAR(100)    NOT NULL,
    deskripsi   TEXT                NULL,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY  uq_categories_nama (nama)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Kategori dokumen';

-- ============================================================
-- 3. TABEL: documents
-- Dokumen arsip utama (mendukung soft delete)
-- ============================================================
CREATE TABLE IF NOT EXISTS documents (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    judul           VARCHAR(255)        NOT NULL,
    deskripsi       TEXT                    NULL,
    file_path       VARCHAR(500)        NOT NULL  COMMENT 'Nama file di server (unique ID)',
    file_name       VARCHAR(255)        NOT NULL  COMMENT 'Nama asli file dari user',
    file_size       INT UNSIGNED            NULL  COMMENT 'Ukuran dalam bytes',
    file_type       VARCHAR(100)            NULL  COMMENT 'MIME type, e.g. application/pdf',
    category_id     INT UNSIGNED            NULL,
    uploaded_by     INT UNSIGNED            NULL  COMMENT 'FK ke users.id',
    nominal         DECIMAL(20,2)           NULL  COMMENT 'Nilai nominal transaksi (opsional)',
    pihak_terkait   VARCHAR(255)            NULL  COMMENT 'Pihak terkait transaksi (opsional)',
    deleted_at      TIMESTAMP               NULL  COMMENT 'Soft delete timestamp (NULL = aktif)',
    created_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX  idx_documents_category   (category_id),
    INDEX  idx_documents_uploader   (uploaded_by),
    INDEX  idx_documents_created    (created_at),
    INDEX  idx_documents_deleted    (deleted_at),
    FULLTEXT INDEX ft_documents_judul (judul, deskripsi),

    CONSTRAINT fk_documents_category
        FOREIGN KEY (category_id)
        REFERENCES  categories (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_documents_uploader
        FOREIGN KEY (uploaded_by)
        REFERENCES  users (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tabel dokumen arsip';

-- ============================================================
-- 4. TABEL: activity_logs
-- Log audit setiap tindakan pengguna
-- ============================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NULL  COMMENT 'NULL jika pengguna dihapus',
    action      VARCHAR(100)    NOT NULL  COMMENT 'Kode aksi, e.g. LOGIN, UPLOAD_DOCUMENT',
    description TEXT                NULL  COMMENT 'Deskripsi detail aksi',
    ip_address  VARCHAR(45)         NULL  COMMENT 'IPv4 atau IPv6',
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX  idx_logs_user    (user_id),
    INDEX  idx_logs_created (created_at),
    INDEX  idx_logs_action  (action),

    CONSTRAINT fk_logs_user
        FOREIGN KEY (user_id)
        REFERENCES  users (id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Log aktivitas pengguna (audit trail)';

-- ============================================================
-- 5. TABEL: login_attempts
-- Rate limiting untuk mencegah brute force
-- ============================================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    email        VARCHAR(255)    NOT NULL,
    ip_address   VARCHAR(45)     NOT NULL,
    attempted_at TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_attempts_email_ip  (email, ip_address),
    INDEX idx_attempts_time      (attempted_at)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Pencatatan percobaan login untuk rate limiting';

-- ============================================================
-- DATA AWAL (Seeding)
-- ============================================================

-- Akun Admin Default (password: Admin@12345)
INSERT INTO users (nama, email, password, role, status) VALUES
(
    'Administrator',
    'admin@simaarsip.id',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password (ganti sebelum produksi!)
    'admin',
    'approved'
);

-- Kategori Awal
INSERT INTO categories (nama, deskripsi) VALUES
('Surat Masuk',    'Dokumen surat yang diterima dari pihak eksternal'),
('Surat Keluar',   'Dokumen surat yang dikirim ke pihak eksternal'),
('Transaksi',      'Dokumen terkait transaksi keuangan'),
('Kontrak',        'Dokumen perjanjian dan kontrak kerja sama'),
('Laporan',        'Laporan periodik dan tahunan'),
('Nota Dinas',     'Nota dinas internal kantor'),
('Arsip Umum',     'Dokumen arsip umum lainnya');

-- ============================================================
-- VIEW: Ringkasan dokumen per kategori (exclude soft-deleted)
-- ============================================================
CREATE OR REPLACE VIEW vw_documents_summary AS
SELECT
    c.id            AS category_id,
    c.nama          AS category_nama,
    COUNT(d.id)     AS total_dokumen,
    MAX(d.created_at) AS dokumen_terakhir
FROM categories c
LEFT JOIN documents d ON d.category_id = c.id AND d.deleted_at IS NULL
GROUP BY c.id, c.nama;
