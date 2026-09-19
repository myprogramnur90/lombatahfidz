<?php
/**
 * Security Helper - Lomba Tahfidz
 * File ini berisi fungsi-fungsi keamanan yang digunakan di seluruh aplikasi
 */

// =============================================
// SESSION SECURITY CONFIGURATION
// =============================================
function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Konfigurasi cookie session yang aman
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Aktifkan secure cookie jika menggunakan HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', 1);
        }
        
        session_start();
    }
    
    // Set session timeout (30 menit)
    $timeout = 1800;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

// =============================================
// SECURITY HEADERS
// =============================================
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// =============================================
// CSRF PROTECTION
// =============================================
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function getCsrfInput() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

function validateCsrfToken() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            http_response_code(403);
            die('<h1>403 Forbidden</h1><p>Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.</p>');
        }
    }
}

// =============================================
// RATE LIMITING (Login)
// =============================================
function checkRateLimit($key = 'login', $maxAttempts = 5, $lockoutTime = 300) {
    $attemptsKey = 'rate_limit_' . $key . '_attempts';
    $timeKey = 'rate_limit_' . $key . '_time';
    
    if (!isset($_SESSION[$attemptsKey])) {
        $_SESSION[$attemptsKey] = 0;
        $_SESSION[$timeKey] = time();
    }
    
    // Reset jika waktu lockout sudah lewat
    if (isset($_SESSION[$timeKey]) && (time() - $_SESSION[$timeKey]) > $lockoutTime) {
        $_SESSION[$attemptsKey] = 0;
        $_SESSION[$timeKey] = time();
    }
    
    if ($_SESSION[$attemptsKey] >= $maxAttempts) {
        $remaining = $lockoutTime - (time() - $_SESSION[$timeKey]);
        $minutes = ceil($remaining / 60);
        return "Terlalu banyak percobaan login. Coba lagi dalam {$minutes} menit.";
    }
    
    return null; // No rate limit hit
}

function incrementRateLimit($key = 'login') {
    $attemptsKey = 'rate_limit_' . $key . '_attempts';
    $_SESSION[$attemptsKey] = ($_SESSION[$attemptsKey] ?? 0) + 1;
}

function resetRateLimit($key = 'login') {
    $attemptsKey = 'rate_limit_' . $key . '_attempts';
    $timeKey = 'rate_limit_' . $key . '_time';
    unset($_SESSION[$attemptsKey], $_SESSION[$timeKey]);
}

// =============================================
// SECURE LOGOUT
// =============================================
function secureLogout($redirectUrl = '../index.php') {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    header('Location: ' . $redirectUrl);
    exit();
}

// =============================================
// FILE UPLOAD VALIDATION
// =============================================
function validateFileUpload($fileKey, $options = []) {
    $defaults = [
        'max_size' => 2 * 1024 * 1024, // 2MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
    ];
    $opts = array_merge($defaults, $options);
    
    // Cek apakah file ada
    if (!isset($_FILES[$fileKey]) || !is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
        return ['success' => false, 'error' => 'File tidak ditemukan.'];
    }
    
    $file = $_FILES[$fileKey];
    
    // Cek error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Gagal mengupload file. Kode error: ' . $file['error']];
    }
    
    // Cek ukuran file
    if ($file['size'] > $opts['max_size']) {
        $maxMB = $opts['max_size'] / (1024 * 1024);
        return ['success' => false, 'error' => "Ukuran file melebihi {$maxMB}MB!"];
    }
    
    // Cek ekstensi file
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $opts['allowed_extensions'])) {
        return ['success' => false, 'error' => 'Ekstensi file tidak diizinkan! Hanya: ' . implode(', ', $opts['allowed_extensions'])];
    }
    
    // Cek MIME type sebenarnya
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $opts['allowed_types'])) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan!'];
    }
    
    return ['success' => true, 'extension' => $ext, 'mime_type' => $mimeType];
}

function generateSafeFilename($prefix, $extension) {
    return $prefix . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
}

function ensureUploadDir($dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// =============================================
// INPUT SANITIZATION
// =============================================
function sanitizeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// =============================================
// AUTH CHECK HELPERS
// =============================================
function requireAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ../auth/login_admin.php');
        exit();
    }
}

function requireSekolahLogin() {
    if (!isset($_SESSION['sekolah_id'])) {
        header('Location: ../auth/login_sekolah.php');
        exit();
    }
}

function requireJuriLogin() {
    if (!isset($_SESSION['juri_id'])) {
        header('Location: ../auth/login_juri.php');
        exit();
    }
}

function requireMusabaqohLogin() {
    if (!isset($_SESSION['musabaqoh_logged_in'])) {
        header('Location: ../auth/login_musabaqoh.php');
        exit();
    }
}
?>
