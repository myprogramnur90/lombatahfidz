<?php
require_once __DIR__ . '/config/database.php';

try {
    // Cek apakah kolom no_urut sudah ada di tabel peserta
    $check = $pdo->query("SHOW COLUMNS FROM peserta LIKE 'no_urut'")->fetch();
    if (!$check) {
        $pdo->exec("ALTER TABLE peserta ADD COLUMN no_urut INT NULL DEFAULT NULL AFTER kelas");
        echo "✅ Kolom no_urut berhasil ditambahkan ke tabel peserta.<br>";
    } else {
        echo "ℹ️ Kolom no_urut sudah ada di tabel peserta.<br>";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
