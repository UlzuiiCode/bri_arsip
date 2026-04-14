<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getDBConnection();
    
    echo "Memulai pembaruan kategori...\n";

    // 1. Set semua dokumen menjadi NULL kategorinya dulu agar tidak ada data yang 'nggantung'
    $db->exec("UPDATE documents SET category_id = NULL");
    echo "- Dokumen dikosongkan kategorinya.\n";

    // 2. Hapus semua kategori lama
    $db->exec("DELETE FROM categories");
    $db->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
    echo "- Kategori lama dihapus.\n";

    // 3. Masukkan kategori baru sesuai permintaan
    $newCategories = [
        ['nama' => 'Setoran', 'deskripsi' => 'Dokumen aktivitas setoran tunai/nontunai'],
        ['nama' => 'Penarikan', 'deskripsi' => 'Dokumen aktivitas penarikan tabungan/giro'],
        ['nama' => 'Transfer', 'deskripsi' => 'Dokumen pengiriman uang (internal/eksternal)'],
        ['nama' => 'Kliring', 'deskripsi' => 'Dokumen proses kliring antar bank'],
        ['nama' => 'Valas', 'deskripsi' => 'Dokumen transaksi valuta asing']
    ];

    $stmt = $db->prepare("INSERT INTO categories (nama, deskripsi) VALUES (:nama, :deskripsi)");
    foreach ($newCategories as $cat) {
        $stmt->execute($cat);
        echo "  > Menambahkan kategori: {$cat['nama']}\n";
    }

    echo "\nSelesai! Kategori sudah nyambung dengan fungsi Divisi Transaksi.\n";
    
} catch (Exception $e) {
    echo "Gagal memperbarui kategori: " . $e->getMessage() . "\n";
}
