<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

/**
 * Import Peserta from uploaded file (.csv)
 * 
 * Expected columns:
 * Nama Lengkap, NISN, Username Sekolah, Tempat Lahir, Tanggal Lahir (YYYY-MM-DD), Jenis Kelamin (L/P), Kelas, No HP, Alamat
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file_import'])) {
    $_SESSION['import_error'] = 'Tidak ada file yang diunggah.';
    header('Location: peserta.php');
    exit();
}

$file = $_FILES['file_import'];

// Validate upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['import_error'] = 'Gagal mengunggah file. Silakan coba lagi.';
    header('Location: peserta.php');
    exit();
}

// Validate file extension
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowedExtensions = ['csv'];

if (!in_array($extension, $allowedExtensions)) {
    $_SESSION['import_error'] = 'Format file tidak didukung. Harap gunakan format .csv';
    header('Location: peserta.php');
    exit();
}

// Read CSV
$filePath = $file['tmp_name'];
$handle = fopen($filePath, "r");
if ($handle !== FALSE) {
    // Skip header
    fgetcsv($handle, 1000, ",");
    
    $successCount = 0;
    $errorCount = 0;
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) < 3) continue;
        
        $nama_lengkap = trim($data[0] ?? '');
        $nisn = trim($data[1] ?? '');
        $username_sekolah = trim($data[2] ?? '');
        $tempat_lahir = trim($data[3] ?? '');
        $tanggal_lahir = trim($data[4] ?? date('Y-m-d'));
        $jenis_kelamin = trim($data[5] ?? 'L');
        $kelas = trim($data[6] ?? '');
        $no_hp = trim($data[7] ?? '');
        $alamat = trim($data[8] ?? '');
        
        if (empty($nama_lengkap) || empty($nisn) || empty($username_sekolah)) {
            $errorCount++;
            continue;
        }
        
        try {
            // Get sekolah id from username
            $stmt = $pdo->prepare("SELECT id FROM sekolah WHERE username = ?");
            $stmt->execute([$username_sekolah]);
            $sekolah = $stmt->fetch();
            
            if (!$sekolah) {
                $errorCount++;
                continue;
            }
            
            $sekolah_id = $sekolah['id'];
            
            // Check if nisn already exists
            $stmt = $pdo->prepare("SELECT id FROM peserta WHERE nisn = ?");
            $stmt->execute([$nisn]);
            if ($stmt->fetch()) {
                $errorCount++;
                continue;
            }
            
            // Insert
            $stmt = $pdo->prepare("INSERT INTO peserta (sekolah_id, nama_lengkap, nisn, tempat_lahir, tanggal_lahir, jenis_kelamin, kelas, no_hp, alamat, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$sekolah_id, $nama_lengkap, $nisn, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $kelas, $no_hp, $alamat]);
            
            $successCount++;
        } catch (PDOException $e) {
            $errorCount++;
        }
    }
    fclose($handle);
    
    $_SESSION['import_success'] = "Berhasil mengimpor $successCount peserta. Gagal/Lewati: $errorCount.";
} else {
    $_SESSION['import_error'] = 'Gagal membaca file csv.';
}

header('Location: peserta.php');
exit();
