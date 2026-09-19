<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Ambil data peserta dengan join sekolah
try {
    $stmt = $pdo->query("
        SELECT p.*, s.nama_sekolah 
        FROM peserta p 
        JOIN sekolah s ON p.sekolah_id = s.id 
        ORDER BY s.nama_sekolah, p.nama_lengkap
    ");
    $peserta_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Terjadi kesalahan dalam mengambil data!");
}

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Data_Peserta_Lomba_Tahfidz_' . date('Y-m-d') . '.xls"');

// Output Excel
echo "<table border='1'>";
echo "<tr>";
echo "<th>No</th>";
echo "<th>Nama Lengkap</th>";
echo "<th>NISN</th>";
echo "<th>Sekolah</th>";
echo "<th>Kelas</th>";
echo "<th>Tempat Lahir</th>";
echo "<th>Tanggal Lahir</th>";
echo "<th>Jenis Kelamin</th>";
echo "<th>Alamat</th>";
echo "<th>No HP</th>";
echo "<th>Status</th>";
echo "<th>Tanggal Daftar</th>";
echo "<th>Catatan</th>";
echo "</tr>";

$no = 1;
foreach ($peserta_list as $peserta) {
    echo "<tr>";
    echo "<td>" . $no++ . "</td>";
    echo "<td>" . htmlspecialchars($peserta['nama_lengkap']) . "</td>";
    echo "<td>" . htmlspecialchars($peserta['nisn']) . "</td>";
    echo "<td>" . htmlspecialchars($peserta['nama_sekolah']) . "</td>";
    echo "<td>" . htmlspecialchars($peserta['kelas']) . "</td>";
    echo "<td>" . htmlspecialchars($peserta['tempat_lahir']) . "</td>";
    echo "<td>" . date('d/m/Y', strtotime($peserta['tanggal_lahir'])) . "</td>";
    echo "<td>" . ($peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan') . "</td>";
    echo "<td>" . htmlspecialchars($peserta['alamat']) . "</td>";
    echo "<td>" . htmlspecialchars($peserta['no_hp']) . "</td>";
    echo "<td>" . $peserta['status'] . "</td>";
    echo "<td>" . date('d/m/Y H:i', strtotime($peserta['tanggal_daftar'])) . "</td>";
    echo "<td>" . htmlspecialchars($peserta['catatan']) . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
