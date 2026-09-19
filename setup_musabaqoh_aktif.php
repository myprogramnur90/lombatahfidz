<?php
// Setup tabel musabaqoh_aktif untuk tracking peserta dan soal yang sedang aktif
session_start();
require_once 'config/database.php';

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS musabaqoh_aktif (
            id INT AUTO_INCREMENT PRIMARY KEY,
            jenis ENUM('penyisihan', 'final') NOT NULL DEFAULT 'penyisihan',
            peserta_id INT DEFAULT NULL,
            no_soal INT DEFAULT NULL,
            status ENUM('Aktif', 'Selesai') DEFAULT 'Aktif',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE SET NULL
        )
    ");
    echo "Tabel musabaqoh_aktif berhasil dibuat!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
