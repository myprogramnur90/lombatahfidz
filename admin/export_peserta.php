<?php
require_once '../config/security.php';
initSecureSession();
require_once '../config/database.php';

// Pastikan user adalah admin
if (!isset($_SESSION['admin_id']) && (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin')) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Filter jika ada
$filter_sekolah = isset($_GET['sekolah']) ? trim($_GET['sekolah']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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
if (!empty($search)) {
    $where_conditions[] = "(p.nama_lengkap LIKE ? OR p.nisn LIKE ? OR s.nama_sekolah LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Ambil data peserta dengan join sekolah
try {
    $query = "
        SELECT p.*, s.nama_sekolah 
        FROM peserta p 
        LEFT JOIN sekolah s ON p.sekolah_id = s.id 
        $where_clause
        ORDER BY CASE WHEN p.no_urut IS NULL OR p.no_urut = 0 THEN 1 ELSE 0 END, p.no_urut ASC, s.nama_sekolah ASC, p.nama_lengkap ASC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $peserta_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Terjadi kesalahan dalam mengambil data: " . $e->getMessage());
}

// Set header untuk download Excel
$filename = 'Data_Peserta_Lomba_Tahfidz_' . date('Y-m-d') . '.xls';
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

// Output HTML Excel
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
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
        }
        th {
            background-color: #28a745;
            color: #FFFFFF;
            font-weight: bold;
            text-align: center;
            padding: 8px 6px;
            border: 1px solid #1e7e34;
            white-space: nowrap;
        }
        td {
            padding: 6px;
            border: 1px solid #cccccc;
            vertical-align: top;
        }
        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-str {
            mso-number-format: "\@";
        }
    </style>
</head>
<body>
    <div class="title">DATA PESERTA LOMBA TAHFIDZ</div>
    <div class="subtitle">Diekspor pada: <?= date('d/m/Y H:i:s') ?></div>

    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>No Urut</th>
                <th>Nama Lengkap</th>
                <th>NISN</th>
                <th>Sekolah</th>
                <th>Kelas</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>No HP</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($peserta_list) > 0): ?>
                <?php $no = 1; foreach ($peserta_list as $peserta): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center text-str"><?= (!empty($peserta['no_urut']) ? $peserta['no_urut'] : '-') ?></td>
                        <td><?= htmlspecialchars($peserta['nama_lengkap'] ?? '') ?></td>
                        <td class="text-center text-str"><?= htmlspecialchars($peserta['nisn'] ?? '') ?></td>
                        <td><?= htmlspecialchars($peserta['nama_sekolah'] ?? '') ?></td>
                        <td class="text-center"><?= htmlspecialchars($peserta['kelas'] ?? '') ?></td>
                        <td><?= htmlspecialchars($peserta['tempat_lahir'] ?? '') ?></td>
                        <td class="text-center"><?= !empty($peserta['tanggal_lahir']) ? date('d/m/Y', strtotime($peserta['tanggal_lahir'])) : '-' ?></td>
                        <td class="text-center"><?= (($peserta['jenis_kelamin'] ?? '') == 'L' ? 'Laki-laki' : 'Perempuan') ?></td>
                        <td><?= htmlspecialchars($peserta['alamat'] ?? '') ?></td>
                        <td class="text-center text-str"><?= htmlspecialchars($peserta['no_hp'] ?? '') ?></td>
                        <td class="text-center"><?= htmlspecialchars($peserta['status'] ?? '') ?></td>
                        <td class="text-center"><?= !empty($peserta['tanggal_daftar']) ? date('d/m/Y H:i', strtotime($peserta['tanggal_daftar'])) : '-' ?></td>
                        <td><?= htmlspecialchars($peserta['catatan'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14" class="text-center">Tidak ada data peserta</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
