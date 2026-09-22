<?php
/**
 * Script kembalikan nama lomba ke semula
 * Akses sekali via browser, lalu HAPUS file ini!
 * URL: https://mhqs.smpn1sumenep.sch.id/update_nama_lomba.php
 */
require_once 'config/database.php';

$stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = 'nama_lomba'");
$result = $stmt->execute(['Lomba Tahfidz Juz 30 Al-Quran 2024']);

if ($result) {
    echo "<h2 style='color:green;font-family:sans-serif;'>✅ Berhasil! Nama lomba dikembalikan menjadi: <b>Lomba Tahfidz Juz 30 Al-Quran 2024</b></h2>";
    echo "<p style='font-family:sans-serif;color:red;'><strong>⚠️ Segera hapus file ini dari hosting setelah digunakan!</strong></p>";
} else {
    echo "<h2 style='color:red;font-family:sans-serif;'>❌ Gagal update database.</h2>";
}
?>
