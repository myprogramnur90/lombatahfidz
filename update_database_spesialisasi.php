<?php
require_once 'config/database.php';

echo "<h2>Update Database - Spesialisasi Juri</h2>";

try {
    // Backup data penilaian yang ada (jika ada)
    $query_backup = "SELECT COUNT(*) as total FROM penilaian";
    $result_backup = $pdo->query($query_backup);
    $total_penilaian = $result_backup->fetch()['total'];
    
    if ($total_penilaian > 0) {
        echo "<p style='color: orange;'>⚠ Ada {$total_penilaian} data penilaian yang akan dihapus karena perubahan struktur</p>";
        
        // Hapus data penilaian lama
        $pdo->exec("DELETE FROM penilaian");
        echo "<p style='color: green;'>✓ Data penilaian lama berhasil dihapus</p>";
    }
    
    // Drop tabel penilaian lama
    $pdo->exec("DROP TABLE IF EXISTS penilaian");
    echo "<p style='color: green;'>✓ Tabel penilaian lama berhasil dihapus</p>";
    
    // Buat tabel penilaian baru dengan struktur spesialisasi
    $sql_penilaian = "
    CREATE TABLE penilaian (
        id INT AUTO_INCREMENT PRIMARY KEY,
        peserta_id INT NOT NULL,
        juri_id INT NOT NULL,
        skor_spesialisasi DECIMAL(5,2) NOT NULL DEFAULT 0,
        catatan TEXT,
        tanggal_penilaian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE CASCADE,
        FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
        UNIQUE KEY unique_penilaian (peserta_id, juri_id)
    )";
    
    if ($pdo->exec($sql_penilaian) !== false) {
        echo "<p style='color: green;'>✓ Tabel 'penilaian' baru berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>✗ Error membuat tabel penilaian baru</p>";
    }
    
    // Update data juri dengan spesialisasi yang benar
    $update_juri = [
        ['id' => 1, 'spesialisasi' => 'Makhraj'],
        ['id' => 2, 'spesialisasi' => 'Kelancaran Hafalan'],
        ['id' => 3, 'spesialisasi' => 'Tajwid dan Adab']
    ];
    
    echo "<h3>Update Spesialisasi Juri:</h3>";
    foreach ($update_juri as $juri) {
        $query_update = "UPDATE juri SET spesialisasi = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($query_update);
        
        if ($stmt_update->execute([$juri['spesialisasi'], $juri['id']])) {
            echo "<p style='color: green;'>✓ Juri {$juri['id']}: {$juri['spesialisasi']}</p>";
        } else {
            echo "<p style='color: red;'>✗ Gagal update juri {$juri['id']}</p>";
        }
    }
    
    // Cek data juri
    $query_juri = "SELECT * FROM juri ORDER BY id";
    $result_juri = $pdo->query($query_juri);
    $juri_list = $result_juri->fetchAll();
    
    echo "<hr>";
    echo "<h3>Data Juri dengan Spesialisasi:</h3>";
    echo "<ul>";
    foreach ($juri_list as $juri) {
        echo "<li><strong>{$juri['nama_lengkap']}</strong> - <em>{$juri['spesialisasi']}</em></li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>Update Database Selesai!</h3>";
    echo "<p><strong>Perubahan yang telah diterapkan:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Tabel penilaian diubah untuk mendukung spesialisasi juri</li>";
    echo "<li>✓ Juri 1: <strong>Makhraj</strong> (pengucapan huruf)</li>";
    echo "<li>✓ Juri 2: <strong>Kelancaran Hafalan</strong> (fluency)</li>";
    echo "<li>✓ Juri 3: <strong>Tajwid dan Adab</strong> (hukum tajwid dan etika)</li>";
    echo "<li>✓ Setiap juri hanya menilai aspek spesialisasinya</li>";
    echo "<li>✓ Skor keseluruhan akan dihitung dari gabungan 3 juri</li>";
    echo "</ul>";
    
    echo "<p><strong>Halaman yang akan diupdate:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Form Penilaian: <a href='juri/penilaian.php'>juri/penilaian.php</a></li>";
    echo "<li>✓ Dashboard Juri: <a href='juri/dashboard.php'>juri/dashboard.php</a></li>";
    echo "<li>✓ Laporan Penilaian: <a href='admin/laporan_penilaian.php'>admin/laporan_penilaian.php</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Cara Kerja Baru:</strong></p>";
    echo "<ol>";
    echo "<li>Setiap juri login dan melihat peserta yang di-assign</li>";
    echo "<li>Juri mengisi skor berdasarkan spesialisasinya (0-100)</li>";
    echo "<li>Sistem menggabungkan skor dari 3 juri untuk skor keseluruhan</li>";
    echo "<li>Admin dapat melihat laporan gabungan dari semua juri</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f5f5f5;
}
h2, h3 {
    color: #333;
}
p {
    margin: 10px 0;
}
ul, ol {
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
