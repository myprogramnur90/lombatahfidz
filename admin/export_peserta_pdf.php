<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Get filter if any
$filter_sekolah = isset($_GET['sekolah']) ? $_GET['sekolah'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$where_conditions = [];
$params = [];

if (!empty($filter_sekolah)) {
    $where_conditions[] = "p.sekolah_id = ?";
    $params[] = $filter_sekolah;
}
if (!empty($filter_status)) {
    $where_conditions[] = "p.status = ?";
    $params[] = $filter_status;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Query peserta with joins
$query = "
    SELECT p.*, s.nama_sekolah,
           pb.status_pembayaran,
           db.status_dokumen
    FROM peserta p 
    JOIN sekolah s ON p.sekolah_id = s.id 
    LEFT JOIN (
        SELECT p1.sekolah_id, p1.status_pembayaran
        FROM pembayaran p1
        INNER JOIN (
            SELECT sekolah_id, MAX(created_at) as max_created
            FROM pembayaran
            GROUP BY sekolah_id
        ) p2 ON p1.sekolah_id = p2.sekolah_id AND p1.created_at = p2.max_created
    ) pb ON p.sekolah_id = pb.sekolah_id
    LEFT JOIN (
        SELECT d1.sekolah_id, d1.status_dokumen
        FROM dokumen_berka d1
        INNER JOIN (
            SELECT sekolah_id, MAX(created_at) as max_created
            FROM dokumen_berka
            GROUP BY sekolah_id
        ) d2 ON d1.sekolah_id = d2.sekolah_id AND d1.created_at = d2.max_created
    ) db ON p.sekolah_id = db.sekolah_id
    $where_clause
    ORDER BY s.nama_sekolah ASC, p.nama_lengkap ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pesertaList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$namaLomba = 'Lomba Tahfidz';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta - <?= htmlspecialchars($namaLomba) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #333; background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2c3e50; padding-bottom: 20px; }
        .header h1 { color: #2c3e50; font-size: 24px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { color: #7f8c8d; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f8f9fa; color: #2c3e50; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        .text-center { text-align: center; }
        .total-info { margin-top: 20px; font-size: 12px; color: #555; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        
        @media print {
            body { background-color: #fff; padding: 0; }
            .container { box-shadow: none; padding: 0; width: 100%; max-width: 100%; }
            @page { margin: 1.5cm; size: landscape; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DATA PESERTA</h1>
            <p><?= htmlspecialchars($namaLomba) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="20%">Nama Lengkap</th>
                    <th width="10%">NISN</th>
                    <th width="20%">Sekolah</th>
                    <th width="8%">Kelas</th>
                    <th class="text-center" width="7%">L/P</th>
                    <th width="10%">Status</th>
                    <th width="10%">Pembayaran</th>
                    <th width="10%">Berkas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pesertaList) > 0): ?>
                    <?php foreach ($pesertaList as $index => $row): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['nisn'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['nama_sekolah'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['kelas'] ?? '') ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['jenis_kelamin'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['status_pembayaran'] ?? 'Belum Bayar') ?></td>
                            <td><?= htmlspecialchars($row['status_dokumen'] ?? 'Belum Upload') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center" style="padding: 20px; color: #999;">Tidak ada data peserta</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="total-info">
            Total Data: <strong><?= count($pesertaList) ?></strong> peserta
        </div>

        <div class="footer">
            Dicetak pada: <?= date('d F Y H:i:s') ?>
        </div>
        
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer;">Print PDF</button>
            <button onclick="window.close(); window.location.href='peserta.php';" style="padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Tutup</button>
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
