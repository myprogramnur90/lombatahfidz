<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil data laporan penilaian final
$query_laporan = "
    SELECT 
        pf.id as peserta_final_id,
        pf.peserta_id,
        pf.peringkat_penyisihan,
        pf.skor_penyisihan,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        penf.skor_spesialisasi,
        penf.catatan,
        penf.tanggal_penilaian,
        j.nama_lengkap as nama_juri,
        j.spesialisasi
    FROM peserta_final pf
    JOIN peserta p ON pf.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian_final penf ON pf.id = penf.peserta_final_id
    LEFT JOIN juri j ON penf.juri_id = j.id
    WHERE pf.status = 'Aktif'
    ORDER BY penf.skor_spesialisasi DESC, pf.peringkat_penyisihan
";
$result_laporan = $pdo->query($query_laporan);
$laporan_list = $result_laporan->fetchAll();

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Laporan_Penilaian_Final_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <title>Laporan Penilaian Final</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .ranking-1 { background-color: #ffd700; }
        .ranking-2 { background-color: #c0c0c0; }
        .ranking-3 { background-color: #cd7f32; color: white; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">LAPORAN PENILAIAN FINAL</h2>
    <h3 style="text-align: center;">Lomba Tahfidz Juz 30 Al-Quran 2024</h3>
    <p style="text-align: center;">Tanggal Export: <?php echo date('d F Y H:i:s'); ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peringkat Final</th>
                <th>Nama Peserta</th>
                <th>NISN</th>
                <th>Sekolah</th>
                <th>Peringkat Penyisihan</th>
                <th>Skor Penyisihan</th>
                <th>Skor Juri</th>
                <th>Nama Juri</th>
                <th>Spesialisasi Juri</th>
                <th>Tanggal Penilaian</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $peringkat_final = 1;
            foreach ($laporan_list as $index => $laporan): 
                if ($index > 0 && $laporan['skor_spesialisasi'] != $laporan_list[$index-1]['skor_spesialisasi']) {
                    $peringkat_final = $index + 1;
                }
            ?>
                <tr class="<?php echo $peringkat_final <= 3 ? 'ranking-' . $peringkat_final : ''; ?>">
                    <td class="text-center"><?php echo $index + 1; ?></td>
                    <td class="text-center">
                        <?php echo $peringkat_final; ?>
                    </td>
                    <td><?php echo htmlspecialchars($laporan['nama_lengkap']); ?></td>
                    <td><?php echo htmlspecialchars($laporan['nisn']); ?></td>
                    <td><?php echo htmlspecialchars($laporan['nama_sekolah']); ?></td>
                    <td class="text-center"><?php echo $laporan['peringkat_penyisihan']; ?></td>
                    <td class="text-right"><?php echo number_format($laporan['skor_penyisihan'], 2); ?></td>
                    <td class="text-right">
                        <strong><?php echo $laporan['skor_spesialisasi'] ? number_format($laporan['skor_spesialisasi'], 2) : '-'; ?></strong>
                    </td>
                    <td><?php echo $laporan['nama_juri'] ? htmlspecialchars($laporan['nama_juri']) : '-'; ?></td>
                    <td><?php echo $laporan['spesialisasi'] ? htmlspecialchars($laporan['spesialisasi']) : '-'; ?></td>
                    <td class="text-center">
                        <?php echo $laporan['tanggal_penilaian'] ? date('d/m/Y H:i', strtotime($laporan['tanggal_penilaian'])) : '-'; ?>
                    </td>
                    <td><?php echo $laporan['catatan'] ? htmlspecialchars($laporan['catatan']) : '-'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br><br>
    
    <h4>Statistik Penilaian Final</h4>
    <table style="width: 50%;">
        <tr>
            <td><strong>Total Peserta Final:</strong></td>
            <td><?php echo count($laporan_list); ?></td>
        </tr>
        <tr>
            <td><strong>Peserta yang Sudah Dinilai:</strong></td>
            <td><?php echo count(array_filter($laporan_list, function($item) { return $item['skor_spesialisasi']; })); ?></td>
        </tr>
        <?php if (!empty($laporan_list)): ?>
            <?php 
            $skor_list = array_filter(array_column($laporan_list, 'skor_spesialisasi'));
            if (!empty($skor_list)) {
                $rata_rata = array_sum($skor_list) / count($skor_list);
                $tertinggi = max($skor_list);
                $terendah = min($skor_list);
            } else {
                $rata_rata = $tertinggi = $terendah = 0;
            }
            ?>
            <tr>
                <td><strong>Rata-rata Skor:</strong></td>
                <td><?php echo number_format($rata_rata, 2); ?></td>
            </tr>
            <tr>
                <td><strong>Skor Tertinggi:</strong></td>
                <td><?php echo number_format($tertinggi, 2); ?></td>
            </tr>
            <tr>
                <td><strong>Skor Terendah:</strong></td>
                <td><?php echo number_format($terendah, 2); ?></td>
            </tr>
        <?php endif; ?>
    </table>
    
    <br><br>
    
    <p style="text-align: center; font-size: 12px; color: #666;">
        Laporan ini dibuat secara otomatis oleh Sistem Manajemen Lomba Tahfidz<br>
        Tanggal: <?php echo date('d F Y H:i:s'); ?>
    </p>
</body>
</html>

