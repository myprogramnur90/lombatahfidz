<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['musabaqoh_logged_in']) || !$_SESSION['musabaqoh_logged_in']) {
    header('Location: ../auth/login_musabaqoh.php');
    exit();
}

// Ambil jenis soal dari parameter
$jenis_soal = $_GET['jenis'] ?? 'penyisihan';

// Reset semua soal terpakai berdasarkan jenis
if ($jenis_soal === 'final') {
    resetSoalFinalTerpakai($pdo);
} else {
    resetSoalTerpakai($pdo);
}

// Redirect kembali ke halaman utama
header('Location: index.php?jenis=' . $jenis_soal);
exit();
?>
