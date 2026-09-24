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

// Set headers for Excel download
$filename = 'Data_Sekolah_' . date('Y-m-d') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
?>
<html>
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            padding: 10px 0;
        }
        .subtitle {
            font-size: 12px;
            text-align: center;
            padding-bottom: 10px;
            color: #555;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th {
            background-color: #4472C4;
            color: #FFFFFF;
            font-weight: bold;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #2F5496;
            white-space: nowrap;
        }
        td {
            padding: 6px;
            border: 1px solid #B4C6E7;
            vertical-align: top;
        }
        tr:nth-child(even) td {
            background-color: #D6E4F0;
        }
        tr:nth-child(odd) td {
            background-color: #FFFFFF;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="title">Data Sekolah</div>
    <div class="subtitle">Diekspor pada: <?= date('d F Y H:i:s') ?></div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Sekolah</th>
                <th>NPSN</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Email</th>
                <th>Nama Kepala Sekolah</th>
                <th>Username</th>
                <th>Status</th>
                <th>Tanggal Dibuat</th>
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
                        <td class="text-center"><?= htmlspecialchars($row['status'] ?? '') ?></td>
                        <td class="text-center"><?= isset($row['created_at']) ? date('d-m-Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data sekolah</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

