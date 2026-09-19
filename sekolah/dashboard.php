<?php
session_start();
require_once '../config/database.php';

$sekolah_id = $_SESSION['user_id'];
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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
    <span class="text-muted">Selamat datang, <?php echo $_SESSION['nama_sekolah']; ?></span>
</div>
                    
                    <!-- Statistik Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo $total_peserta; ?></div>
                                    <div>Total Peserta</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card fa-2x mb-2 text-primary"></i>
                                    <div class="stat-number text-primary">
                                        <?php echo $status_pembayaran ? ucfirst($status_pembayaran['status_pembayaran']) : 'Belum'; ?>
                                    </div>
                                    <div>Status Pembayaran</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-2x mb-2 text-success"></i>
                                    <div class="stat-number text-success">
                                        <?php echo $status_berka ? ucfirst($status_berka['status_dokumen']) : 'Belum'; ?>
                                    </div>
                                    <div>Status Berka</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar fa-2x mb-2 text-warning"></i>
                                    <div class="stat-number text-warning">
                                        <?php echo date('d/m/Y', strtotime(getPengaturan('tanggal_penutupan'))); ?>
                                    </div>
                                    <div>Batas Pendaftaran</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Row kedua untuk statistik tambahan -->
                    <div class="row mb-4">
                    </div>
                    
                    <!-- Status Peserta dan Informasi Lomba -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-pie me-2"></i>Status Peserta</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($status_peserta)): ?>
                                        <?php foreach ($status_peserta as $status): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><?php echo ucfirst($status['status']); ?></span>
                                                <span class="badge bg-primary"><?php echo $status['jumlah']; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada peserta terdaftar</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle me-2"></i>Informasi Lomba</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nama Lomba:</strong><br><?php echo getPengaturan('nama_lomba'); ?></p>
                                    <p><strong>Biaya Pendaftaran:</strong><br>Rp <?php echo number_format(getPengaturan('biaya_pendaftaran')); ?></p>
                                    <p><strong>Kontak Panitia:</strong><br><?php echo getPengaturan('kontak_panitia'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Posting Terbaru -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5><i class="fas fa-newspaper me-2"></i>Posting & Pengumuman Terbaru</h5>
                                    <a href="kelola_posting.php" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>Lihat Semua
                                    </a>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($posts)): ?>
                                        <div class="row">
                                            <?php foreach ($posts as $post): ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-left-primary">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="card-title text-primary mb-0"><?php echo htmlspecialchars($post['judul']); ?></h6>
                                                                <div class="d-flex flex-column align-items-end">
                                                                    <span class="badge bg-<?php echo $post['jenis_post'] == 'Persyaratan' ? 'warning' : ($post['jenis_post'] == 'Pengumuman' ? 'danger' : 'info'); ?> mb-1">
                                                                        <?php echo $post['jenis_post']; ?>
                                                                    </span>
                                                                    <span class="badge bg-<?php echo $post['target_audience'] == 'Sekolah' ? 'primary' : ($post['target_audience'] == 'Juri' ? 'warning' : 'success'); ?> small">
                                                                        <?php echo $post['target_audience']; ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <p class="card-text text-muted small">
                                                                <?php echo substr(strip_tags($post['konten']), 0, 100) . '...'; ?>
                                                            </p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                                                                </small>
                                                                <a href="detail_posting.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye me-1"></i>Baca
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada posting yang dipublikasikan</p>
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
