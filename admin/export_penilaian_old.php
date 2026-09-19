<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil data laporan penilaian
$query_laporan = "
    SELECT 
        p.id,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        COUNT(pen.id) as jumlah_penilaian,
        AVG(pen.skor_tajwid) as avg_tajwid,
        AVG(pen.skor_fluency) as avg_fluency,
        AVG(pen.skor_makhraj) as avg_makhraj,
        AVG(pen.skor_keseluruhan) as avg_keseluruhan,
        MAX(pen.tanggal_penilaian) as tanggal_terakhir_penilaian
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id
    WHERE p.status = 'Diterima'
    GROUP BY p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
    ORDER BY avg_keseluruhan DESC, p.nama_lengkap
";
$result_laporan = $pdo->query($query_laporan);
$laporan_list = $result_laporan->fetchAll();

// Set header untuk download Excel
$filename = 'Laporan_Penilaian_Lomba_Tahfidz_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tambahkan BOM untuk UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header CSV
fputcsv($output, [
    'No',
    'Nama Lengkap',
    'NISN',
    'Sekolah',
    'Jumlah Penilaian',
    'Skor Tajwid (Rata-rata)',
    'Skor Fluency (Rata-rata)',
    'Skor Makhraj (Rata-rata)',
    'Skor Keseluruhan (Rata-rata)',
    'Tanggal Terakhir Penilaian',
    'Ranking'
]);

// Data peserta
$no = 1;
$rank = 1;
foreach ($laporan_list as $peserta) {
    fputcsv($output, [
        $no++,
        $peserta['nama_lengkap'],
        $peserta['nisn'],
        $peserta['nama_sekolah'],
        $peserta['jumlah_penilaian'],
        number_format($peserta['avg_tajwid'], 2),
        number_format($peserta['avg_fluency'], 2),
        number_format($peserta['avg_makhraj'], 2),
        number_format($peserta['avg_keseluruhan'], 2),
        $peserta['tanggal_terakhir_penilaian'] ? date('d/m/Y H:i', strtotime($peserta['tanggal_terakhir_penilaian'])) : '-',
        $rank++
    ]);
}

fclose($output);
exit();
?>
