<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Proses update status dokumen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $dokumen_id = $_POST['dokumen_id'];
    $status = $_POST['status'];
    $catatan = trim($_POST['catatan']);
    
    try {
        $stmt = $pdo->prepare("UPDATE dokumen_berka SET status_dokumen = ?, catatan = ? WHERE id = ?");
        $stmt->execute([$status, $catatan, $dokumen_id]);
        $success = 'Status dokumen berhasil diupdate!';
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam mengupdate data!';
    }
}

// Proses delete dokumen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_dokumen') {
    $dokumen_id = $_POST['dokumen_id'];
    
    try {
        // Ambil nama file untuk dihapus dari folder uploads
        $stmt = $pdo->prepare("SELECT file_dokumen FROM dokumen_berka WHERE id = ?");
        $stmt->execute([$dokumen_id]);
        $dokumen = $stmt->fetch();
        
        if ($dokumen) {
            // Hapus file dari folder uploads
            $file_path = '../uploads/dokumen_berka/' . $dokumen['file_dokumen'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Hapus record dari database
            $stmt = $pdo->prepare("DELETE FROM dokumen_berka WHERE id = ?");
            $stmt->execute([$dokumen_id]);
            $success = 'Dokumen berhasil dihapus!';
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam menghapus data!';
    }
}

// Ambil data dokumen berka dengan join sekolah
try {
    $stmt = $pdo->query("
        SELECT d.*, s.nama_sekolah 
        FROM dokumen_berka d 
        JOIN sekolah s ON d.sekolah_id = s.id 
        ORDER BY d.created_at DESC
    ");
    $dokumen_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Berka - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 8px 20px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h2><i class="fas fa-file-alt me-2"></i>Dokumen Berka</h2>
                    
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
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Sekolah</th>
                                            <th>Jenis Dokumen</th>
                                            <th>Status</th>
                                            <th>Tanggal Upload</th>
                                            <th>File</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($dokumen_list)): ?>
                                            <?php foreach ($dokumen_list as $index => $dokumen): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($dokumen['nama_sekolah']); ?></td>
                                                    <td><?php echo htmlspecialchars($dokumen['jenis_dokumen']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $dokumen['status_dokumen'] == 'Diterima' ? 'success' : ($dokumen['status_dokumen'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                            <?php echo $dokumen['status_dokumen']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($dokumen['tanggal_upload'])); ?></td>
                                                    <td>
                                                        <a href="../uploads/dokumen_berka/<?php echo $dokumen['file_dokumen']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $dokumen['id']; ?>" title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $dokumen['id']; ?>" title="Hapus Dokumen">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal Update Status -->
                                                <div class="modal fade" id="statusModal<?php echo $dokumen['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Status Dokumen</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="dokumen_id" value="<?php echo $dokumen['id']; ?>">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Sekolah</label>
                                                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($dokumen['nama_sekolah']); ?>" readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jenis Dokumen</label>
                                                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($dokumen['jenis_dokumen']); ?>" readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="status" class="form-label">Status</label>
                                                                        <select class="form-control" name="status" required>
                                                                            <option value="Pending" <?php echo $dokumen['status_dokumen'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                            <option value="Diterima" <?php echo $dokumen['status_dokumen'] == 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
                                                                            <option value="Ditolak" <?php echo $dokumen['status_dokumen'] == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="catatan" class="form-label">Catatan</label>
                                                                        <textarea class="form-control" name="catatan" rows="3"><?php echo htmlspecialchars($dokumen['catatan']); ?></textarea>
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
                                                <div class="modal fade" id="deleteModal<?php echo $dokumen['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title text-danger">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="delete_dokumen">
                                                                <input type="hidden" name="dokumen_id" value="<?php echo $dokumen['id']; ?>">
                                                                <div class="modal-body">
                                                                    <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                                                                    <div class="alert alert-warning">
                                                                        <strong>Sekolah:</strong> <?php echo htmlspecialchars($dokumen['nama_sekolah']); ?><br>
                                                                        <strong>Jenis Dokumen:</strong> <?php echo htmlspecialchars($dokumen['jenis_dokumen']); ?><br>
                                                                        <strong>File:</strong> <?php echo htmlspecialchars($dokumen['file_dokumen']); ?>
                                                                    </div>
                                                                    <p class="text-danger"><strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-trash me-1"></i>Hapus
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Belum ada dokumen berka</td>
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
