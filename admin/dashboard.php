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
    $pembayaran_pending = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran != 'Lunas'")->fetchColumn();
    $dokumen_diterima = $pdo->query("SELECT COUNT(*) FROM dokumen_berka WHERE status_dokumen = 'Diterima'")->fetchColumn();
    $dokumen_pending = $pdo->query("SELECT COUNT(*) FROM dokumen_berka WHERE status_dokumen = 'Menunggu'")->fetchColumn();
} catch (PDOException $e) {
    $total_sekolah = 0;
    $total_peserta = 0;
    $pembayaran_lunas = 0;
    $pembayaran_pending = 0;
    $dokumen_diterima = 0;
    $dokumen_pending = 0;
}

// Ambil data juri
$total_juri = 0;
try {
    if ($pdo->query("SHOW TABLES LIKE 'juri'")->rowCount()) {
        $total_juri = $pdo->query("SELECT COUNT(*) FROM juri")->fetchColumn();
    }
} catch (PDOException $e) {}

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

// Ambil 5 sekolah terbaru
$sekolahTerbaru = [];
try {
    $sekolahTerbaru = $pdo->query("SELECT nama_sekolah, created_at FROM sekolah ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {}

// Ambil 5 peserta terbaru
$pesertaTerbaru = [];
try {
    $pesertaTerbaru = $pdo->query("SELECT p.nama_peserta, s.nama_sekolah, p.created_at FROM peserta p LEFT JOIN sekolah s ON p.sekolah_id = s.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {}

$namaAdmin = $_SESSION['admin_nama'] ?? $_SESSION['admin_username'] ?? 'Admin';
$namaLomba = getPengaturan('nama_lomba');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo $namaLomba; ?></title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f0f2f5; }
        
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            border-radius: 20px;
            padding: 2rem 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 10%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .welcome-banner h2 { font-weight: 800; font-size: 1.8rem; margin-bottom: 0.3rem; position: relative; z-index: 1; }
        .welcome-banner p { opacity: 0.9; font-size: 0.95rem; position: relative; z-index: 1; margin-bottom: 0; }
        .welcome-banner .welcome-date { 
            background: rgba(255,255,255,0.2); 
            border-radius: 12px; 
            padding: 8px 16px; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px;
            font-size: 0.85rem;
            backdrop-filter: blur(10px);
            position: relative; z-index: 1;
        }
        
        /* Stat Cards */
        .stat-card {
            border: none;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            color: inherit;
            text-decoration: none;
        }
        .stat-card .card-body {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.3rem;
        }
        .stat-card .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }
        .stat-card .stat-badge {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* Warna card */
        .stat-card.card-sekolah { background: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1); }
        .stat-card.card-sekolah .stat-icon { background: rgba(102, 126, 234, 0.12); color: #667eea; }
        .stat-card.card-sekolah .stat-number { color: #667eea; }
        
        .stat-card.card-peserta { background: white; box-shadow: 0 4px 15px rgba(17, 153, 142, 0.1); }
        .stat-card.card-peserta .stat-icon { background: rgba(17, 153, 142, 0.12); color: #11998e; }
        .stat-card.card-peserta .stat-number { color: #11998e; }
        
        .stat-card.card-bayar { background: white; box-shadow: 0 4px 15px rgba(246, 185, 59, 0.1); }
        .stat-card.card-bayar .stat-icon { background: rgba(246, 185, 59, 0.12); color: #f6b93b; }
        .stat-card.card-bayar .stat-number { color: #f6b93b; }
        
        .stat-card.card-dokumen { background: white; box-shadow: 0 4px 15px rgba(238, 82, 83, 0.1); }
        .stat-card.card-dokumen .stat-icon { background: rgba(238, 82, 83, 0.12); color: #ee5253; }
        .stat-card.card-dokumen .stat-number { color: #ee5253; }
        
        .stat-card.card-juri { background: white; box-shadow: 0 4px 15px rgba(120, 111, 166, 0.1); }
        .stat-card.card-juri .stat-icon { background: rgba(120, 111, 166, 0.12); color: #786fa6; }
        .stat-card.card-juri .stat-number { color: #786fa6; }
        
        /* Quick Action */
        .quick-action {
            border: none;
            border-radius: 14px;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 1rem 1.2rem;
        }
        .quick-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            color: inherit;
            text-decoration: none;
        }
        .quick-action .qa-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .quick-action .qa-text { font-weight: 600; font-size: 0.85rem; }
        
        /* Content Cards */
        .content-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            background: white;
        }
        .content-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f2f5;
            padding: 1.2rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
        }
        .content-card .card-body { padding: 1.2rem 1.5rem; }
        
        /* Activity Timeline */
        .activity-item {
            display: flex;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
            align-items: flex-start;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .activity-content { flex: 1; }
        .activity-content .name { font-weight: 600; font-size: 0.9rem; color: #2d3436; }
        .activity-content .detail { font-size: 0.8rem; color: #636e72; }
        .activity-content .time { font-size: 0.75rem; color: #b2bec3; }
        
        /* Soal Badge */
        .soal-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 600;
        }
        
        /* Page title */
        .page-title { font-weight: 800; font-size: 1.5rem; color: #2d3436; }

        /* Responsive */
        @media (max-width: 768px) {
            .welcome-banner { padding: 1.5rem; }
            .welcome-banner h2 { font-size: 1.4rem; }
            .stat-card .stat-number { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    
                    <!-- Welcome Banner -->
                    <div class="welcome-banner mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="mb-1" style="font-size: 0.9rem; opacity: 0.85;">👋 Selamat datang kembali,</p>
                                <h2><?php echo htmlspecialchars($namaAdmin); ?></h2>
                                <p><?php echo htmlspecialchars($namaLomba); ?></p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="welcome-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo strftime('%A, %d %B %Y', strtotime(date('Y-m-d'))); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-lg-3">
                            <a href="sekolah.php" class="stat-card card-sekolah">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-school"></i>
                                    </div>
                                    <div class="stat-number"><?php echo $total_sekolah; ?></div>
                                    <div class="stat-label">Total Sekolah</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-lg-3">
                            <a href="peserta.php" class="stat-card card-peserta">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-number"><?php echo $total_peserta; ?></div>
                                    <div class="stat-label">Total Peserta</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-lg-3">
                            <a href="pembayaran.php" class="stat-card card-bayar">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="stat-number"><?php echo $pembayaran_lunas; ?></div>
                                    <div class="stat-label">Pembayaran Lunas</div>
                                    <?php if ($pembayaran_pending > 0): ?>
                                        <span class="stat-badge bg-warning bg-opacity-10 text-warning mt-2 d-inline-block">
                                            <i class="fas fa-clock me-1"></i><?php echo $pembayaran_pending; ?> pending
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-lg-3">
                            <a href="dokumen.php" class="stat-card card-dokumen">
                                <div class="card-body">
                                    <div class="stat-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="stat-number"><?php echo $dokumen_diterima; ?></div>
                                    <div class="stat-label">Dokumen Diterima</div>
                                    <?php if ($dokumen_pending > 0): ?>
                                        <span class="stat-badge bg-info bg-opacity-10 text-info mt-2 d-inline-block">
                                            <i class="fas fa-clock me-1"></i><?php echo $dokumen_pending; ?> menunggu
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">
                                <i class="fas fa-bolt me-1"></i> Aksi Cepat
                            </h6>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="sekolah.php" class="quick-action">
                                <div class="qa-icon" style="background: rgba(102,126,234,0.1); color: #667eea;">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="qa-text">Tambah Sekolah</div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="juri.php" class="quick-action">
                                <div class="qa-icon" style="background: rgba(120,111,166,0.1); color: #786fa6;">
                                    <i class="fas fa-gavel"></i>
                                </div>
                                <div class="qa-text">Kelola Juri</div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="soal.php" class="quick-action">
                                <div class="qa-icon" style="background: rgba(17,153,142,0.1); color: #11998e;">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="qa-text">Kelola Soal</div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="laporan_penilaian.php" class="quick-action">
                                <div class="qa-icon" style="background: rgba(238,82,83,0.1); color: #ee5253;">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="qa-text">Laporan Nilai</div>
                            </a>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row g-4 mb-4">
                        
                        <!-- Peserta Terbaru -->
                        <div class="col-lg-6">
                            <div class="content-card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-user-plus me-2 text-primary"></i>Peserta Terbaru</span>
                                    <a href="peserta.php" class="text-decoration-none" style="font-size: 0.82rem;">
                                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($pesertaTerbaru)): ?>
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            <small>Belum ada peserta terdaftar</small>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($pesertaTerbaru as $pt): ?>
                                            <div class="activity-item">
                                                <div class="activity-dot" style="background: rgba(17,153,142,0.1); color: #11998e;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="name"><?php echo htmlspecialchars($pt['nama_peserta']); ?></div>
                                                    <div class="detail"><?php echo htmlspecialchars($pt['nama_sekolah'] ?? '-'); ?></div>
                                                    <div class="time">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo date('d M Y, H:i', strtotime($pt['created_at'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sekolah Terbaru -->
                        <div class="col-lg-6">
                            <div class="content-card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-school me-2" style="color: #667eea;"></i>Sekolah Terbaru</span>
                                    <a href="sekolah.php" class="text-decoration-none" style="font-size: 0.82rem;">
                                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($sekolahTerbaru)): ?>
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            <small>Belum ada sekolah terdaftar</small>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($sekolahTerbaru as $st): ?>
                                            <div class="activity-item">
                                                <div class="activity-dot" style="background: rgba(102,126,234,0.1); color: #667eea;">
                                                    <i class="fas fa-school"></i>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="name"><?php echo htmlspecialchars($st['nama_sekolah']); ?></div>
                                                    <div class="time">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo date('d M Y, H:i', strtotime($st['created_at'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ringkasan Soal -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="content-card h-100" id="soal">
                                <div class="card-header">
                                    <i class="fas fa-book-quran me-2" style="color: #764ba2;"></i>Soal Penyisihan & Final
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h6 class="fw-bold mb-3" style="font-size: 0.85rem;">
                                                <span class="soal-badge" style="background: rgba(102,126,234,0.1); color: #667eea;">
                                                    <i class="fas fa-layer-group"></i> Penyisihan
                                                </span>
                                            </h6>
                                            <?php if (empty($soalPenyisihan)): ?>
                                                <p class="text-muted" style="font-size: 0.85rem;">Tidak ada data.</p>
                                            <?php else: ?>
                                                <ul class="list-group list-group-flush">
                                                    <?php foreach ($soalPenyisihan as $s): ?>
                                                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0" style="font-size: 0.85rem;">
                                                            Soal No. <?php echo sanitizeOutput($s['no_soal']); ?>
                                                            <span class="badge rounded-pill" style="background: rgba(102,126,234,0.15); color: #667eea;">3 butir</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="fw-bold mb-3" style="font-size: 0.85rem;">
                                                <span class="soal-badge" style="background: rgba(238,82,83,0.1); color: #ee5253;">
                                                    <i class="fas fa-trophy"></i> Final
                                                </span>
                                            </h6>
                                            <?php if (empty($soalFinal)): ?>
                                                <p class="text-muted" style="font-size: 0.85rem;">Tidak ada data.</p>
                                            <?php else: ?>
                                                <ul class="list-group list-group-flush">
                                                    <?php foreach ($soalFinal as $s): ?>
                                                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0" style="font-size: 0.85rem;">
                                                            Soal No. <?php echo sanitizeOutput($s['no_soal']); ?>
                                                            <span class="badge rounded-pill" style="background: rgba(238,82,83,0.15); color: #ee5253;">3 butir</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="content-card h-100" id="soal-baru">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-book-open me-2" style="color: #11998e;"></i>Soal Skema Baru</span>
                                    <a href="soal.php" class="text-decoration-none" style="font-size: 0.82rem;">
                                        Kelola <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($soalBaru)): ?>
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-book-open fa-2x mb-2 d-block" style="opacity: 0.3;"></i>
                                            <small>Tidak ada data soal skema baru.</small>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" style="font-size: 0.85rem;">
                                                <thead>
                                                    <tr style="border-bottom: 2px solid #f0f2f5;">
                                                        <th class="fw-bold text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">No</th>
                                                        <th class="fw-bold text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Judul</th>
                                                        <th class="fw-bold text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Jenis</th>
                                                        <th class="fw-bold text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($soalBaru as $s): ?>
                                                        <tr>
                                                            <td class="fw-semibold"><?php echo htmlspecialchars($s['no_soal']); ?></td>
                                                            <td><?php echo htmlspecialchars($s['judul_soal']); ?></td>
                                                            <td>
                                                                <span class="badge rounded-pill" style="background: rgba(102,126,234,0.1); color: #667eea; font-weight: 600;">
                                                                    <?php echo htmlspecialchars($s['jenis_soal']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                $statusColor = $s['status'] == 'Aktif' ? '#00b894' : ($s['status'] == 'Draft' ? '#fdcb6e' : '#b2bec3');
                                                                $statusBg = $s['status'] == 'Aktif' ? 'rgba(0,184,148,0.1)' : ($s['status'] == 'Draft' ? 'rgba(253,203,110,0.15)' : 'rgba(178,190,195,0.15)');
                                                                ?>
                                                                <span class="badge rounded-pill" style="background: <?php echo $statusBg; ?>; color: <?php echo $statusColor; ?>; font-weight: 600;">
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

