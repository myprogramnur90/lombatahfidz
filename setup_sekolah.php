<?php
require_once 'config/database.php';

try {
    // Tambah data sekolah contoh
    $nama_sekolah = 'SMA Negeri 1 Contoh';
    $npsn = '12345678';
    $alamat_sekolah = 'Jl. Contoh No. 123, Kota Contoh';
    $no_hp_sekolah = '081234567890';
    $email_sekolah = 'sman1contoh@email.com';
    $nama_kepala_sekolah = 'Dr. Contoh, M.Pd';
    $username = 'sman1contoh';
    $password = password_hash('sekolah123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO sekolah (nama_sekolah, npsn, alamat_sekolah, no_hp_sekolah, email_sekolah, nama_kepala_sekolah, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama_sekolah, $npsn, $alamat_sekolah, $no_hp_sekolah, $email_sekolah, $nama_kepala_sekolah, $username, $password]);
    
    echo "Data sekolah berhasil ditambahkan!<br>";
    echo "Username: sman1contoh<br>";
    echo "Password: sekolah123<br>";
    echo "<a href='index.php'>Kembali ke halaman login</a>";
    
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo "Data sekolah sudah ada!<br>";
        echo "Username: sman1contoh<br>";
        echo "Password: sekolah123<br>";
        echo "<a href='index.php'>Kembali ke halaman login</a>";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
