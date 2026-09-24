<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['musabaqoh_logged_in']) || !$_SESSION['musabaqoh_logged_in']) {
    header('Location: ../auth/login_musabaqoh.php');
    exit();
}

// Ambil jumlah soal dari database
try {
    $stmtPenyisihan = $pdo->query("SELECT COUNT(*) as total FROM soal_musabaqoh WHERE status = 'Aktif'");
    $totalPenyisihan = $stmtPenyisihan->fetch()['total'];
    
    $stmtFinal = $pdo->query("SELECT COUNT(*) as total FROM soal_musabaqoh_final WHERE status = 'Aktif'");
    $totalFinal = $stmtFinal->fetch()['total'];
} catch (PDOException $e) {
    $totalPenyisihan = 0;
    $totalFinal = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jenis Soal - Musabaqoh Hifdzul Qur'an</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-main {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .soal-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 3px solid transparent;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .soal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .soal-card.penyisihan {
            border-color: #3498db;
        }
        
        .soal-card.penyisihan:hover {
            border-color: #2980b9;
            background: linear-gradient(135deg, #f8f9fa, #e3f2fd);
        }
        
        .soal-card.final {
            border-color: #e74c3c;
        }
        
        .soal-card.final:hover {
            border-color: #c0392b;
            background: linear-gradient(135deg, #f8f9fa, #ffebee);
        }
        
        .card-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        
        .penyisihan .card-icon {
            color: #3498db;
        }
        
        .final .card-icon {
            color: #e74c3c;
        }
        
        .card-title {
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .card-description {
            color: #7f8c8d;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .card-features {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .card-features li {
            padding: 8px 0;
            color: #34495e;
        }
        
        .card-features li i {
            margin-right: 10px;
            width: 20px;
        }
        
        .penyisihan .card-features li i {
            color: #3498db;
        }
        
        .final .card-features li i {
            color: #e74c3c;
        }
        
        .user-info {
            background: rgba(52, 152, 219, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .user-info h5 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .user-info p {
            color: #7f8c8d;
            margin: 0;
        }
        
        
        .badge {
            font-size: 0.9em !important;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .soal-card.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-main">
            
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-book-quran me-3"></i>Musabaqoh Hifdzul Qur'an</h1>
                <p>Sistem Pemilihan Soal Juz 30</p>
            </div>
            
            <!-- User Info -->
            <div class="user-info">
                <h5><i class="fas fa-user me-2"></i>Selamat Datang</h5>
                <p>Login sebagai: <strong><?php echo htmlspecialchars($_SESSION['musabaqoh_nama'] ?? $_SESSION['musabaqoh_user']); ?></strong></p>
            </div>
            
            <!-- Card Pilihan -->
            <div class="card-container">
                <!-- Soal Penyisihan -->
                <a href="index.php?jenis=penyisihan" class="soal-card penyisihan <?php echo $totalPenyisihan == 0 ? 'disabled' : ''; ?>">
                    <div class="card-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="card-title">Soal Penyisihan</div>
                    <div class="card-description">
                        Soal untuk tahap penyisihan dengan tingkat kesulitan menengah
                    </div>
                    <div class="mb-3">
                        <?php if ($totalPenyisihan > 0): ?>
                            <span class="badge bg-primary fs-6"><?php echo $totalPenyisihan; ?> Soal Tersedia</span>
                        <?php else: ?>
                            <span class="badge bg-secondary fs-6">Tidak Ada Soal</span>
                        <?php endif; ?>
                    </div>
                    <ul class="card-features">
                        <li><i class="fas fa-check"></i> 2 Soal Sambung Ayat</li>
                        <li><i class="fas fa-check"></i> 1 Soal Sambung Surat</li>
                        <li><i class="fas fa-check"></i> Tingkat Kesulitan: Menengah</li>
                        <li><i class="fas fa-check"></i> Durasi: 10-15 menit</li>
                    </ul>
                </a>
                
                <!-- Soal Final -->
                <a href="index.php?jenis=final" class="soal-card final <?php echo $totalFinal == 0 ? 'disabled' : ''; ?>">
                    <div class="card-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="card-title">Soal Final</div>
                    <div class="card-description">
                        Soal untuk tahap final dengan tingkat kesulitan tinggi
                    </div>
                    <div class="mb-3">
                        <?php if ($totalFinal > 0): ?>
                            <span class="badge bg-danger fs-6"><?php echo $totalFinal; ?> Soal Tersedia</span>
                        <?php else: ?>
                            <span class="badge bg-secondary fs-6">Tidak Ada Soal</span>
                        <?php endif; ?>
                    </div>
                    <ul class="card-features">
                        <li><i class="fas fa-check"></i> 1 Soal Sambung Ayat</li>
                        <li><i class="fas fa-check"></i> 1 Soal Tebak Surah</li>
                        <li><i class="fas fa-check"></i> 1 Soal Sambung Surah</li>
                        <li><i class="fas fa-check"></i> Tingkat Kesulitan: Tinggi</li>
                        <li><i class="fas fa-check"></i> Durasi: 15-20 menit</li>
                    </ul>
                </a>
            </div>
            
            <!-- Info Tambahan -->
            <div class="text-center">
                <?php if ($totalPenyisihan == 0 && $totalFinal == 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Belum ada soal yang tersedia. Silakan hubungi admin untuk menambahkan soal.
                    </div>
                <?php elseif ($totalPenyisihan == 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Belum ada soal penyisihan yang tersedia. Hanya soal final yang dapat digunakan.
                    </div>
                <?php elseif ($totalFinal == 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Belum ada soal final yang tersedia. Hanya soal penyisihan yang dapat digunakan.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Pilih jenis soal sesuai dengan tahap lomba yang sedang berlangsung.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Controls -->
            <div class="text-center mt-4">
                <a href="../auth/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

