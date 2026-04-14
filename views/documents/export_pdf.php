<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Dokumen — <?= APP_NAME ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 11pt;
            line-height: 1.5;
        }
        
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .header-title {
            font-size: 18pt;
            font-weight: 700;
            color: #1e293b;
        }
        .header-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }
        .header-logo {
            text-align: right;
            font-size: 9pt;
            color: #64748b;
        }
        .header-logo strong {
            display: block;
            font-size: 12pt;
            color: #1e293b;
        }
        
        .meta-info {
            display: flex;
            gap: 24px;
            margin-bottom: 16px;
            font-size: 9pt;
            color: #64748b;
        }
        .meta-info strong { color: #334155; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 8px;
        }
        thead th {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.05em;
        }
        tbody td {
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nominal { font-family: 'Courier New', monospace; font-weight: 500; }
        
        .footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
        
        .summary-box {
            display: inline-flex;
            gap: 24px;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 16px;
            font-size: 9pt;
        }
        .summary-box .item strong {
            display: block;
            font-size: 16pt;
            color: #1e293b;
            line-height: 1.2;
        }
        .summary-box .item span {
            color: #64748b;
        }

        .print-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(37,99,235,0.4);
            z-index: 50;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .print-btn:hover { background: #1d4ed8; }

        @media print {
            .print-btn { display: none !important; }
            body { font-size: 9pt; }
            .header { border-bottom-width: 2px; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
        </svg>
        Cetak / Simpan PDF
    </button>

    <div class="header">
        <div>
            <div class="header-title">Laporan Daftar Dokumen Arsip</div>
            <div class="header-subtitle">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</div>
        </div>
        <div class="header-logo">
            <strong><?= APP_NAME ?></strong>
            PT Bank Rakyat Indonesia (Persero) Tbk<br>
            Divisi Operasional & Transaksi
        </div>
    </div>

    <div class="meta-info">
        <?php if (!empty($_GET['search'])): ?>
        <div>Pencarian: <strong><?= htmlspecialchars($_GET['search']) ?></strong></div>
        <?php endif; ?>
        <?php if (!empty($_GET['category_id'])): 
            $filteredCat = '';
            foreach ($categories as $c) { if ($c['id'] == $_GET['category_id']) { $filteredCat = $c['nama']; break; } }
        ?>
        <div>Kategori: <strong><?= htmlspecialchars($filteredCat) ?></strong></div>
        <?php endif; ?>
        <div>Dicetak oleh: <strong><?= htmlspecialchars($_SESSION['user_nama'] ?? 'Admin') ?></strong></div>
    </div>

    <?php
        $totalNominal = 0;
        foreach ($documents as $d) { $totalNominal += (float)($d['nominal'] ?? 0); }
    ?>

    <div class="summary-box">
        <div class="item">
            <strong><?= count($documents) ?></strong>
            <span>Total Dokumen</span>
        </div>
        <?php if ($totalNominal > 0): ?>
        <div class="item">
            <strong>Rp <?= number_format($totalNominal, 0, ',', '.') ?></strong>
            <span>Total Nominal</span>
        </div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:35px">No</th>
                <th>Judul Dokumen</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th class="text-right">Nominal (Rp)</th>
                <th>Pihak Terkait</th>
                <th>Pengunggah</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($documents)): ?>
            <tr><td colspan="8" class="text-center" style="padding:24px;color:#94a3b8">Tidak ada dokumen ditemukan.</td></tr>
            <?php else: ?>
            <?php foreach ($documents as $i => $doc): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><strong><?= htmlspecialchars($doc['judul']) ?></strong></td>
                <td><?= htmlspecialchars($doc['nama_kategori'] ?? '-') ?></td>
                <td style="max-width:180px"><?= htmlspecialchars(mb_strimwidth($doc['deskripsi'] ?? '', 0, 80, '...')) ?></td>
                <td class="text-right nominal"><?= $doc['nominal'] ? number_format((float)$doc['nominal'], 0, ',', '.') : '-' ?></td>
                <td><?= htmlspecialchars($doc['pihak_terkait'] ?? '-') ?></td>
                <td><?= htmlspecialchars($doc['nama_uploader'] ?? '-') ?></td>
                <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <span><?= APP_NAME ?> — PT Bank Rakyat Indonesia (Persero) Tbk</span>
        <span>Halaman 1 dari 1</span>
    </div>
</body>
</html>
