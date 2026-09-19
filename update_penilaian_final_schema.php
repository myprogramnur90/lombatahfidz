<?php
require 'config/database.php';
$pdo->exec("ALTER TABLE penilaian_final DROP COLUMN skor_tajwid");
$pdo->exec("ALTER TABLE penilaian_final DROP COLUMN skor_fluency");
$pdo->exec("ALTER TABLE penilaian_final DROP COLUMN skor_makhraj");
$pdo->exec("ALTER TABLE penilaian_final CHANGE COLUMN skor_keseluruhan skor_spesialisasi decimal(5,2) NOT NULL DEFAULT 0.00");
echo "Done";
