<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil data laporan penilaian
$query_laporan = "
    SELECT 
        p.id,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        COUNT(pen.id) as jumlah_penilaian,
        AVG(pen.skor_tajwid) as avg_tajwid,
        AVG(pen.skor_fluency) as avg_fluency,
        AVG(pen.skor_makhraj) as avg_makhraj,
        AVG(pen.skor_keseluruhan) as avg_keseluruhan,
        MAX(pen.tanggal_penilaian) as tanggal_terakhir_penilaian
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id
    WHERE p.status = 'Diterima'
    GROUP BY p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
    ORDER BY avg_keseluruhan DESC, p.nama_lengkap
";
$result_laporan = $pdo->query($query_laporan);
$laporan_list = $result_laporan->fetchAll();

// Ambil statistik penilaian
$query_stats = "
    SELECT 
        COUNT(DISTINCT p.id) as total_peserta,
        COUNT(pen.id) as total_penilaian,
        COUNT(DISTINCT pen.juri_id) as total_juri_aktif,
        AVG(pen.skor_keseluruhan) as rata_rata_skor
    FROM peserta p
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id
    WHERE p.status = 'Diterima'
";
$stats = $pdo->query($query_stats)->fetch();

// Ambil data juri dan jumlah penilaian mereka
$query_juri_stats = "
    SELECT 
        j.nama_lengkap,
        j.spesialisasi,
        COUNT(pen.id) as jumlah_penilaian,
        AVG(pen.skor_keseluruhan) as rata_rata_skor
    FROM juri j
    LEFT JOIN penilaian pen ON j.id = pen.juri_id
    WHERE j.status = 'Aktif'
    GROUP BY j.id, j.nama_lengkap, j.spesialisasi
    ORDER BY jumlah_penilaian DESC
";
$result_juri_stats = $pdo->query($query_juri_stats);
$juri_stats_list = $result_juri_stats->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian - Admin</title>
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
        .ranking-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; }
        .ranking-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%); color: white; }
        .ranking-3 { background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%); color: white; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-chart-bar me-2"></i>
                Laporan Penilaian
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
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title mb-0">
                                    <i class="fas fa-chart-line me-2"></i>
                                    Laporan Hasil Penilaian
                                </h2>
                                <p class="text-muted mb-0">Rekapitulasi penilaian lomba tahfidz</p>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Cetak Laporan
                                </button>
                                <a href="export_penilaian.php" class="btn btn-success">
                                    <i class="fas fa-file-excel me-2"></i>Export Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3><?php echo $stats['total_peserta']; ?></h3>
                        <p class="mb-0">Total Peserta</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card success">
                    <div class="card-body text-center">
                        <i class="fas fa-clipboard-check fa-2x mb-2"></i>
                        <h3><?php echo $stats['total_penilaian']; ?></h3>
                        <p class="mb-0">Total Penilaian</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card warning">
                    <div class="card-body text-center">
                        <i class="fas fa-gavel fa-2x mb-2"></i>
                        <h3><?php echo $stats['total_juri_aktif']; ?></h3>
                        <p class="mb-0">Juri Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card info">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h3><?php echo number_format($stats['rata_rata_skor'], 2); ?></h3>
                        <p class="mb-0">Rata-rata Skor</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Ranking Peserta -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Ranking Peserta
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Nama Peserta</th>
                                        <th>Sekolah</th>
                                        <th>Jumlah Penilaian</th>
                                        <th>Skor Rata-rata</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($laporan_list) > 0): ?>
                                        <?php $rank = 1; foreach ($laporan_list as $peserta): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($rank <= 3): ?>
                                                        <span class="badge ranking-<?php echo $rank; ?>">
                                                            <i class="fas fa-trophy me-1"></i><?php echo $rank; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?php echo $rank; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($peserta['nisn']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?php echo $peserta['jumlah_penilaian']; ?>/3
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?php echo number_format($peserta['avg_keseluruhan'], 2); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        T: <?php echo number_format($peserta['avg_tajwid'], 1); ?> | 
                                                        F: <?php echo number_format($peserta['avg_fluency'], 1); ?> | 
                                                        M: <?php echo number_format($peserta['avg_makhraj'], 1); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="showDetail(<?php echo $peserta['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php $rank++; endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                                <h5>Belum ada data penilaian</h5>
                                                <p class="text-muted">Data penilaian akan muncul setelah juri melakukan penilaian.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Juri -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Statistik Juri
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($juri_stats_list) > 0): ?>
                            <?php foreach ($juri_stats_list as $juri): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($juri['nama_lengkap']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($juri['spesialisasi']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="badge bg-primary"><?php echo $juri['jumlah_penilaian']; ?> penilaian</div>
                                        <br>
                                        <small class="text-muted">Rata-rata: <?php echo number_format($juri['rata_rata_skor'], 2); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                <h6>Belum ada data juri</h6>
                                <p class="text-muted small">Data juri akan muncul setelah mereka melakukan penilaian.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Progress Penilaian -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Progress Penilaian
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $total_peserta = $stats['total_peserta'];
                        $total_penilaian = $stats['total_penilaian'];
                        $max_penilaian = $total_peserta * 3; // 3 juri per peserta
                        $progress_percentage = $max_penilaian > 0 ? ($total_penilaian / $max_penilaian) * 100 : 0;
                        ?>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="progress mb-3" style="height: 30px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $progress_percentage; ?>%">
                                        <?php echo number_format($progress_percentage, 1); ?>%
                                    </div>
                                </div>
                                <p class="text-muted">
                                    <strong><?php echo $total_penilaian; ?></strong> dari <strong><?php echo $max_penilaian; ?></strong> penilaian telah selesai
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <h4 class="text-success"><?php echo number_format($progress_percentage, 1); ?>%</h4>
                                <p class="text-muted mb-0">Progress Penilaian</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Penilaian -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2"></i>Detail Penilaian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetail(pesertaId) {
            // Load detail penilaian via AJAX
            fetch(`detail_penilaian.php?id=${pesertaId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailContent').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('modalDetail')).show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail penilaian');
                });
        }
    </script>
</body>
</html>
