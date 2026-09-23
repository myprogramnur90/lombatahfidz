<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai juri
if (!isset($_SESSION['juri_id'])) {
    header('Location: ../auth/login_juri.php');
    exit();
}

$juri_id = $_SESSION['juri_id'];
$juri_nama = $_SESSION['juri_nama'];

// Ambil data juri
$query_juri = "SELECT * FROM juri WHERE id = ?";
$stmt_juri = $pdo->prepare($query_juri);
$stmt_juri->execute([$juri_id]);
$juri = $stmt_juri->fetch();

// Keyword pencarian
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$like = "%" . $q . "%";

// Hitung statistik penilaian untuk semua peserta diterima
$query_stats = "
    SELECT 
        (SELECT COUNT(*) FROM peserta WHERE status = 'Diterima') as total_peserta,
        (SELECT COUNT(*) FROM penilaian WHERE juri_id = ?) as sudah_dinilai,
        ((SELECT COUNT(*) FROM peserta WHERE status = 'Diterima') - (SELECT COUNT(*) FROM penilaian WHERE juri_id = ?)) as belum_dinilai
";
$stmt_stats = $pdo->prepare($query_stats);
$stmt_stats->execute([$juri_id, $juri_id]);
$stats = $stmt_stats->fetch();

// Hitung statistik penilaian final
$query_stats_final = "
    SELECT 
        (SELECT COUNT(*) FROM peserta_final WHERE status = 'Aktif') as total_peserta_final,
        (SELECT COUNT(*) FROM penilaian_final WHERE juri_id = ?) as sudah_dinilai_final,
        ((SELECT COUNT(*) FROM peserta_final WHERE status = 'Aktif') - (SELECT COUNT(*) FROM penilaian_final WHERE juri_id = ?)) as belum_dinilai_final
";
$stmt_stats_final = $pdo->prepare($query_stats_final);
$stmt_stats_final->execute([$juri_id, $juri_id]);
$stats_final = $stmt_stats_final->fetch();

// Ambil daftar peserta belum dinilai oleh juri ini
$query_belum = "
    SELECT p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE p.status = 'Diterima' AND p.id NOT IN (
        SELECT peserta_id FROM penilaian WHERE juri_id = ?
    ) " . ($q !== '' ? "AND (p.nama_lengkap LIKE ? OR p.nisn LIKE ? OR s.nama_sekolah LIKE ?)" : "") . "
    ORDER BY p.nama_lengkap
    LIMIT 10
";
$stmt_belum = $pdo->prepare($query_belum);
if ($q !== '') {
    $stmt_belum->execute([$juri_id, $like, $like, $like]);
} else {
    $stmt_belum->execute([$juri_id]);
}
$peserta_belum_dinilai = $stmt_belum->fetchAll();

// Ambil daftar peserta sudah dinilai oleh juri ini
$query_sudah = "
    SELECT p.id, p.nama_lengkap, p.nisn, s.nama_sekolah, pen.skor_spesialisasi as skor_keseluruhan, pen.tanggal_penilaian
    FROM penilaian pen
    JOIN peserta p ON pen.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE pen.juri_id = ? " . ($q !== '' ? "AND (p.nama_lengkap LIKE ? OR p.nisn LIKE ? OR s.nama_sekolah LIKE ?)" : "") . "
    ORDER BY pen.tanggal_penilaian DESC
    LIMIT 10
