<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkRateLimit('sekolah');
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header('Location: ../index.php');
        exit();
    }
    
    try {
        // Cek data sekolah
        $stmt = $pdo->prepare("SELECT * FROM sekolah WHERE username = ? AND status = 'Aktif'");
        $stmt->execute([$username]);
        $sekolah = $stmt->fetch();
        
        if ($sekolah && password_verify($password, $sekolah['password'])) {
            // Login berhasil
            session_regenerate_id(true);
            resetRateLimit('sekolah');
            $_SESSION['sekolah_id'] = $sekolah['id'];
            $_SESSION['sekolah_username'] = $sekolah['username'];
            $_SESSION['sekolah_nama'] = $sekolah['nama_sekolah'];
            
            header('Location: ../sekolah/dashboard.php');
            exit();
        } else {
            incrementRateLimit('sekolah');
            $_SESSION['error'] = 'Username atau password salah!';
            header('Location: ../index.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan sistem!';
        header('Location: ../index.php');
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>
