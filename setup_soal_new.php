<?php
session_start();
require_once 'config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Handle form submission untuk setup struktur baru
if ($_POST && isset($_POST['setup_new_structure'])) {
    try {
        // Baca file SQL
        $sqlFile = 'create_soal_new_structure.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception('File SQL tidak ditemukan!');
        }
        
        $sql = file_get_contents($sqlFile);
        
        // Split SQL menjadi array per statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        // Execute setiap statement
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        $success = 'Struktur soal baru berhasil dibuat!';
    } catch (Exception $e) {
        $error = 'Error membuat struktur baru: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Struktur Soal Baru - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .feature-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .feature-icon {
            font-size: 2rem;
            color: #007bff;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'admin/menu.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-cogs me-2"></i>Setup Struktur Soal Baru</h1>
                </div>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-info-circle me-2"></i>Informasi Struktur Baru</h5>
                            </div>
                            <div class="card-body">
                                <p>Struktur soal baru akan memiliki fitur-fitur berikut:</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="fas fa-tags"></i>
                                            </div>
                                            <h6>Kategori Soal</h6>
                                            <p class="text-muted">Organisasi soal berdasarkan jenis (Sambung Ayat, Tebak Surah, dll)</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <h6>Tingkat Kesulitan</h6>
                                            <p class="text-muted">Pengaturan tingkat kesulitan dari Mudah hingga Sangat Sulit</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="fas fa-key"></i>
                                            </div>
                                            <h6>Kunci Jawaban</h6>
                                            <p class="text-muted">Penyimpanan kunci jawaban untuk setiap soal</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="fas fa-history"></i>
                                            </div>
                                            <h6>Riwayat Soal</h6>
                                            <p class="text-muted">Tracking penggunaan soal dan skor peserta</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <h6>Waktu Pengerjaan</h6>
                                            <p class="text-muted">Pengaturan waktu pengerjaan per soal</p>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="feature-card">
                                            <div class="feature-icon">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <h6>Skor Maksimal</h6>
                                            <p class="text-muted">Pengaturan skor maksimal per soal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-database me-2"></i>Setup Database</h5>
                            </div>
                            <div class="card-body">
                                <p>Klik tombol di bawah untuk membuat struktur database baru:</p>
                                
                                <form method="POST">
                                    <button type="submit" name="setup_new_structure" class="btn btn-primary btn-lg w-100" onclick="return confirmSetup()">
                                        <i class="fas fa-cogs me-2"></i>Setup Struktur Baru
                                    </button>
                                </form>
                                
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Pastikan Anda sudah menghapus data soal lama terlebih dahulu
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5><i class="fas fa-list me-2"></i>Langkah Selanjutnya</h5>
                            </div>
                            <div class="card-body">
                                <ol class="small">
                                    <li>Setup struktur database</li>
                                    <li>Buka halaman kelola soal</li>
                                    <li>Tambah soal baru sesuai kebutuhan</li>
                                    <li>Test sistem soal baru</li>
                                </ol>
                                
                                <div class="mt-3">
                                    <a href="admin/soal_musabaqoh.php" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrow-right me-1"></i>Kelola Soal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmSetup() {
            return confirm('Apakah Anda yakin ingin membuat struktur soal baru?\n\nTindakan ini akan membuat tabel-tabel baru di database.');
        }
    </script>
</body>
</html>


