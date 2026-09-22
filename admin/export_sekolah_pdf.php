<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Query all sekolah ordered by nama_sekolah
$stmt = $pdo->query("SELECT * FROM sekolah ORDER BY nama_sekolah ASC");
$sekolahList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get application name from settings
$namaLomba = '';
if (function_exists('getPengaturan')) {
    $namaLomba = getPengaturan('nama_lomba');
}
if (empty($namaLomba)) {
    $namaLomba = 'Lomba Tahfidz';
}

$tanggal = date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sekolah - <?= htmlspecialchars($namaLomba) ?></title>
    <style>
        /* Screen styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px double #333;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #222;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
        }

        .header .date {
            font-size: 11px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: 600;
            text-align: center;
            padding: 8px 5px;
            border: 1px solid #1a252f;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
            font-size: 10.5px;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #eef2f7;
        }

        .text-center {
            text-align: center;
        }

        .status-aktif {
            color: #27ae60;
            font-weight: 600;
        }

        .status-nonaktif {
            color: #e74c3c;
            font-weight: 600;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #999;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn-print {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
            transition: background-color 0.3s;
        }

        .btn-print:hover {
            background-color: #2980b9;
        }

        .btn-back {
            background-color: #95a5a6;
            color: #fff;
            border: none;
            padding: 10px 30px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
        }

        .total-info {
            margin-top: 10px;
            font-size: 11px;
            color: #555;
        }

        /* Print styles */
        @media print {
            @page {
                size: landscape;
                margin: 10mm 8mm;
            }

            body {
                background: #fff;
                padding: 0;
                font-size: 9px;
            }

            .container {
                box-shadow: none;
                padding: 0;
                border-radius: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }

            .header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .header h1 {
                font-size: 16px;
            }

            .header h2 {
                font-size: 13px;
            }

            th {
                background-color: #2c3e50 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 9px;
                padding: 5px 3px;
            }

            td {
                font-size: 9px;
                padding: 4px 3px;
            }

            tr:nth-child(even) td {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <a href="sekolah.php" class="btn-back">← Kembali</a>
    </div>

    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($namaLomba) ?></h1>
            <h2>Data Sekolah</h2>
            <div class="date">Tanggal: <?= $tanggal ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Sekolah</th>
                    <th style="width: 80px;">NPSN</th>
                    <th>Alamat</th>
                    <th style="width: 90px;">No HP</th>
                    <th>Email</th>
                    <th>Nama Kepala Sekolah</th>
                    <th style="width: 80px;">Username</th>
                    <th style="width: 55px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sekolahList) > 0): ?>
                    <?php $no = 1; foreach ($sekolahList as $row): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_sekolah'] ?? '') ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['npsn'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['alamat_sekolah'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['no_hp_sekolah'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['email_sekolah'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['nama_kepala_sekolah'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
                            <td class="text-center <?= (strtolower($row['status'] ?? '') === 'aktif') ? 'status-aktif' : 'status-nonaktif' ?>">
                                <?= htmlspecialchars($row['status'] ?? '') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #999;">Tidak ada data sekolah</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-info">
            Total Data: <strong><?= count($sekolahList) ?></strong> sekolah
        </div>

        <div class="footer">
            Dicetak pada: <?= date('d F Y H:i:s') ?>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
