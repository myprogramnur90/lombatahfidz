<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    checkRateLimit('admin');
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header('Location: ../index.php');
        exit();
    }
    
    try {
        // Cek data admin
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Login berhasil
            session_regenerate_id(true);
            resetRateLimit('admin');
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            $_SESSION['admin_logged_in'] = true;
            
            header('Location: ../admin/dashboard.php');
            exit();
        } else {
            incrementRateLimit('admin');
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
