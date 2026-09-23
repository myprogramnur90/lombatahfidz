<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

requireSekolahLogin();
$sekolah_id = $_SESSION['sekolah_id'];
$page_title = 'Dashboard';

// Ambil data statistik
try {
    // Hitung jumlah peserta
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM peserta WHERE sekolah_id = ?");
    $stmt->execute([$sekolah_id]);
    $total_peserta = $stmt->fetch()['total'];
    
    // Hitung peserta berdasarkan status
    $stmt = $pdo->prepare("SELECT status, COUNT(*) as jumlah FROM peserta WHERE sekolah_id = ? GROUP BY status");
    $stmt->execute([$sekolah_id]);
    $status_peserta = $stmt->fetchAll();
    
    // Cek status pembayaran
    $stmt = $pdo->prepare("SELECT status_pembayaran FROM pembayaran WHERE sekolah_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$sekolah_id]);
    $status_pembayaran = $stmt->fetch();
    
    // Cek status dokumen berka
    $stmt = $pdo->prepare("SELECT status_dokumen FROM dokumen_berka WHERE sekolah_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$sekolah_id]);
    $status_berka = $stmt->fetch();
    
    // Ambil posting admin terbaru (untuk sekolah ini atau umum)
    $stmt = $pdo->prepare("SELECT p.*, s.nama_sekolah 
                          FROM posts p 
                          LEFT JOIN sekolah s ON p.sekolah_id = s.id 
                          WHERE p.status = 'Published' 
                          AND (p.target_audience = 'Umum' OR p.target_audience = 'Sekolah' OR p.sekolah_id = ?)
                          ORDER BY p.created_at DESC LIMIT 5");
    $stmt->execute([$sekolah_id]);
    $posts = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Buat konten dashboard
ob_start();
?>
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dashboard-header h2 { margin: 0; font-weight: 700; }
    .stat-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .stat-card .card-body {
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }
    .stat-info { text-align: right; }
    .stat-number { font-size: 1.8rem; font-weight: 800; line-height: 1.2; margin-bottom: 0.2rem; }
    .stat-label { font-size: 0.9rem; color: #6c757d; font-weight: 600; text-transform: uppercase; }
    
    .bg-light-primary { background: rgba(102, 126, 234, 0.1); color: #667eea; }
    .bg-light-success { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
    .bg-light-warning { background: rgba(241, 196, 15, 0.1); color: #f1c40f; }
    .bg-light-info { background: rgba(52, 152, 219, 0.1); color: #3498db; }
    
    .custom-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .custom-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.2rem 1.5rem;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .post-card {
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s;
    }
    .post-card:hover { border-color: #667eea; box-shadow: 0 5px 15px rgba(102,126,234,0.1); }
</style>

<div class="dashboard-header flex-column flex-md-row text-center text-md-start">
    <div>
        <h2><i class="fas fa-school me-2"></i>Dashboard Sekolah</h2>
        <p class="mb-0 mt-1 opacity-75">Panel pendaftaran dan informasi musabaqoh</p>
    </div>
    <div class="mt-3 mt-md-0">
        <span class="badge bg-white text-dark py-2 px-3 rounded-pill shadow-sm">
            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['nama_sekolah'] ?? 'Panel Sekolah'); ?>
        </span>
    </div>
</div>
                    
<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-light-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number text-primary"><?php echo $total_peserta; ?></div>
                    <div class="stat-label">Total Peserta</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-light-info">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number text-info fs-4"><?php echo $status_pembayaran ? ucfirst(htmlspecialchars($status_pembayaran['status_pembayaran'])) : 'Belum'; ?></div>
                    <div class="stat-label">Pembayaran</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-light-success">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number text-success fs-4"><?php echo $status_berka ? ucfirst(htmlspecialchars($status_berka['status_dokumen'])) : 'Belum'; ?></div>
                    <div class="stat-label">Status Berkas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="stat-icon bg-light-warning">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-number text-warning fs-5"><?php echo date('d/m/Y', strtotime(getPengaturan('tanggal_penutupan'))); ?></div>
                    <div class="stat-label">Batas Daftar</div>
                </div>
            </div>
        </div>
    </div>
</div>
                    
<!-- Status Peserta dan Informasi Lomba -->
<div class="row">
    <div class="col-md-6">
        <div class="card custom-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-pie me-2 text-primary"></i>Status Peserta</span>
                <a href="peserta.php" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Data</a>
            </div>
            <div class="card-body">
                <?php if (!empty($status_peserta)): ?>
                    <div class="list-group list-group-flush">
                    <?php foreach ($status_peserta as $status): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                                <?php echo ucfirst(htmlspecialchars($status['status'])); ?>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3"><?php echo $status['jumlah']; ?></span>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <div class="mb-3"><i class="fas fa-user-times fa-3x text-muted opacity-50"></i></div>
                        <p class="text-muted mb-0">Belum ada peserta terdaftar</p>
                        <a href="daftar.php" class="btn btn-primary btn-sm mt-3 rounded-pill px-4"><i class="fas fa-plus me-1"></i> Daftar Sekarang</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-4 mt-md-0">
        <div class="card custom-card h-100">
            <div class="card-header">
                <i class="fas fa-info-circle me-2 text-info"></i>Informasi Lomba
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 py-3">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Nama Lomba</small>
                        <div class="fw-bold text-dark fs-5"><?php echo htmlspecialchars(getPengaturan('nama_lomba')); ?></div>
                    </li>
                    <li class="list-group-item px-0 py-3">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Biaya Pendaftaran</small>
                        <div class="fw-bold text-success fs-5">Rp <?php echo number_format(getPengaturan('biaya_pendaftaran'), 0, ',', '.'); ?></div>
                    </li>
                    <li class="list-group-item px-0 py-3 border-0">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1">Kontak Panitia</small>
                        <div class="d-flex align-items-center">
                            <i class="fab fa-whatsapp text-success fs-4 me-2"></i>
                            <span class="fw-bold text-dark"><?php echo htmlspecialchars(getPengaturan('kontak_panitia')); ?></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
                    
<!-- Posting Terbaru -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-newspaper me-2 text-primary"></i>Pengumuman & Informasi Terbaru</span>
                <a href="kelola_posting.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body bg-light">
                <?php if (!empty($posts)): ?>
                    <div class="row g-3">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6 mb-2">
                                <div class="card post-card h-100 bg-white">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 flex-grow-1 pe-2" style="color: #2c3e50;">
                                                <?php echo htmlspecialchars($post['judul']); ?>
                                            </h6>
                                            <span class="badge bg-<?php echo $post['jenis_post'] == 'Persyaratan' ? 'warning text-dark' : ($post['jenis_post'] == 'Pengumuman' ? 'danger' : 'info'); ?> rounded-pill">
                                                <?php echo htmlspecialchars($post['jenis_post']); ?>
                                            </span>
                                        </div>
                                        <p class="card-text text-muted small flex-grow-1 mb-3">
                                            <?php echo htmlspecialchars(substr(strip_tags(html_entity_decode($post['konten'])), 0, 100)) . '...'; ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                            <small class="text-muted" style="font-size: 0.8rem;">
                                                <i class="far fa-clock me-1"></i> <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?>
                                            </small>
                                            <a href="detail_posting.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3" style="font-size: 0.8rem;">
                                                Baca
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted opacity-25 mb-3"></i>
                        <p class="text-muted fw-bold">Belum ada pengumuman</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
