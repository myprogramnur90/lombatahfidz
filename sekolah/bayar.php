<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

requireSekolahLogin();
$sekolah_id = $_SESSION['sekolah_id'];
$page_title = 'Pembayaran';
$success = '';
$error = '';

// Proses upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateCsrfToken();
    $nominal = $_POST['nominal'];
    // hilangkan titik jika ada format ribuan
    $nominal = str_replace('.', '', $nominal);
    
    if (empty($nominal)) {
        $error = 'Nominal harus diisi!';
    } else {
        $upload_dir = '../uploads/bukti_pembayaran/';
        ensureUploadDir($upload_dir);
        
        $berhasil = 0;
        $gagal = 0;
        $error_msgs = [];
        
        if (isset($_FILES['bukti_pembayaran']) && is_array($_FILES['bukti_pembayaran']['name']) && count($_FILES['bukti_pembayaran']['name']) > 0 && $_FILES['bukti_pembayaran']['name'][0] != '') {
            $total_files = count($_FILES['bukti_pembayaran']['name']);
            $uploaded_files = [];
            
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['bukti_pembayaran']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['bukti_pembayaran']['tmp_name'][$i];
                    $name = $_FILES['bukti_pembayaran']['name'][$i];
                    $size = $_FILES['bukti_pembayaran']['size'][$i];
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $tmp_name);
                    finfo_close($finfo);
                    
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
                    $allowed_mimes = ['image/jpeg', 'image/png', 'application/pdf'];
                    
                    if ($size > 2 * 1024 * 1024) {
                        $error_msgs[] = "File $name ukurannya lebih dari 2MB.";
                    } elseif (!in_array($ext, $allowed_exts) || !in_array($mimeType, $allowed_mimes)) {
                        $error_msgs[] = "Format file $name tidak didukung.";
                    } else {
                        $file_name = generateSafeFilename($sekolah_id, $ext);
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($tmp_name, $file_path)) {
                            $uploaded_files[] = $file_name;
                        } else {
                            $error_msgs[] = "Gagal memindahkan file $name.";
                        }
                    }
                }
            }
            
            if (count($uploaded_files) > 0) {
                try {
                    $file_names_str = implode(',', $uploaded_files);
                    $stmt = $pdo->prepare("INSERT INTO pembayaran (sekolah_id, nominal, bukti_pembayaran) VALUES (?, ?, ?)");
                    $stmt->execute([$sekolah_id, $nominal, $file_names_str]);
                    $success = count($uploaded_files) . " file bukti pembayaran berhasil diupload!" . (count($error_msgs) > 0 ? " (" . implode(', ', $error_msgs) . ")" : "");
                } catch (PDOException $e) {
                    error_log('Payment upload error: ' . $e->getMessage());
                    $error = 'Terjadi kesalahan dalam menyimpan data ke database!';
                }
            } else {
                $error = 'Gagal mengupload file! ' . implode(', ', $error_msgs);
            }
        } else {
            $error = 'Pilih setidaknya satu file bukti pembayaran!';
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
                                        <?php echo getCsrfInput(); ?>
                                        <div class="mb-3">
                                            <label for="nominal" class="form-label">Nominal Pembayaran <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nominal" name="nominal" 
                                                   value="<?php echo !empty(getPengaturan('biaya_pendaftaran')) ? number_format((int)getPengaturan('biaya_pendaftaran'), 0, '', '.') : ''; ?>" 
                                                   required onkeyup="formatRupiah(this)">
                                        </div>
                                        <div class="mb-3">
                                            <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran[]" 
                                                   accept="image/*,.pdf" required multiple>
                                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB per file). Anda dapat memilih lebih dari satu file.</small>
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
                                                <?php 
                                                $buktis = explode(',', $pembayaran['bukti_pembayaran']);
                                                foreach ($buktis as $index => $bukti): 
                                                    $bukti = trim($bukti);
                                                    if (!empty($bukti)):
                                                ?>
                                                    <a href="../uploads/bukti_pembayaran/<?php echo $bukti; ?>" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                                        <i class="fas fa-eye me-1"></i>Lihat <?php echo count($buktis) > 1 ? ($index + 1) : ''; ?>
                                                    </a>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada data pembayaran</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

<script>
function formatRupiah(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    input.value = formatted;
}
</script>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
