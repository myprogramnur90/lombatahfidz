<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil data laporan penilaian berdasarkan spesialisasi
$query_laporan = "
    SELECT 
        p.id,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        COUNT(pen.id) as jumlah_penilaian,
        AVG(pen.skor_spesialisasi) as skor_rata_rata,
        MAX(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END) as skor_makhraj,
        MAX(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END) as skor_fluency,
        MAX(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END) as skor_tajwid_adab,
        (0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END), 0)
         + 0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END), 0)
         + 0.2 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END), 0)) as skor_terbobot,
        GROUP_CONCAT(DISTINCT CONCAT(j.nama_lengkap, ' (', j.spesialisasi, ': ', pen.skor_spesialisasi, ')') SEPARATOR ', ') as detail_penilaian,
        MAX(pen.tanggal_penilaian) as tanggal_terakhir_penilaian
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id
    LEFT JOIN juri j ON pen.juri_id = j.id
    WHERE p.status = 'Diterima'
    GROUP BY p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
    ORDER BY skor_terbobot DESC, p.nama_lengkap
";
$result_laporan = $pdo->query($query_laporan);
$laporan_list = $result_laporan->fetchAll();

// Ambil statistik penilaian
$query_stats = "
    SELECT 
        COUNT(DISTINCT p.id) as total_peserta,
        COUNT(pen.id) as total_penilaian,
        COUNT(DISTINCT pen.juri_id) as total_juri_aktif,
        AVG(pen.skor_spesialisasi) as rata_rata_skor
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
        AVG(pen.skor_spesialisasi) as rata_rata_skor
    FROM juri j
    LEFT JOIN penilaian pen ON j.id = pen.juri_id
    GROUP BY j.id, j.nama_lengkap, j.spesialisasi
    ORDER BY j.spesialisasi
";
$juri_stats = $pdo->query($query_juri_stats)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian Spesialisasi - Admin</title>
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
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .badge {
            border-radius: 20px;
            padding: 8px 12px;
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
        .ranking-badge {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #333;
            font-weight: bold;
        }
        .skor-cell {
            font-weight: bold;
            text-align: center;
        }
        .skor-excellent { color: #28a745; }
        .skor-good { color: #17a2b8; }
        .skor-fair { color: #ffc107; }
        .skor-poor { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h2 class="card-title mb-0">
                                                <i class="fas fa-chart-bar me-2"></i>
                                                Laporan Penilaian Spesialisasi
                                            </h2>
                                            <p class="text-muted mb-0">Laporan hasil penilaian berdasarkan spesialisasi juri</p>
                                        </div>
                                        <div>
                                            <a href="export_penilaian.php" class="btn btn-success">
                                                <i class="fas fa-file-csv me-2"></i>Export CSV
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
                                    <i class="fas fa-star fa-2x mb-2"></i>
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
                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                    <h3><?php echo number_format($stats['rata_rata_skor'], 1); ?></h3>
                                    <p class="mb-0">Rata-rata Skor</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Juri -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-tie me-2"></i>
                                        Statistik Juri
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($juri_stats as $juri): ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card border-primary">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title text-primary"><?php echo htmlspecialchars($juri['nama_lengkap']); ?></h6>
                                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($juri['spesialisasi']); ?></p>
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <strong><?php echo $juri['jumlah_penilaian']; ?></strong>
                                                                <br><small>Penilaian</small>
                                                            </div>
                                                            <div class="col-6">
                                                                <strong><?php echo number_format($juri['rata_rata_skor'], 1); ?></strong>
                                                                <br><small>Rata-rata</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Laporan -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
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
                                            <th>NISN</th>
                                            <th>Sekolah</th>
                                            <th class="text-center">Makhraj Tajwid</th>
                                            <th class="text-center">Kelancaran (Tahfidz)</th>
                                            <th class="text-center">Shifatul Huruf</th>
                                            <th class="text-center">Skor Terbobot (40/40/20)</th>
                                            <th class="text-center">Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($laporan_list) > 0): ?>
                                            <?php $rank = 1; foreach ($laporan_list as $laporan): ?>
                                                <tr>
                                                    <td>
                                                        <?php if ($laporan['skor_rata_rata'] > 0): ?>
                                                            <span class="badge ranking-badge">#<?php echo $rank++; ?></span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($laporan['nama_lengkap']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($laporan['nisn']); ?></td>
                                                    <td><?php echo htmlspecialchars($laporan['nama_sekolah']); ?></td>
                                                    <td class="skor-cell">
                                                        <?php if ($laporan['skor_makhraj']): ?>
                                                            <span class="<?php echo $laporan['skor_makhraj'] >= 80 ? 'skor-excellent' : ($laporan['skor_makhraj'] >= 60 ? 'skor-good' : 'skor-poor'); ?>">
                                                                <?php echo number_format($laporan['skor_makhraj'], 1); ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="skor-cell">
                                                        <?php if ($laporan['skor_fluency']): ?>
                                                            <span class="<?php echo $laporan['skor_fluency'] >= 80 ? 'skor-excellent' : ($laporan['skor_fluency'] >= 60 ? 'skor-good' : 'skor-poor'); ?>">
                                                                <?php echo number_format($laporan['skor_fluency'], 1); ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="skor-cell">
                                                        <?php if ($laporan['skor_tajwid_adab']): ?>
                                                            <span class="<?php echo $laporan['skor_tajwid_adab'] >= 80 ? 'skor-excellent' : ($laporan['skor_tajwid_adab'] >= 60 ? 'skor-good' : 'skor-poor'); ?>">
                                                                <?php echo number_format($laporan['skor_tajwid_adab'], 1); ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="skor-cell">
                                                        <?php if ($laporan['skor_terbobot']): ?>
                                                            <span class="<?php echo $laporan['skor_terbobot'] >= 80 ? 'skor-excellent' : ($laporan['skor_terbobot'] >= 60 ? 'skor-good' : 'skor-poor'); ?>">
                                                                <strong><?php echo number_format($laporan['skor_terbobot'], 1); ?></strong>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($laporan['jumlah_penilaian'] == 3): ?>
                                                            <span class="badge bg-success">Lengkap</span>
                                                        <?php elseif ($laporan['jumlah_penilaian'] > 0): ?>
                                                            <span class="badge bg-warning">Sebagian</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Belum</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="detail_penilaian.php?id=<?php echo $laporan['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
