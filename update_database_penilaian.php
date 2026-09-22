<?php
require_once 'config/database.php';

echo "<h2>Update Database - Penambahan Fitur Penilaian</h2>";

try {
    // Buat tabel juri
    $sql_juri = "
    CREATE TABLE IF NOT EXISTS juri (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_lengkap VARCHAR(100) NOT NULL,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        no_hp VARCHAR(15),
        spesialisasi VARCHAR(100),
        status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($pdo->exec($sql_juri) !== false) {
        echo "<p style='color: green;'>✓ Tabel 'juri' berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>✗ Error membuat tabel juri</p>";
    }
    
    // Buat tabel penilaian
    $sql_penilaian = "
    CREATE TABLE IF NOT EXISTS penilaian (
        id INT AUTO_INCREMENT PRIMARY KEY,
        peserta_id INT NOT NULL,
        juri_id INT NOT NULL,
        skor_tajwid DECIMAL(5,2) NOT NULL DEFAULT 0,
        skor_fluency DECIMAL(5,2) NOT NULL DEFAULT 0,
        skor_makhraj DECIMAL(5,2) NOT NULL DEFAULT 0,
        skor_keseluruhan DECIMAL(5,2) NOT NULL DEFAULT 0,
        catatan TEXT,
        tanggal_penilaian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE CASCADE,
        FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
        UNIQUE KEY unique_penilaian (peserta_id, juri_id)
    )";
    
    if ($pdo->exec($sql_penilaian) !== false) {
        echo "<p style='color: green;'>✓ Tabel 'penilaian' berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>✗ Error membuat tabel penilaian</p>";
    }
    
    // Tambahkan pengaturan baru
    $pengaturan_baru = [
        ['maksimal_skor', '100', 'Skor maksimal untuk setiap aspek penilaian'],
        ['minimal_skor', '0', 'Skor minimal untuk setiap aspek penilaian']
    ];
    
    foreach ($pengaturan_baru as $pengaturan) {
        $stmt_cek = $pdo->prepare("SELECT id FROM pengaturan WHERE nama_pengaturan = ?");
        $stmt_cek->execute([$pengaturan[0]]);
        
        if ($stmt_cek->rowCount() == 0) {
            $stmt_insert = $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES (?, ?, ?)");
            
            if ($stmt_insert->execute([$pengaturan[0], $pengaturan[1], $pengaturan[2]])) {
                echo "<p style='color: green;'>✓ Pengaturan '{$pengaturan[0]}' berhasil ditambahkan</p>";
            } else {
                echo "<p style='color: red;'>✗ Error menambahkan pengaturan {$pengaturan[0]}</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠ Pengaturan '{$pengaturan[0]}' sudah ada</p>";
        }
    }
    
    // Insert juri default jika belum ada
    $result_cek = $pdo->query("SELECT COUNT(*) as total FROM juri");
    $total_juri = $result_cek->fetch()['total'];
    
    if ($total_juri == 0) {
        $juri_default = [
            ['Dr. Ahmad Al-Hafizh', 'juri1', 'juri123', 'juri1@lombatahfidz.com', '081111111111', 'Tajwid dan Makhraj'],
            ['Ust. Muhammad Qari', 'juri2', 'juri123', 'juri2@lombatahfidz.com', '081222222222', 'Fluency dan Tartil'],
            ['Ust. Abdullah Hafizh', 'juri3', 'juri123', 'juri3@lombatahfidz.com', '081333333333', 'Keseluruhan dan Hafalan']
        ];
        
        $stmt_insert_juri = $pdo->prepare("INSERT INTO juri (nama_lengkap, username, password, email, no_hp, spesialisasi) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($juri_default as $juri) {
            $hashedPassword = password_hash($juri[2], PASSWORD_DEFAULT);
            if ($stmt_insert_juri->execute([$juri[0], $juri[1], $hashedPassword, $juri[3], $juri[4], $juri[5]])) {
                echo "<p style='color: green;'>✓ Juri '{$juri[0]}' berhasil ditambahkan</p>";
            } else {
                echo "<p style='color: red;'>✗ Error menambahkan juri {$juri[0]}</p>";
            }
        }
    } else {
        echo "<p style='color: orange;'>⚠ Data juri sudah ada ({$total_juri} juri)</p>";
    }
    
    echo "<hr>";
    echo "<h3>Update Database Selesai!</h3>";
    echo "<p><strong>Fitur yang telah ditambahkan:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Tabel juri untuk menyimpan data juri penilaian</li>";
    echo "<li>✓ Tabel penilaian untuk menyimpan skor dari setiap juri</li>";
    echo "<li>✓ 3 juri default dengan kredensial login</li>";
    echo "<li>✓ Pengaturan skor maksimal dan minimal</li>";
    echo "</ul>";
    
    echo "<p><strong>Kredensial Login Juri Default:</strong></p>";
    echo "<ul>";
    echo "<li>Username: juri1, Password: juri123 (Dr. Ahmad Al-Hafizh)</li>";
    echo "<li>Username: juri2, Password: juri123 (Ust. Muhammad Qari)</li>";
    echo "<li>Username: juri3, Password: juri123 (Ust. Abdullah Hafizh)</li>";
    echo "</ul>";
    
    echo "<p><strong>Halaman yang dapat diakses:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Login Juri: <a href='auth/login_juri.php'>auth/login_juri.php</a></li>";
    echo "<li>✓ Dashboard Juri: <a href='juri/dashboard.php'>juri/dashboard.php</a></li>";
    echo "<li>✓ Manajemen Juri (Admin): <a href='admin/juri.php'>admin/juri.php</a></li>";
    echo "<li>✓ Laporan Penilaian (Admin): <a href='admin/laporan_penilaian.php'>admin/laporan_penilaian.php</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// PDO connection akan otomatis ditutup
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
ul {
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
