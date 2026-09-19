<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai juri
if (!isset($_SESSION['juri_id'])) {
    header('Location: ../auth/login_juri.php');
    exit();
}

$juri_id = $_SESSION['juri_id'];
$success = '';
$error = '';

// Ambil data juri
$query_juri = "SELECT * FROM juri WHERE id = ?";
$stmt_juri = $pdo->prepare($query_juri);
$stmt_juri->execute([$juri_id]);
$juri = $stmt_juri->fetch();

if ($_POST) {
    validateCsrfToken();
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_hp = $_POST['no_hp'];
    $spesialisasi = $_POST['spesialisasi'];
    
    // Update data juri
    $query_update = "UPDATE juri SET nama_lengkap = ?, email = ?, no_hp = ?, spesialisasi = ? WHERE id = ?";
    $stmt_update = $pdo->prepare($query_update);
    
    if ($stmt_update->execute([$nama_lengkap, $email, $no_hp, $spesialisasi, $juri_id])) {
        $success = 'Profil berhasil diperbarui!';
        $_SESSION['juri_nama'] = $nama_lengkap;
        
        // Update data juri di session
        $juri['nama_lengkap'] = $nama_lengkap;
        $juri['email'] = $email;
        $juri['no_hp'] = $no_hp;
        $juri['spesialisasi'] = $spesialisasi;
    } else {
        $error = 'Gagal memperbarui profil!';
    }
}

// Handle change password
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verifikasi password lama
    if (!password_verify($old_password, $juri['password'])) {
        $error = 'Password lama salah!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Password baru dan konfirmasi password tidak sama!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query_password = "UPDATE juri SET password = ? WHERE id = ?";
        $stmt_password = $pdo->prepare($query_password);
        
        if ($stmt_password->execute([$hashed_password, $juri_id])) {
            $success = 'Password berhasil diubah!';
        } else {
            $error = 'Gagal mengubah password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Juri - Lomba Tahfidz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-user-edit me-2"></i>
                Profil Juri
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($juri['nama_lengkap']); ?></h3>
                        <p class="text-muted"><?php echo htmlspecialchars($juri['spesialisasi']); ?></p>
                        <span class="badge bg-<?php echo $juri['status'] == 'Aktif' ? 'success' : 'danger'; ?> fs-6">
                            <?php echo $juri['status']; ?>
                        </span>
                    </div>
                </div>

                <!-- Alert Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Edit Profil -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Profil
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                           value="<?php echo htmlspecialchars($juri['nama_lengkap']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" 
                                           value="<?php echo htmlspecialchars($juri['username']); ?>" readonly>
                                    <div class="form-text">Username tidak dapat diubah</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($juri['email']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">No HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                           value="<?php echo htmlspecialchars($juri['no_hp']); ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="spesialisasi" class="form-label">Spesialisasi</label>
                                <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" 
                                       value="<?php echo htmlspecialchars($juri['spesialisasi']); ?>">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Form Ubah Password -->
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Ubah Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="change_password" value="1">
                            
                            <div class="mb-3">
                                <label for="old_password" class="form-label">Password Lama</label>
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       minlength="6" required>
                                <div class="form-text">Password minimal 6 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       minlength="6" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-2"></i>Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi konfirmasi password
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Password tidak sama');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
