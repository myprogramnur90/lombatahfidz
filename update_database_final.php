<?php
require_once 'config/database.php';

try {
    echo "<h2>Update Database untuk Sistem Penilaian Final</h2>";
    
    // 1. Buat tabel peserta_final untuk menyimpan 6 peserta terbaik
    $sql_peserta_final = "
    CREATE TABLE IF NOT EXISTS peserta_final (
        id INT AUTO_INCREMENT PRIMARY KEY,
        peserta_id INT NOT NULL,
        skor_penyisihan DECIMAL(5,2) NOT NULL,
        peringkat_penyisihan INT NOT NULL,
        status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE CASCADE,
        UNIQUE KEY unique_peserta_final (peserta_id)
    )";
    
    $pdo->exec($sql_peserta_final);
    echo "✅ Tabel peserta_final berhasil dibuat<br>";
    
    // 2. Buat tabel penilaian_final untuk menyimpan hasil penilaian final
    $sql_penilaian_final = "
    CREATE TABLE IF NOT EXISTS penilaian_final (
        id INT AUTO_INCREMENT PRIMARY KEY,
        peserta_final_id INT NOT NULL,
        juri_id INT NOT NULL,
        skor_tajwid DECIMAL(5,2) NOT NULL DEFAULT 0,
        skor_fluency DECIMAL(5,2) NOT NULL DEFAULT 0,
        skor_makhraj DECIMAL(5,2) NOT NULL DEFAULT 0,
        skor_keseluruhan DECIMAL(5,2) NOT NULL DEFAULT 0,
        catatan TEXT,
        tanggal_penilaian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (peserta_final_id) REFERENCES peserta_final(id) ON DELETE CASCADE,
        FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
        UNIQUE KEY unique_penilaian_final (peserta_final_id, juri_id)
    )";
    
    $pdo->exec($sql_penilaian_final);
    echo "✅ Tabel penilaian_final berhasil dibuat<br>";
    
    // 3. Buat tabel assignment_juri_final untuk assignment juri ke peserta final
    $sql_assignment_final = "
    CREATE TABLE IF NOT EXISTS assignment_juri_final (
        id INT AUTO_INCREMENT PRIMARY KEY,
        juri_id INT NOT NULL,
        peserta_final_id INT NOT NULL,
        status ENUM('Assigned', 'Completed') DEFAULT 'Assigned',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
        FOREIGN KEY (peserta_final_id) REFERENCES peserta_final(id) ON DELETE CASCADE,
        UNIQUE KEY unique_juri_final_assignment (juri_id),
        UNIQUE KEY unique_peserta_final_assignment (peserta_final_id)
    )";
    
    $pdo->exec($sql_assignment_final);
    echo "✅ Tabel assignment_juri_final berhasil dibuat<br>";
    
    // 4. Tambahkan pengaturan untuk sistem final
    $sql_pengaturan_final = "
    INSERT IGNORE INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES 
    ('jumlah_peserta_final', '6', 'Jumlah peserta yang masuk ke babak final'),
    ('status_penyisihan', 'Aktif', 'Status babak penyisihan (Aktif/Selesai)'),
    ('status_final', 'Nonaktif', 'Status babak final (Aktif/Selesai)'),
    ('bobot_tajwid_final', '0.3', 'Bobot penilaian tajwid untuk final'),
    ('bobot_fluency_final', '0.4', 'Bobot penilaian fluency untuk final'),
    ('bobot_makhraj_final', '0.3', 'Bobot penilaian makhraj untuk final')
    ";
    
    $pdo->exec($sql_pengaturan_final);
    echo "✅ Pengaturan sistem final berhasil ditambahkan<br>";
    
    echo "<br><h3>Database berhasil diupdate untuk sistem penilaian final!</h3>";
    echo "<p><strong>Fitur yang ditambahkan:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Tabel peserta_final - untuk menyimpan 6 peserta terbaik</li>";
    echo "<li>✅ Tabel penilaian_final - untuk menyimpan hasil penilaian final</li>";
    echo "<li>✅ Tabel assignment_juri_final - untuk assignment juri ke peserta final</li>";
    echo "<li>✅ Pengaturan sistem final - konfigurasi bobot dan status</li>";
    echo "</ul>";
    
    echo "<p><strong>Langkah selanjutnya:</strong></p>";
    echo "<ol>";
    echo "<li>Akses halaman admin untuk memilih 6 peserta terbaik</li>";
    echo "<li>Setup assignment juri untuk peserta final</li>";
    echo "<li>Juri melakukan penilaian final</li>";
    echo "<li>Lihat laporan hasil final</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
