<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil data penilaian berdasarkan spesialisasi
$query = "
    SELECT 
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        AVG(pen.skor_spesialisasi) as skor_rata_rata,
        MAX(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END) as skor_makhraj,
        MAX(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END) as skor_fluency,
        MAX(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END) as skor_tajwid_adab,
        COUNT(pen.id) as jumlah_penilaian,
        MAX(pen.tanggal_penilaian) as tanggal_terakhir_penilaian
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id
    LEFT JOIN juri j ON pen.juri_id = j.id
    WHERE p.status = 'Diterima'
    GROUP BY p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
        , (0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END), 0)
           + 0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END), 0)
           + 0.2 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END), 0)) as skor_terbobot
    ORDER BY skor_terbobot DESC
";

$result = $pdo->query($query);
$data = $result->fetchAll();

// Set header untuk download file Excel
$filename = 'Laporan_Penilaian_Spesialisasi_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tambahkan BOM untuk UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header CSV
$headers = [
    'No',
    'Nama Peserta',
    'NISN',
    'Sekolah',
    'Skor Makhraj Tajwid',
    'Skor Kelancaran (Tahfidz)',
    'Skor Shifatul Huruf',
    'Skor Terbobot (40/40/20)',
    'Jumlah Penilaian',
    'Status Penilaian',
    'Tanggal Terakhir Penilaian'
];
fputcsv($output, $headers);

// Data
$no = 1;
foreach ($data as $row) {
    $status = '';
    if ($row['jumlah_penilaian'] == 3) {
        $status = 'Lengkap';
    } elseif ($row['jumlah_penilaian'] > 0) {
        $status = 'Sebagian';
    } else {
        $status = 'Belum Dinilai';
    }
    
    $tanggal = $row['tanggal_terakhir_penilaian'] ? date('d/m/Y H:i', strtotime($row['tanggal_terakhir_penilaian'])) : '-';
    
    $csv_row = [
        $no++,
        $row['nama_lengkap'],
        $row['nisn'],
        $row['nama_sekolah'],
        $row['skor_makhraj'] ? number_format($row['skor_makhraj'], 1) : '-',
        $row['skor_fluency'] ? number_format($row['skor_fluency'], 1) : '-',
        $row['skor_tajwid_adab'] ? number_format($row['skor_tajwid_adab'], 1) : '-',
        $row['skor_rata_rata'] ? number_format($row['skor_rata_rata'], 1) : '-',
        $row['jumlah_penilaian'],
        $status,
        $tanggal
    ];
    
    fputcsv($output, $csv_row);
}

// Tambahkan summary di akhir
fputcsv($output, []); // Baris kosong
fputcsv($output, ['SUMMARY']);
fputcsv($output, ['Total Peserta', count($data)]);
fputcsv($output, ['Peserta Lengkap Dinilai', count(array_filter($data, function($row) { return $row['jumlah_penilaian'] == 3; }))]);
fputcsv($output, ['Peserta Sebagian Dinilai', count(array_filter($data, function($row) { return $row['jumlah_penilaian'] > 0 && $row['jumlah_penilaian'] < 3; }))]);
fputcsv($output, ['Peserta Belum Dinilai', count(array_filter($data, function($row) { return $row['jumlah_penilaian'] == 0; }))]);

$rata_rata_keseluruhan = array_filter(array_column($data, 'skor_rata_rata'), function($val) { return $val > 0; });
if (!empty($rata_rata_keseluruhan)) {
    fputcsv($output, ['Rata-rata Skor Keseluruhan', number_format(array_sum($rata_rata_keseluruhan) / count($rata_rata_keseluruhan), 1)]);
}

fputcsv($output, ['Tanggal Export', date('d/m/Y H:i:s')]);

fclose($output);
exit();
?>
