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

// Set header untuk download Excel
$filename = 'Soal_Musabaqoh_' . ucfirst($jenis) . '_' . date('Y-m-d') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8"></head>';
echo '<body>';

// Soal Penyisihan
if (!empty($soalPenyisihan)) {
    echo '<h2>Soal Penyisihan</h2>';
    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
    echo '<tr style="background-color: #667eea; color: white; font-weight: bold;">';
    echo '<th>No</th>';
    echo '<th>No Soal</th>';
    echo '<th>Soal 1</th>';
    echo '<th>Soal 2</th>';
    echo '<th>Soal 3</th>';
    echo '</tr>';
    
    $no = 1;
    foreach ($soalPenyisihan as $s) {
        $bg = ($no % 2 == 0) ? ' style="background-color: #f2f2f2;"' : '';
        echo '<tr' . $bg . '>';
        echo '<td align="center">' . $no++ . '</td>';
        echo '<td align="center">' . $s['no_soal'] . '</td>';
        echo '<td>' . htmlspecialchars($s['soal1']) . '</td>';
        echo '<td>' . htmlspecialchars($s['soal2']) . '</td>';
        echo '<td>' . htmlspecialchars($s['soal3']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<br><br>';
}

// Soal Final
if (!empty($soalFinal)) {
    echo '<h2>Soal Final</h2>';
    echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
    echo '<tr style="background-color: #764ba2; color: white; font-weight: bold;">';
    echo '<th>No</th>';
    echo '<th>No Soal</th>';
    echo '<th>Soal 1</th>';
    echo '<th>Soal 2</th>';
    echo '<th>Soal 3</th>';
    echo '</tr>';
    
    $no = 1;
    foreach ($soalFinal as $s) {
        $bg = ($no % 2 == 0) ? ' style="background-color: #f2f2f2;"' : '';
        echo '<tr' . $bg . '>';
        echo '<td align="center">' . $no++ . '</td>';
        echo '<td align="center">' . $s['no_soal'] . '</td>';
        echo '<td>' . htmlspecialchars($s['soal1']) . '</td>';
        echo '<td>' . htmlspecialchars($s['soal2']) . '</td>';
        echo '<td>' . htmlspecialchars($s['soal3']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

echo '<br><p style="font-size: 10pt; color: #666;">Diekspor pada: ' . date('d/m/Y H:i:s') . '</p>';
echo '</body></html>';
?>

