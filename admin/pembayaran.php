<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateCsrfToken();
}

// Proses update status pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $pembayaran_id = $_POST['pembayaran_id'];
    $status = $_POST['status'];
    $catatan = trim($_POST['catatan']);
    
    try {
        $stmt = $pdo->prepare("UPDATE pembayaran SET status_pembayaran = ?, catatan = ? WHERE id = ?");
        $stmt->execute([$status, $catatan, $pembayaran_id]);
        $success = 'Status pembayaran berhasil diupdate!';
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam mengupdate data!';
    }
}

// Proses delete pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_pembayaran') {
    $pembayaran_id = $_POST['pembayaran_id'];
    
    try {
        // Ambil data pembayaran untuk menghapus file bukti
        $stmt = $pdo->prepare("SELECT bukti_pembayaran FROM pembayaran WHERE id = ?");
        $stmt->execute([$pembayaran_id]);
        $pembayaran = $stmt->fetch();
        
        // Hapus data dari database
        $stmt = $pdo->prepare("DELETE FROM pembayaran WHERE id = ?");
        $stmt->execute([$pembayaran_id]);
        
        // Hapus file bukti pembayaran jika ada
        if ($pembayaran && $pembayaran['bukti_pembayaran']) {
            $file_path = '../uploads/bukti_pembayaran/' . $pembayaran['bukti_pembayaran'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $success = 'Data pembayaran berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam menghapus data!';
    }
}

// Ambil data pembayaran dengan join sekolah
try {
    $stmt = $pdo->query("
        SELECT p.*, s.nama_sekolah 
        FROM pembayaran p 
        JOIN sekolah s ON p.sekolah_id = s.id 
        ORDER BY p.created_at DESC
    ");
    $pembayaran_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Ambil statistik pembayaran
try {
    $total_pembayaran = $pdo->query("SELECT COUNT(*) FROM pembayaran")->fetchColumn();
    $pembayaran_lunas = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran = 'Lunas'")->fetchColumn();
    $pembayaran_pending = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran = 'Pending'")->fetchColumn();
    $pembayaran_ditolak = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE status_pembayaran = 'Ditolak'")->fetchColumn();
    
    // Total nominal pembayaran yang sudah lunas
    $total_nominal_lunas = $pdo->query("SELECT COALESCE(SUM(nominal), 0) FROM pembayaran WHERE status_pembayaran = 'Lunas'")->fetchColumn();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil statistik!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembayaran - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 8px 20px; font-weight: 600; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-card .card-body { padding: 2rem; }
        .stat-number { font-size: 2.5rem; font-weight: bold; }
        
        /* Print Styles */
        @media print {
            * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; }
            .sidebar, .btn, .modal, .alert, .d-flex { display: none !important; }
            .col-md-9, .col-lg-10 { width: 100% !important; max-width: 100% !important; }
            .container-fluid { padding: 0 !important; margin: 0 !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; page-break-inside: avoid; }
            .table { font-size: 11px !important; width: 100% !important; }
            .stat-card { background: #f8f9fa !important; color: #000 !important; }
            .table-responsive { overflow: visible !important; }
            body { font-size: 12px !important; }
            h2 { font-size: 18px !important; margin-bottom: 10px !important; }
            .row { margin: 0 !important; }
            .col-md-3 { width: 25% !important; float: left !important; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                        <h2><i class="fas fa-credit-card me-2"></i>Data Pembayaran</h2>
                        <div class="mt-2 mt-md-0">
                            <a href="export_pembayaran.php" class="btn btn-success me-2 mb-2 mb-md-0">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </a>
                            <button id="printBtn" class="btn btn-info">
                                <i class="fas fa-print me-2"></i>Print
                            </button>
                        </div>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips Print:</strong> Jika tombol print tidak berfungsi, gunakan <kbd>Ctrl+P</kbd> (Windows) atau <kbd>Cmd+P</kbd> (Mac) untuk membuka dialog print.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    
                    <!-- Statistik Pembayaran -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-credit-card fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo $total_pembayaran; ?></div>
                                    <div>Total Pembayaran</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo $pembayaran_lunas; ?></div>
                                    <div>Lunas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo $pembayaran_pending; ?></div>
                                    <div>Pending</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                    <div class="stat-number">Rp <?php echo number_format($total_nominal_lunas); ?></div>
                                    <div>Total Terkumpul</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Sekolah</th>
                                            <th>Nominal</th>
                                            <th>Status</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Bukti</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $modalsHtml = '';
                                        if (!empty($pembayaran_list)): ?>
                                            <?php foreach ($pembayaran_list as $index => $pembayaran): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($pembayaran['nama_sekolah']); ?></td>
                                                    <td>Rp <?php echo number_format($pembayaran['nominal']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $pembayaran['status_pembayaran'] == 'Lunas' ? 'success' : ($pembayaran['status_pembayaran'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                            <?php echo $pembayaran['status_pembayaran']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])); ?></td>
                                                    <td>
                                                        <?php if ($pembayaran['bukti_pembayaran']): ?>
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $pembayaran['id']; ?>" title="Lihat Bukti">
                                                                <img src="../uploads/bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>" alt="Bukti" style="max-height: 50px; max-width: 50px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $pembayaran['id']; ?>" title="Edit Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $pembayaran['id']; ?>" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php ob_start(); ?>
                                                
                                                <?php if ($pembayaran['bukti_pembayaran']): ?>
                                                <!-- Modal Image -->
                                                <div class="modal fade" id="imageModal<?php echo $pembayaran['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Bukti Pembayaran: <?php echo htmlspecialchars($pembayaran['nama_sekolah']); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center p-0">
                                                                <img src="../uploads/bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>" alt="Bukti Pembayaran" style="max-width: 100%; height: auto;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>

                                                <!-- Modal Update Status -->
                                                <div class="modal fade" id="statusModal<?php echo $pembayaran['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Status Pembayaran</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <?php echo getCsrfInput(); ?>
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="pembayaran_id" value="<?php echo $pembayaran['id']; ?>">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Sekolah</label>
                                                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($pembayaran['nama_sekolah']); ?>" readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nominal</label>
                                                                        <input type="text" class="form-control" value="Rp <?php echo number_format($pembayaran['nominal']); ?>" readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="status" class="form-label">Status</label>
                                                                        <select class="form-control" name="status" required>
                                                                            <option value="Pending" <?php echo $pembayaran['status_pembayaran'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                            <option value="Lunas" <?php echo $pembayaran['status_pembayaran'] == 'Lunas' ? 'selected' : ''; ?>>Lunas</option>
                                                                            <option value="Ditolak" <?php echo $pembayaran['status_pembayaran'] == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="catatan" class="form-label">Catatan</label>
                                                                        <textarea class="form-control" name="catatan" rows="3"><?php echo htmlspecialchars($pembayaran['catatan']); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Delete Confirmation -->
                                                <div class="modal fade" id="deleteModal<?php echo $pembayaran['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <?php echo getCsrfInput(); ?>
                                                                <input type="hidden" name="action" value="delete_pembayaran">
                                                                <input type="hidden" name="pembayaran_id" value="<?php echo $pembayaran['id']; ?>">
                                                                <div class="modal-body">
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                                                                    </div>
                                                                    <p>Apakah Anda yakin ingin menghapus data pembayaran berikut?</p>
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                <div class="col-sm-4"><strong>Sekolah:</strong></div>
                                                                                <div class="col-sm-8"><?php echo htmlspecialchars($pembayaran['nama_sekolah']); ?></div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-sm-4"><strong>Nominal:</strong></div>
                                                                                <div class="col-sm-8">Rp <?php echo number_format($pembayaran['nominal']); ?></div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-sm-4"><strong>Status:</strong></div>
                                                                                <div class="col-sm-8">
                                                                                    <span class="badge bg-<?php echo $pembayaran['status_pembayaran'] == 'Lunas' ? 'success' : ($pembayaran['status_pembayaran'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                                                        <?php echo $pembayaran['status_pembayaran']; ?>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-sm-4"><strong>Tanggal:</strong></div>
                                                                                <div class="col-sm-8"><?php echo date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])); ?></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php if ($pembayaran['bukti_pembayaran']): ?>
                                                                        <div class="alert alert-info">
                                                                            <i class="fas fa-info-circle me-2"></i>
                                                                            File bukti pembayaran juga akan dihapus dari server.
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        <i class="fas fa-times me-2"></i>Batal
                                                                    </button>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-trash me-2"></i>Ya, Hapus Data
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php $modalsHtml .= ob_get_clean(); ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Belum ada data pembayaran</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php echo $modalsHtml; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk export Excel dengan loading
        function exportExcel() {
            const btn = document.querySelector('a[href="export_pembayaran.php"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
            btn.style.pointerEvents = 'none';
            
            // Simulasi loading
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.pointerEvents = 'auto';
            }, 2000);
        }
        
        // Fungsi untuk print dengan loading
        function printData() {
            console.log('Print function called');
            const btn = document.getElementById('printBtn');
            if (!btn) {
                console.error('Print button not found');
                fallbackPrint();
                return;
            }
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Printing...';
            btn.disabled = true;
            
            // Print setelah loading
            setTimeout(() => {
                try {
                    console.log('Attempting to print...');
                    window.print();
                    console.log('Print dialog opened');
                } catch (error) {
                    console.error('Print error:', error);
                    alert('Gagal membuka dialog print. Silakan gunakan Ctrl+P atau Cmd+P');
                }
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 500);
        }
        
        // Event listener untuk tombol
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up event listeners');
            
            const exportBtn = document.querySelector('a[href="export_pembayaran.php"]');
            const printBtn = document.getElementById('printBtn');
            
            console.log('Export button:', exportBtn);
            console.log('Print button:', printBtn);
            
            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    exportExcel();
                });
            }
            
            if (printBtn) {
                printBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Print button clicked');
                    printData();
                });
            } else {
                console.error('Print button not found in DOM');
            }
        });
        
        // Fallback untuk print jika ada masalah
        function fallbackPrint() {
            try {
                window.print();
            } catch (error) {
                alert('Gagal membuka dialog print. Silakan gunakan Ctrl+P (Windows) atau Cmd+P (Mac)');
            }
        }
        
        // Test print function
        function testPrint() {
            console.log('Testing print function...');
            if (typeof window.print === 'function') {
                console.log('window.print is available');
                window.print();
            } else {
                console.error('window.print is not available');
            }
        }
    </script>
</body>
</html>
