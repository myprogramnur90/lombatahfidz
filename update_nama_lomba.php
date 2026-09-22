<?php
/**
 * Script update nama lomba di database
 * Akses sekali via browser, lalu HAPUS file ini!
 * URL: https://mhqs.smpn1sumenep.sch.id/update_nama_lomba.php
 */
require_once 'config/database.php';

$stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = 'nama_lomba'");
$result = $stmt->execute(['MHQS 2026']);

if ($result) {
    echo "<h2 style='color:green;font-family:sans-serif;'>✅ Berhasil! Nama lomba diubah menjadi: <b>MHQS 2026</b></h2>";
    echo "<p style='font-family:sans-serif;color:red;'><strong>⚠️ Segera hapus file ini dari hosting setelah digunakan!</strong></p>";
} else {
    echo "<h2 style='color:red;font-family:sans-serif;'>❌ Gagal update database.</h2>";
}
?>
