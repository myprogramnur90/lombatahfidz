<?php
// Konfigurasi Database
$host = 'localhost';
$port = '3307';
$dbname = 'lombatahfid';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage()); 
    die("Terjadi kesalahan koneksi database. Silakan hubungi administrator.");
}

// Fungsi untuk mendapatkan pengaturan sistem
function getPengaturan($nama) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT nilai FROM pengaturan WHERE nama_pengaturan = ?");
    $stmt->execute([$nama]);
    $result = $stmt->fetch();
    return $result ? $result['nilai'] : '';
}

// Fungsi untuk update pengaturan sistem
function updatePengaturan($nama, $nilai) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = ?");
    return $stmt->execute([$nilai, $nama]);
}

// Fungsi untuk mendapatkan soal yang belum terpakai
function getSoalBelumTerpakai($pdo) {
    $query = "
        SELECT s.* 
        FROM soal_musabaqoh s 
        LEFT JOIN soal_terpakai t ON s.no_soal = t.no_soal AND t.status = 'Aktif'
        WHERE s.status = 'Aktif' AND t.no_soal IS NULL
        ORDER BY s.no_soal
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Fungsi untuk menandai soal sebagai terpakai
function markSoalTerpakai($pdo, $no_soal) {
    $query = "INSERT INTO soal_terpakai (no_soal) VALUES (?)";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$no_soal]);
}

// Fungsi untuk reset semua soal terpakai
function resetSoalTerpakai($pdo) {
    $query = "UPDATE soal_terpakai SET status = 'Reset' WHERE status = 'Aktif'";
    $stmt = $pdo->prepare($query);
    return $stmt->execute();
}


// Fungsi untuk mendapatkan soal final yang belum terpakai
function getSoalFinalBelumTerpakai($pdo) {
    $query = "
        SELECT s.* 
        FROM soal_musabaqoh_final s 
        LEFT JOIN soal_terpakai_final t ON s.no_soal = t.no_soal AND t.status = 'Aktif'
        WHERE s.status = 'Aktif' AND t.no_soal IS NULL
        ORDER BY s.no_soal
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Fungsi untuk menandai soal final sebagai terpakai
function markSoalFinalTerpakai($pdo, $no_soal) {
    $query = "INSERT INTO soal_terpakai_final (no_soal) VALUES (?)";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$no_soal]);
}

// Fungsi untuk reset semua soal final terpakai
function resetSoalFinalTerpakai($pdo) {
    $query = "UPDATE soal_terpakai_final SET status = 'Reset' WHERE status = 'Aktif'";
    $stmt = $pdo->prepare($query);
    return $stmt->execute();
}

?>