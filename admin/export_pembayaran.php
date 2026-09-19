<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Ambil data pembayaran dengan join sekolah
try {
    $stmt = $pdo->query("
        SELECT p.*, s.nama_sekolah 
        FROM pembayaran p 
        JOIN sekolah s ON p.sekolah_id = s.id 
        ORDER BY p.created_at DESC
    ");
    $pembayaran_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Terjadi kesalahan dalam mengambil data!");
}

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Data_Pembayaran_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Mulai output Excel
echo '<table border="1">';
echo '<tr>';
echo '<th colspan="6" style="background-color: #4CAF50; color: white; text-align: center; font-size: 16px; font-weight: bold; padding: 10px;">DATA PEMBAYARAN LOMBA TAHFIDZ</th>';
echo '</tr>';
echo '<tr>';
echo '<th colspan="6" style="text-align: center; padding: 5px;">Tanggal Export: ' . date('d/m/Y H:i:s') . '</th>';
echo '</tr>';
echo '<tr style="background-color: #f2f2f2; font-weight: bold;">';
echo '<th style="padding: 8px; text-align: center;">No</th>';
echo '<th style="padding: 8px; text-align: center;">Nama Sekolah</th>';
echo '<th style="padding: 8px; text-align: center;">Nominal</th>';
echo '<th style="padding: 8px; text-align: center;">Status</th>';
echo '<th style="padding: 8px; text-align: center;">Tanggal Bayar</th>';
echo '<th style="padding: 8px; text-align: center;">Catatan</th>';
echo '</tr>';

$no = 1;
$total_nominal = 0;
$total_lunas = 0;
$total_pending = 0;
$total_ditolak = 0;

foreach ($pembayaran_list as $pembayaran) {
    echo '<tr>';
    echo '<td style="padding: 5px; text-align: center;">' . $no . '</td>';
    echo '<td style="padding: 5px;">' . htmlspecialchars($pembayaran['nama_sekolah']) . '</td>';
    echo '<td style="padding: 5px; text-align: right;">Rp ' . number_format($pembayaran['nominal']) . '</td>';
    echo '<td style="padding: 5px; text-align: center;">' . $pembayaran['status_pembayaran'] . '</td>';
    echo '<td style="padding: 5px; text-align: center;">' . date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])) . '</td>';
    echo '<td style="padding: 5px;">' . htmlspecialchars($pembayaran['catatan']) . '</td>';
    echo '</tr>';
    
    $total_nominal += $pembayaran['nominal'];
    
    if ($pembayaran['status_pembayaran'] == 'Lunas') {
        $total_lunas++;
    } elseif ($pembayaran['status_pembayaran'] == 'Pending') {
        $total_pending++;
    } elseif ($pembayaran['status_pembayaran'] == 'Ditolak') {
        $total_ditolak++;
    }
    
    $no++;
}

// Baris total
echo '<tr style="background-color: #e8f5e8; font-weight: bold;">';
echo '<td colspan="2" style="padding: 8px; text-align: center;">TOTAL</td>';
echo '<td style="padding: 8px; text-align: right;">Rp ' . number_format($total_nominal) . '</td>';
echo '<td colspan="3" style="padding: 8px; text-align: center;">' . count($pembayaran_list) . ' Pembayaran</td>';
echo '</tr>';

// Statistik
echo '<tr style="background-color: #f0f8ff;">';
echo '<td colspan="2" style="padding: 8px; text-align: center; font-weight: bold;">STATISTIK</td>';
echo '<td colspan="4" style="padding: 8px; text-align: center;">Lunas: ' . $total_lunas . ' | Pending: ' . $total_pending . ' | Ditolak: ' . $total_ditolak . '</td>';
echo '</tr>';

echo '</table>';
?>
