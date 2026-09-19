<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai juri
if (!isset($_SESSION['juri_id'])) {
    header('Location: ../auth/login_juri.php');
    exit();
}

$juri_id = $_SESSION['juri_id'];

// Ambil riwayat penilaian juri ini
$query_riwayat = "
    SELECT 
        p.*, 
        s.nama_sekolah,
        pen.skor_spesialisasi,
        pen.catatan,
        pen.tanggal_penilaian
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    JOIN penilaian pen ON p.id = pen.peserta_id
    WHERE pen.juri_id = ?
    ORDER BY pen.tanggal_penilaian DESC
";
$stmt_riwayat = $pdo->prepare($query_riwayat);
$stmt_riwayat->execute([$juri_id]);
$riwayat_list = $stmt_riwayat->fetchAll();

// Hitung statistik
$query_stats = "
    SELECT 
        COUNT(*) as total_penilaian,
        AVG(skor_spesialisasi) as rata_rata_skor,
        MIN(skor_spesialisasi) as skor_terendah,
        MAX(skor_spesialisasi) as skor_tertinggi
    FROM penilaian
    WHERE juri_id = ?
";
$stmt_stats = $pdo->prepare($query_stats);
$stmt_stats->execute([$juri_id]);
$stats = $stmt_stats->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penilaian - Juri</title>
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
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .badge {
            border-radius: 20px;
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-history me-2"></i>
                Riwayat Penilaian
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Riwayat Penilaian Saya
                        </h2>
                        <p class="text-muted mb-0">Riwayat penilaian yang telah Anda lakukan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                        <h3><?php echo $stats['total_penilaian']; ?></h3>
                        <p class="mb-0">Total Penilaian</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card success">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h3><?php echo number_format($stats['rata_rata_skor'], 2); ?></h3>
                        <p class="mb-0">Rata-rata Skor</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card warning">
                    <div class="card-body text-center">
                        <i class="fas fa-arrow-up fa-2x mb-2"></i>
                        <h3><?php echo number_format($stats['skor_tertinggi'], 2); ?></h3>
                        <p class="mb-0">Skor Tertinggi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card info">
                    <div class="card-body text-center">
                        <i class="fas fa-arrow-down fa-2x mb-2"></i>
                        <h3><?php echo number_format($stats['skor_terendah'], 2); ?></h3>
                        <p class="mb-0">Skor Terendah</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Daftar Penilaian
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peserta</th>
                                <th>Sekolah</th>
                                <th>Skor Spesialisasi</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($riwayat_list) > 0): ?>
                                <?php $no = 1; foreach ($riwayat_list as $riwayat): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($riwayat['nama_lengkap']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($riwayat['nisn']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($riwayat['nama_sekolah']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?php echo number_format($riwayat['skor_spesialisasi'], 2); ?></span>
                                        </td>
                                        <td>
                                            <small><?php echo date('d/m/Y H:i', strtotime($riwayat['tanggal_penilaian'])); ?></small>
                                        </td>
                                        <td>
                                            <a href="penilaian.php?id=<?php echo $riwayat['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                        <h5>Belum ada riwayat penilaian</h5>
                                        <p class="text-muted">Riwayat penilaian akan muncul setelah Anda melakukan penilaian.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
