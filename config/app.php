<?php

/**
 * Konfigurasi Aplikasi Global
 */

// Path root aplikasi
defined('BASE_PATH') || define('BASE_PATH', dirname(__DIR__));
defined('BASE_URL')  || define('BASE_URL',  'http://localhost/pkl-arsip-php');

// Path folder upload
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL', BASE_URL . '/public/uploads/');

// Ukuran maksimum file upload (5MB)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Ekstensi file yang diizinkan
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png']);

// Nama Aplikasi
define('APP_NAME', 'Sistem Manajemen Arsip');
define('APP_VERSION', '1.0.0');

// Mulai sesi jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi zona waktu
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi error handling
ini_set('display_errors', 0); // Matikan di produksi
ini_set('log_errors', 1);
error_reporting(E_ALL);
