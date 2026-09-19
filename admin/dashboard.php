<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil statistik
try {
    $total_sekolah = $pdo->query("SELECT COUNT(*) FROM sekolah")->fetchColumn();
    $total_peserta = $pdo->query("SELECT COUNT(*) FROM peserta")->fetchColumn();
    $pembayaran_lunas = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran = 'Lunas'")->fetchColumn();
    $dokumen_diterima = $pdo->query("SELECT COUNT(*) FROM dokumen_berka WHERE status_dokumen = 'Diterima'")->fetchColumn();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Ambil ringkasan soal (skema lama)
$soalPenyisihan = [];
$soalFinal = [];
try {
    if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'")->rowCount()) {
        $soalPenyisihan = $pdo->query("SELECT no_soal, soal1, soal2, soal3 FROM soal_musabaqoh ORDER BY no_soal LIMIT 10")->fetchAll();
    }
} catch (PDOException $e) {}

try {
    if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'")->rowCount()) {
        $soalFinal = $pdo->query("SELECT no_soal, soal1, soal2, soal3 FROM soal_musabaqoh_final ORDER BY no_soal LIMIT 10")->fetchAll();
    }
} catch (PDOException $e) {}

// Ambil ringkasan soal (skema baru)
$soalBaru = [];
try {
    if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_new'")->rowCount()) {
        $soalBaru = $pdo->query("SELECT id, no_soal, judul_soal, jenis_soal, status FROM soal_musabaqoh_new ORDER BY jenis_soal, no_soal LIMIT 10")->fetchAll();
    }
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-card .card-body { padding: 2rem; }
        .stat-number { font-size: 2.5rem; font-weight: bold; }
        
        /* Link card styles */
        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s ease;
        }
        
        .card-link:hover {
            text-decoration: none;
            color: inherit;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-link:hover .stat-card {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .card-link .card {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</h2>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <a href="sekolah.php" class="card-link">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-school fa-2x mb-2"></i>
                                        <div class="stat-number"><?php echo $total_sekolah; ?></div>
                                        <div>Total Sekolah</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="peserta.php" class="card-link">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <div class="stat-number"><?php echo $total_peserta; ?></div>
                                        <div>Total Peserta</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="pembayaran.php" class="card-link">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                                        <div class="stat-number"><?php echo $pembayaran_lunas; ?></div>
                                        <div>Pembayaran Lunas</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="dokumen.php" class="card-link">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                                        <div class="stat-number"><?php echo $dokumen_diterima; ?></div>
                                        <div>Dokumen Diterima</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="card mb-4" id="soal">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-book-quran me-2"></i>
                            <strong>Ringkasan Soal (Skema Lama)</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Penyisihan</h6>
                                    <?php if (empty($soalPenyisihan)): ?>
                                        <p class="text-muted">Tidak ada data.</p>
                                    <?php else: ?>
                                        <ul class="list-group">
                                            <?php foreach ($soalPenyisihan as $s): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    No. <?php echo sanitizeOutput($s['no_soal']); ?>
                                                    <span class="badge bg-primary rounded-pill">3 butir</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Final</h6>
                                    <?php if (empty($soalFinal)): ?>
                                        <p class="text-muted">Tidak ada data.</p>
                                    <?php else: ?>
                                        <ul class="list-group">
                                            <?php foreach ($soalFinal as $s): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    No. <?php echo sanitizeOutput($s['no_soal']); ?>
                                                    <span class="badge bg-danger rounded-pill">3 butir</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4" id="soal-baru">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-book-open me-2"></i>
                            <strong>Ringkasan Soal (Skema Baru)</strong>
                        </div>
                        <div class="card-body">
                            <?php if (empty($soalBaru)): ?>
                                <p class="text-muted">Tidak ada data.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px;">No</th>
                                                <th>Judul</th>
                                                <th style="width: 130px;">Jenis</th>
                                                <th style="width: 120px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($soalBaru as $s): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($s['no_soal']); ?></td>
                                                    <td><?php echo htmlspecialchars($s['judul_soal']); ?></td>
                                                    <td><?php echo htmlspecialchars($s['jenis_soal']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $s['status'] == 'Aktif' ? 'success' : ($s['status'] == 'Draft' ? 'warning' : 'secondary'); ?>">
                                                            <?php echo htmlspecialchars($s['status']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
