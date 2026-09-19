<?php
require_once 'config/database.php';

echo "<h2>Setup Tabel Soal Final</h2>";

try {
    // Buat tabel soal_musabaqoh_final jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS soal_musabaqoh_final (
        id INT AUTO_INCREMENT PRIMARY KEY,
        no_soal INT NOT NULL,
        soal1 TEXT NOT NULL,
        soal2 TEXT NOT NULL,
        soal3 TEXT NOT NULL,
        status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_no_soal_final (no_soal)
    )";
    
    $pdo->exec($sql);
    echo "<p>✅ Tabel soal_musabaqoh_final berhasil dibuat/diperiksa</p>";
    
    // Insert data soal final jika belum ada
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM soal_musabaqoh_final");
    $count = $stmt->fetch();
    
    if ($count['count'] == 0) {
        $soalData = [
            [1, 'Sambung Ayat - An-Naba ayat 11-15', 'Tebak Surah - An-Nazi\'at', 'Sambung Surah - An-Naba ke An-Nazi\'at'],
            [2, 'Sambung Ayat - An-Nazi\'at ayat 11-15', 'Tebak Surah - \'Abasa', 'Sambung Surah - An-Nazi\'at ke \'Abasa'],
            [3, 'Sambung Ayat - \'Abasa ayat 11-15', 'Tebak Surah - At-Takwir', 'Sambung Surah - \'Abasa ke At-Takwir'],
            [4, 'Sambung Ayat - At-Takwir ayat 11-15', 'Tebak Surah - Al-Infitar', 'Sambung Surah - At-Takwir ke Al-Infitar'],
            [5, 'Sambung Ayat - Al-Infitar ayat 11-15', 'Tebak Surah - Al-Mutaffifin', 'Sambung Surah - Al-Infitar ke Al-Mutaffifin'],
            [6, 'Sambung Ayat - Al-Mutaffifin ayat 11-15', 'Tebak Surah - Al-Inshiqaq', 'Sambung Surah - Al-Mutaffifin ke Al-Inshiqaq'],
            [7, 'Sambung Ayat - Al-Inshiqaq ayat 11-15', 'Tebak Surah - Al-Buruj', 'Sambung Surah - Al-Inshiqaq ke Al-Buruj'],
            [8, 'Sambung Ayat - Al-Buruj ayat 11-15', 'Tebak Surah - At-Tariq', 'Sambung Surah - Al-Buruj ke At-Tariq'],
            [9, 'Sambung Ayat - At-Tariq ayat 11-15', 'Tebak Surah - Al-A\'la', 'Sambung Surah - At-Tariq ke Al-A\'la'],
            [10, 'Sambung Ayat - Al-A\'la ayat 11-15', 'Tebak Surah - Al-Ghashiyah', 'Sambung Surah - Al-A\'la ke Al-Ghashiyah']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO soal_musabaqoh_final (no_soal, soal1, soal2, soal3) VALUES (?, ?, ?, ?)");
        foreach ($soalData as $soal) {
            $stmt->execute($soal);
        }
        echo "<p>✅ Data soal final berhasil ditambahkan (10 soal)</p>";
    } else {
        echo "<p>✅ Data soal final sudah ada (" . $count['count'] . " soal)</p>";
    }
    
    // Buat tabel soal_terpakai_final jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS soal_terpakai_final (
        id INT AUTO_INCREMENT PRIMARY KEY,
        no_soal INT NOT NULL,
        tanggal_pakai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('Aktif', 'Reset') DEFAULT 'Aktif',
        FOREIGN KEY (no_soal) REFERENCES soal_musabaqoh_final(no_soal) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "<p>✅ Tabel soal_terpakai_final berhasil dibuat/diperiksa</p>";
    
    echo "<p><strong>✅ Tabel soal final sudah siap!</strong></p>";
    echo "<p><a href='index.php'>Kembali ke halaman utama</a></p>";
    echo "<p><a href='musabaqoh/pilih_jenis.php'>Akses Musabaqoh</a></p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
