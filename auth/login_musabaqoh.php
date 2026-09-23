<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

$error = '';

if ($_POST) {
    validateCsrfToken();
    checkRateLimit('musabaqoh');
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Cek kredensial di database
    $stmt = $pdo->prepare("SELECT * FROM user_musabaqoh WHERE username = ? AND status = 'Aktif'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        resetRateLimit('musabaqoh');
        $_SESSION['musabaqoh_logged_in'] = true;
        $_SESSION['musabaqoh_user'] = $user['username'];
        $_SESSION['musabaqoh_nama'] = $user['nama_lengkap'];
        header('Location: ../musabaqoh/pilih_jenis.php');
        exit();
    } else {
        incrementRateLimit('musabaqoh');
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Musabaqoh Hifdzul Qur'an</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #7f8c8d;
            font-size: 1em;
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
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .credentials-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .credentials-info h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .credentials-info small {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-lock me-2"></i>Login</h2>
            <p>Masuk untuk mengakses soal Musabaqoh</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <?php echo getCsrfInput(); ?>
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user me-2"></i>Username
                </label>
                <input type="text" 
                       class="form-control" 
                       id="username" 
                       name="username" 
                       required
                       autocomplete="username">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-key me-2"></i>Password
                </label>
                <input type="password" 
                       class="form-control" 
                       id="password" 
                       name="password" 
                       required
                       autocomplete="current-password">
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </div>
        </form>
        

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
