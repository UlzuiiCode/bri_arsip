-- ============================================================
-- Migration: Upgrade existing pkl_arsip_db
-- Jalankan script ini jika database sudah ada dan perlu di-upgrade
-- ============================================================

USE pkl_arsip_db;

-- 1. Tambah kolom status ke tabel users (jika belum ada)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'pkl_arsip_db' AND TABLE_NAME = 'users' AND COLUMN_NAME = 'status');
SET @sql = IF(@col_exists = 0, 
    "ALTER TABLE users ADD COLUMN status ENUM('pending', 'approved') NOT NULL DEFAULT 'pending' COMMENT 'Status persetujuan akun' AFTER role",
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Set semua user yang sudah ada menjadi approved
UPDATE users SET status = 'approved' WHERE status = 'pending' AND role = 'admin';

-- 2. Tambah kolom deleted_at ke tabel documents (jika belum ada)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'pkl_arsip_db' AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'deleted_at');
SET @sql = IF(@col_exists = 0, 
    "ALTER TABLE documents ADD COLUMN deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp' AFTER pihak_terkait",
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambah index untuk deleted_at
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'pkl_arsip_db' AND TABLE_NAME = 'documents' AND INDEX_NAME = 'idx_documents_deleted');
SET @sql = IF(@idx_exists = 0, 
    "ALTER TABLE documents ADD INDEX idx_documents_deleted (deleted_at)",
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Buat tabel login_attempts (jika belum ada)
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

-- 4. Update view
CREATE OR REPLACE VIEW vw_documents_summary AS
SELECT
    c.id            AS category_id,
    c.nama          AS category_nama,
    COUNT(d.id)     AS total_dokumen,
    MAX(d.created_at) AS dokumen_terakhir
FROM categories c
LEFT JOIN documents d ON d.category_id = c.id AND d.deleted_at IS NULL
GROUP BY c.id, c.nama;

SELECT 'Migration completed successfully!' AS result;
