<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login_sekolah.php');
    exit();
}

$sekolah_id = $_SESSION['user_id'];
$page_title = 'Detail Posting';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$post_id = $_GET['id'];

// Ambil data posting admin yang relevan untuk sekolah
try {
    $stmt = $pdo->prepare("SELECT p.*, s.nama_sekolah 
                          FROM posts p 
                          LEFT JOIN sekolah s ON p.sekolah_id = s.id 
                          WHERE p.id = ? 
                          AND p.status = 'Published' 
                          AND (p.target_audience = 'Umum' OR p.target_audience = 'Sekolah' OR p.sekolah_id = ?)");
    $stmt->execute([$post_id, $sekolah_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit();
}

// Buat konten halaman
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-newspaper me-2"></i>Detail Posting</h2>
    <div>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo htmlspecialchars($post['judul']); ?></h5>
                    <div>
                        <span class="badge bg-<?php echo $post['jenis_post'] == 'Persyaratan' ? 'warning' : ($post['jenis_post'] == 'Pengumuman' ? 'danger' : 'info'); ?> me-2">
                            <?php echo $post['jenis_post']; ?>
                        </span>
                        <span class="badge bg-<?php echo $post['target_audience'] == 'Sekolah' ? 'primary' : ($post['target_audience'] == 'Juri' ? 'warning' : 'success'); ?> me-2">
                            <?php echo $post['target_audience']; ?>
                        </span>
                        <span class="badge bg-<?php echo $post['status'] == 'Published' ? 'success' : 'secondary'; ?>">
                            <?php echo $post['status']; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Dibuat: <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                        <?php if ($post['updated_at'] != $post['created_at']): ?>
                            | Diupdate: <?php echo date('d/m/Y H:i', strtotime($post['updated_at'])); ?>
                        <?php endif; ?>
                    </small>
                </div>
                
                <div class="post-content">
                    <?php echo $post['konten']; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-info-circle me-2"></i>Informasi Posting</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID Posting:</strong></td>
                        <td><?php echo $post['id']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jenis:</strong></td>
                        <td>
                            <span class="badge bg-<?php echo $post['jenis_post'] == 'Persyaratan' ? 'warning' : ($post['jenis_post'] == 'Pengumuman' ? 'danger' : 'info'); ?>">
                                <?php echo $post['jenis_post']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Target:</strong></td>
                        <td>
                            <span class="badge bg-<?php echo $post['target_audience'] == 'Sekolah' ? 'primary' : ($post['target_audience'] == 'Juri' ? 'warning' : 'success'); ?>">
                                <?php echo $post['target_audience']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-<?php echo $post['status'] == 'Published' ? 'success' : 'secondary'; ?>">
                                <?php echo $post['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php if ($post['nama_sekolah']): ?>
                    <tr>
                        <td><strong>Dari Sekolah:</strong></td>
                        <td><?php echo htmlspecialchars($post['nama_sekolah']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Dibuat:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Diupdate:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($post['updated_at'])); ?></td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi:</strong> Posting ini dibuat oleh admin. Sekolah hanya dapat melihat dan membaca posting.
                </div>
            </div>
        </div>
        
    </div>
</div>



$content = ob_get_clean();
include 'includes/layout.php';
?>



