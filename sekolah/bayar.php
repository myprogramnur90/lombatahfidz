<?php
session_start();
require_once '../config/database.php';

$sekolah_id = $_SESSION['user_id'];
$page_title = 'Pembayaran';
$success = '';
$error = '';

// Proses upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nominal = $_POST['nominal'];
    
    if (empty($nominal)) {
        $error = 'Nominal harus diisi!';
    } else {
        // Upload file
        $upload_dir = '../uploads/bukti_pembayaran/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $sekolah_id . '_' . time() . '_' . $_FILES['bukti_pembayaran']['name'];
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $file_path)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO pembayaran (sekolah_id, nominal, bukti_pembayaran) VALUES (?, ?, ?)");
                $stmt->execute([$sekolah_id, $nominal, $file_name]);
                $success = 'Bukti pembayaran berhasil diupload!';
            } catch (PDOException $e) {
                $error = 'Terjadi kesalahan dalam menyimpan data!';
            }
        } else {
            $error = 'Gagal mengupload file!';
        }
    }
}

// Ambil data pembayaran terakhir
try {
    $stmt = $pdo->prepare("SELECT * FROM pembayaran WHERE sekolah_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$sekolah_id]);
    $pembayaran = $stmt->fetch();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Buat konten pembayaran
ob_start();
?>
<h2><i class="fas fa-credit-card me-2"></i>Pembayaran</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="nominal" class="form-label">Nominal Pembayaran <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="nominal" name="nominal" 
                                                   value="<?php echo getPengaturan('biaya_pendaftaran'); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" 
                                                   accept="image/*,.pdf" required>
                                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload me-2"></i>Upload Bukti
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle me-2"></i>Status Pembayaran</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($pembayaran): ?>
                                        <p><strong>Nominal:</strong> Rp <?php echo number_format($pembayaran['nominal']); ?></p>
                                        <p><strong>Status:</strong> 
                                            <span class="badge bg-<?php echo $pembayaran['status_pembayaran'] == 'Lunas' ? 'success' : ($pembayaran['status_pembayaran'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                <?php echo ucfirst($pembayaran['status_pembayaran']); ?>
                                            </span>
                                        </p>
                                        <p><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])); ?></p>
                                        <?php if ($pembayaran['bukti_pembayaran']): ?>
                                            <p><strong>Bukti:</strong> 
                                                <a href="../uploads/bukti_pembayaran/<?php echo $pembayaran['bukti_pembayaran']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>Lihat
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada data pembayaran</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
