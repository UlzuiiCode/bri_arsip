<?php
require_once __DIR__ . '/config/database.php';

$db = getDBConnection();
$db->exec('ALTER TABLE documents ADD COLUMN tanggal_transaksi DATE NULL DEFAULT NULL AFTER pihak_terkait');
echo 'Column tanggal_transaksi added successfully!';
