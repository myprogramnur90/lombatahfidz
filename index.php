<?php
require_once 'config/security.php';
initSecureSession();
setSecurityHeaders();
require_once 'config/database.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit();
} elseif (isset($_SESSION['sekolah_id'])) {
    header('Location: sekolah/dashboard.php');
    exit();
} elseif (isset($_SESSION['juri_id'])) {
    header('Location: juri/dashboard.php');
    exit();
} elseif (isset($_SESSION['musabaqoh_logged_in'])) {
    header('Location: musabaqoh/pilih_jenis.php');
    exit();
}

// Handler login terpusat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    validateCsrfToken();
    
    // Rate limiting
    $rateLimitMsg = checkRateLimit('index_login');
    if ($rateLimitMsg) {
        $_SESSION['error'] = $rateLimitMsg;
        header('Location: index.php');
        exit();
    }
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
    } else {
        try {
            $loggedIn = false;
            
            // 1. Cek Admin
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            if ($admin && password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                resetRateLimit('index_login');
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                $_SESSION['admin_logged_in'] = true;
                header('Location: admin/dashboard.php');
                exit();
            }

            // 2. Cek Juri
            $stmt = $pdo->prepare("SELECT * FROM juri WHERE username = ? AND status = 'Aktif'");
            $stmt->execute([$username]);
            $juri = $stmt->fetch();
            if ($juri && password_verify($password, $juri['password'])) {
                session_regenerate_id(true);
                resetRateLimit('index_login');
                $_SESSION['juri_id'] = $juri['id'];
                $_SESSION['juri_nama'] = $juri['nama_lengkap'];
                $_SESSION['juri_username'] = $juri['username'];
                header('Location: juri/dashboard.php');
                exit();
            }

            // 3. Cek Musabaqoh
            $stmt = $pdo->prepare("SELECT * FROM user_musabaqoh WHERE username = ? AND status = 'Aktif'");
            $stmt->execute([$username]);
            $userMusabaqoh = $stmt->fetch();
            if ($userMusabaqoh && password_verify($password, $userMusabaqoh['password'])) {
                session_regenerate_id(true);
                resetRateLimit('index_login');
                $_SESSION['musabaqoh_logged_in'] = true;
                $_SESSION['musabaqoh_user'] = $userMusabaqoh['username'];
                $_SESSION['musabaqoh_nama'] = $userMusabaqoh['nama_lengkap'];
                header('Location: musabaqoh/pilih_jenis.php');
                exit();
            }

            // 4. Cek Sekolah
            $stmt = $pdo->prepare("SELECT * FROM sekolah WHERE username = ? AND status = 'Aktif'");
            $stmt->execute([$username]);
            $sekolah = $stmt->fetch();
            if ($sekolah && password_verify($password, $sekolah['password'])) {
                session_regenerate_id(true);
                resetRateLimit('index_login');
                $_SESSION['sekolah_id'] = $sekolah['id'];
                $_SESSION['sekolah_username'] = $sekolah['username'];
                $_SESSION['sekolah_nama'] = $sekolah['nama_sekolah'];
                header('Location: sekolah/dashboard.php');
                exit();
            }

            // Jika tidak ada yang cocok
            if (!$loggedIn) {
                $_SESSION['error'] = 'Username atau password salah, atau akun tidak aktif!';
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Terjadi kesalahan sistem!';
        }
    }
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .btn-login {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .login-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 2rem;
        }
        .nav-link {
            border: none;
            border-radius: 10px 10px 0 0;
            margin-right: 5px;
            font-weight: 600;
            color: #6c757d;
        }
        .nav-link.active {
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-card">
                        <div class="login-header">
                            <h2><i class="fas fa-quran me-2"></i><?php echo getPengaturan('nama_lomba'); ?></h2>
                            <p class="mb-0">Sistem Pendaftaran Online</p>
                        </div>
                        <div class="login-body">
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <?php unset($_SESSION['error']); ?>
                            <?php endif; ?>
                            
                            <form action="index.php" method="POST">
                                <?php echo getCsrfInput(); ?>
                                <div class="mb-4">
                                    <label for="username" class="form-label fw-bold text-muted">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                        <input type="text" class="form-control form-control-lg bg-light" id="username" name="username" placeholder="sayang" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold text-muted">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-lock text-primary"></i></span>
                                        <input type="password" class="form-control form-control-lg bg-light" id="password" name="password" placeholder="Masukkan password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-login w-100 btn-lg shadow-sm">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login ke Sistem
                                </button>
                            </form>
                            
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kontak Panitia: <?php echo getPengaturan('kontak_panitia'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
