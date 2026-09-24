<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua';

// Ambil data soal
$soalPenyisihan = [];
$soalFinal = [];

try {
    if ($jenis === 'penyisihan' || $jenis === 'semua') {
        if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'")->rowCount()) {
            $soalPenyisihan = $pdo->query("SELECT no_soal, soal1, soal2, soal3 FROM soal_musabaqoh ORDER BY no_soal")->fetchAll();
        }
    }
    if ($jenis === 'final' || $jenis === 'semua') {
        if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'")->rowCount()) {
            $soalFinal = $pdo->query("SELECT no_soal, soal1, soal2, soal3 FROM soal_musabaqoh_final ORDER BY no_soal")->fetchAll();
        }
    }
} catch (PDOException $e) {
    die("Terjadi kesalahan dalam mengambil data!");
}

// Ambil nama lomba
$namaLomba = 'Lomba Tahfidz';
try {
    $stmt = $pdo->prepare("SELECT nilai FROM pengaturan WHERE nama_pengaturan = 'nama_lomba'");
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result) $namaLomba = $result['nilai'];
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <title>Export Soal Musabaqoh - PDF</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .header h1 {
            font-size: 18pt;
            color: #333;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14pt;
            color: #667eea;
            font-weight: normal;
        }

        .header .subtitle {
            font-size: 10pt;
            color: #888;
            margin-top: 5px;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #fff;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 20px 0 10px 0;
        }

        .section-title.penyisihan {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .section-title.final {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        table thead th {
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            color: #fff;
            border: 1px solid #ddd;
        }

        table.penyisihan thead th {
            background-color: #667eea;
        }

        table.final thead th {
            background-color: #f5576c;
        }

        table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tbody tr:hover {
            background-color: #e8f0fe;
        }

        .td-center {
            text-align: center;
        }

        .footer {
            text-align: center;
            font-size: 9pt;
            color: #999;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        .btn-toolbar {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-size: 11pt;
            cursor: pointer;
            text-decoration: none;
            color: white;
            margin: 0 5px;
            transition: all 0.3s ease;
        }

        .btn-print {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-back {
            background: #6c757d;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .empty-message {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="btn-toolbar no-print">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> 🖨️ Cetak / Simpan PDF
        </button>
        <a href="soal.php" class="btn btn-back">
            ← Kembali
        </a>
    </div>

    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars($namaLomba); ?></h1>
            <h2>Daftar Soal Musabaqoh<?php 
                if ($jenis === 'penyisihan') echo ' - Penyisihan';
                elseif ($jenis === 'final') echo ' - Final';
            ?></h2>
            <div class="subtitle">Diekspor pada: <?php echo date('d F Y, H:i'); ?> WIB</div>
        </div>

        <?php if (!empty($soalPenyisihan)): ?>
            <div class="section-title penyisihan">📋 Soal Penyisihan (<?php echo count($soalPenyisihan); ?> soal)</div>
            <table class="penyisihan">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 8%;">No Soal</th>
                        <th style="width: 29%;">Soal 1</th>
                        <th style="width: 29%;">Soal 2</th>
                        <th style="width: 29%;">Soal 3</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($soalPenyisihan as $s): ?>
                    <tr>
                        <td class="td-center"><?php echo $no++; ?></td>
                        <td class="td-center"><?php echo $s['no_soal']; ?></td>
                        <td><?php echo htmlspecialchars($s['soal1']); ?></td>
                        <td><?php echo htmlspecialchars($s['soal2']); ?></td>
                        <td><?php echo htmlspecialchars($s['soal3']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($jenis === 'penyisihan' || $jenis === 'semua'): ?>
            <div class="empty-message">Tidak ada data soal penyisihan.</div>
        <?php endif; ?>

        <?php if (!empty($soalFinal)): ?>
            <?php if (!empty($soalPenyisihan)): ?>
                <div class="page-break"></div>
            <?php endif; ?>
            <div class="section-title final">🏆 Soal Final (<?php echo count($soalFinal); ?> soal)</div>
            <table class="final">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 8%;">No Soal</th>
                        <th style="width: 29%;">Soal 1</th>
                        <th style="width: 29%;">Soal 2</th>
                        <th style="width: 29%;">Soal 3</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($soalFinal as $s): ?>
                    <tr>
                        <td class="td-center"><?php echo $no++; ?></td>
                        <td class="td-center"><?php echo $s['no_soal']; ?></td>
                        <td><?php echo htmlspecialchars($s['soal1']); ?></td>
                        <td><?php echo htmlspecialchars($s['soal2']); ?></td>
                        <td><?php echo htmlspecialchars($s['soal3']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($jenis === 'final' || $jenis === 'semua'): ?>
            <div class="empty-message">Tidak ada data soal final.</div>
        <?php endif; ?>

        <div class="footer">
            <?php echo htmlspecialchars($namaLomba); ?> &bull; 
            Dokumen ini dicetak pada <?php echo date('d/m/Y H:i:s'); ?>
        </div>
    </div>
</body>
</html>