";
$stmt_sudah = $pdo->prepare($query_sudah);
if ($q !== '') {
    $stmt_sudah->execute([$juri_id, $like, $like, $like]);
} else {
    $stmt_sudah->execute([$juri_id]);
}
$peserta_sudah_dinilai = $stmt_sudah->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Juri - Lomba Tahfidz</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
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
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gavel me-2"></i>
                Dashboard Juri
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        <?php echo htmlspecialchars($juri_nama); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profil.php"><i class="fas fa-user-edit me-2"></i>Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Live Peserta Aktif Banner -->
        <div id="liveBannerContainer" style="display: none;">
            <div class="alert mb-4 shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997); border: none; border-radius: 15px; color: white;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="badge bg-danger rounded-pill pulse-animation" style="font-size: 1.1em; padding: 10px 20px; box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);">
                                <i class="fas fa-broadcast-tower me-2"></i> LIVE SEKARANG
                            </span>
                        </div>
                        <div>
                            <h4 class="mb-1" style="font-weight: 700; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                                <i class="fas fa-user-graduate me-2"></i><span id="liveNama"></span>
                            </h4>
                            <div class="d-flex align-items-center" style="font-size: 1.1em; opacity: 0.9;">
                                <i class="fas fa-school me-2"></i><span id="liveSekolah" class="me-3"></span>
                                <span id="liveSoalBadge" class="badge bg-light text-success ms-2" style="font-size: 0.9em; display: none;"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a id="liveBtnNilai" href="#" class="btn btn-light btn-lg fw-bold" style="color: #28a745; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <i class="fas fa-edit me-2"></i>Nilai Sekarang
                        </a>
                    </div>
                </div>
                
                <!-- Soal Preview (opsional, jika ada soal) -->
                <div id="liveSoalPreview" class="mt-3 pt-3 border-top border-light" style="display: none; border-opacity: 0.3;">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <small class="text-uppercase fw-bold opacity-75">Soal 1</small>
                            <div id="liveSoal1" style="font-size: 1.2em; font-weight: 500;"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-uppercase fw-bold opacity-75">Soal 2</small>
                            <div id="liveSoal2" style="font-size: 1.2em; font-weight: 500;"></div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="text-uppercase fw-bold opacity-75">Soal 3</small>
                            <div id="liveSoal3" style="font-size: 1.2em; font-weight: 500;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <style>
                @keyframes pulse-animation {
                    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
                    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
                }
                .pulse-animation {
                    animation: pulse-animation 2s infinite;
                }
            </style>
        </div>
        
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-0">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard Juri
                        </h2>
                        <p class="text-muted mb-0">Selamat datang, <?php echo htmlspecialchars($juri_nama); ?>!</p>
                        <div class="mt-2">
                            <span class="badge bg-primary fs-6">Spesialisasi: <?php echo htmlspecialchars(
                                $juri['spesialisasi'] == 'Kelancaran Hafalan' ? 'Kelancaran (Tahfidz)' : (
                                $juri['spesialisasi'] == 'Makhraj' ? 'Makhraj Tajwid' : (
                                $juri['spesialisasi'] == 'Tajwid dan Adab' ? 'Shifatul Huruf' : $juri['spesialisasi']
                                ))
                            ); ?></span>
                            <form class="mt-3" method="GET" action="dashboard.php">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Cari nama, NISN, atau sekolah..." name="q" value="<?php echo htmlspecialchars($q); ?>">
                                    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                                    <?php if ($q !== ''): ?>
                                        <a class="btn btn-outline-light" href="dashboard.php"><i class="fas fa-times"></i></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                            <div class="mt-2">
                                <a href="laporan_penilaian.php" class="btn btn-success">
                                    <i class="fas fa-chart-bar me-2"></i>Lihat Rekap Penilaian
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
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h3><?php echo $stats['total_peserta']; ?></h3>
                        <p class="mb-0">Total Peserta</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3><?php echo $stats['sudah_dinilai']; ?></h3>
                        <p class="mb-0">Sudah Dinilai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h3><?php echo $stats['belum_dinilai']; ?></h3>
                        <p class="mb-0">Belum Dinilai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card info">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h3><?php echo htmlspecialchars($juri['spesialisasi']); ?></h3>
                        <p class="mb-0">Spesialisasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Final -->
        <?php if ($stats_final['total_peserta_final'] > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Statistik Penilaian Final
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-trophy fa-2x mb-2 text-warning"></i>
                                        <h3><?php echo $stats_final['total_peserta_final']; ?></h3>
                                        <p class="mb-0">Peserta Final</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <h3><?php echo $stats_final['sudah_dinilai_final']; ?></h3>
                                        <p class="mb-0">Sudah Dinilai</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card warning">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h3><?php echo $stats_final['belum_dinilai_final']; ?></h3>
                                        <p class="mb-0">Belum Dinilai</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card info">
                                    <div class="card-body text-center">
                                        <a href="penilaian_final.php" class="btn btn-primary">
                                            <i class="fas fa-edit me-2"></i>
                                            Penilaian Final
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Peserta Belum Dinilai -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Peserta Belum Dinilai
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($peserta_belum_dinilai) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Sekolah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($peserta_belum_dinilai as $peserta): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($peserta['nisn']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                                <td>
                                                    <a href="penilaian.php?id=<?php echo $peserta['id']; ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-edit me-1"></i>Nilai
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="daftar_peserta.php" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-2"></i>Lihat Semua Peserta
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>Semua peserta sudah dinilai!</h5>
                                <p class="text-muted">Tidak ada peserta yang belum dinilai.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Peserta Sudah Dinilai -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Peserta Sudah Dinilai
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($peserta_sudah_dinilai) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Sekolah</th>
                                            <th>Skor</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($peserta_sudah_dinilai as $peserta): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($peserta['nisn']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo number_format($peserta['skor_keseluruhan'], 2); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?php echo date('d/m/Y H:i', strtotime($peserta['tanggal_penilaian'])); ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="riwayat_penilaian.php" class="btn btn-outline-success">
                                    <i class="fas fa-history me-2"></i>Lihat Riwayat Lengkap
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <h5>Belum ada penilaian</h5>
                                <p class="text-muted">Mulai menilai peserta yang tersedia.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPesertaId = null;
        let currentJenis = 'penyisihan'; // Default, bisa diubah jika juri ada di final

        function cekPesertaAktif() {
            // Cek penyisihan
            fetch('../musabaqoh/api_aktif.php?action=get&jenis=penyisihan')
                .then(r => r.json())
                .then(dataPenyisihan => {
                    if (dataPenyisihan.status === 'ok' && dataPenyisihan.data) {
                        tampilkanBanner(dataPenyisihan.data, 'penyisihan');
                    } else {
                        // Jika tidak ada penyisihan, cek final
                        fetch('../musabaqoh/api_aktif.php?action=get&jenis=final')
                            .then(r => r.json())
                            .then(dataFinal => {
                                if (dataFinal.status === 'ok' && dataFinal.data) {
                                    tampilkanBanner(dataFinal.data, 'final');
                                } else {
                                    sembunyikanBanner();
                                }
                            }).catch(() => sembunyikanBanner());
                    }
                }).catch(() => sembunyikanBanner());
        }

        function tampilkanBanner(data, jenis) {
            const container = document.getElementById('liveBannerContainer');
            
            document.getElementById('liveNama').textContent = data.nama_lengkap;
            document.getElementById('liveSekolah').textContent = data.nama_sekolah;
            
            const btnNilai = document.getElementById('liveBtnNilai');
            if (jenis === 'final') {
                btnNilai.href = 'penilaian_final.php?id=' + data.peserta_id;
            } else {
                btnNilai.href = 'penilaian.php?id=' + data.peserta_id;
            }
            
            const badgeSoal = document.getElementById('liveSoalBadge');
            const previewSoal = document.getElementById('liveSoalPreview');
            
            if (data.no_soal) {
                badgeSoal.textContent = 'Soal ' + (jenis==='final'?'Final':'Penyisihan') + ' No. ' + data.no_soal;
                badgeSoal.style.display = 'inline-block';
                
                if (data.soal1) {
                    document.getElementById('liveSoal1').textContent = data.soal1;
                    document.getElementById('liveSoal2').textContent = data.soal2 || '-';
                    document.getElementById('liveSoal3').textContent = data.soal3 || '-';
                    previewSoal.style.display = 'block';
                } else {
                    previewSoal.style.display = 'none';
                }
            } else {
                badgeSoal.style.display = 'none';
                previewSoal.style.display = 'none';
            }
            
            if (container.style.display === 'none') {
                // Tambahkan efek fade in jika baru muncul
                container.style.opacity = 0;
                container.style.display = 'block';
                setTimeout(() => {
                    container.style.transition = 'opacity 0.5s';
                    container.style.opacity = 1;
                }, 50);
            }
        }

        function sembunyikanBanner() {
            const container = document.getElementById('liveBannerContainer');
            if (container.style.display !== 'none') {
                container.style.transition = 'opacity 0.5s';
                container.style.opacity = 0;
                setTimeout(() => {
                    container.style.display = 'none';
                }, 500);
            }
        }

        // Cek setiap 3 detik
        setInterval(cekPesertaAktif, 3000);
        
        // Cek pertama kali saat halaman dimuat
        document.addEventListener('DOMContentLoaded', cekPesertaAktif);
    </script>
</body>
</html>
