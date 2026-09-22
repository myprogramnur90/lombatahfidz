<?php
// Konfigurasi Database - CONTOH / TEMPLATE
// Salin file ini menjadi config/database.php lalu isi dengan kredensial asli

$host = 'localhost';
$port = '3306';
$dbname = 'nama_database';      // Ganti dengan nama database dari cPanel
$username = 'nama_user_mysql';  // Ganti dengan username MySQL dari cPanel
$password = 'password_mysql';   // Ganti dengan password MySQL dari cPanel

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage()); 
    die("Terjadi kesalahan koneksi database. Silakan hubungi administrator.");
}
?>
