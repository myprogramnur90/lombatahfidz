<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

requireSekolahLogin();
$sekolah_id = $_SESSION['sekolah_id'];
$page_title = 'Dokumen Berka';
$success = '';
$error = '';

// Proses upload dokumen berka
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateCsrfToken();
    $jenis_dokumen = $_POST['jenis_dokumen'];
    
    if (empty($jenis_dokumen)) {
        $error = 'Jenis dokumen harus dipilih!';
    } else {
        // Validasi file upload
        $validation = validateFileUpload('file_dokumen');
        if (!$validation['success']) {
            $error = $validation['error'];
        } else {
            $upload_dir = '../uploads/dokumen_berka/';
            ensureUploadDir($upload_dir);
            
            $file_name = generateSafeFilename($sekolah_id, $validation['extension']);
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['file_dokumen']['tmp_name'], $file_path)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO dokumen_berka (sekolah_id, jenis_dokumen, file_dokumen) VALUES (?, ?, ?)");
                    $stmt->execute([$sekolah_id, $jenis_dokumen, $file_name]);
                    $success = 'Dokumen berhasil diupload!';
                } catch (PDOException $e) {
                    error_log('Dokumen upload error: ' . $e->getMessage());
                    $error = 'Terjadi kesalahan dalam menyimpan data!';
                }
            } else {
                $error = 'Gagal mengupload file!';
            }
        }
    }
}

// Ambil data dokumen berka
try {
    $stmt = $pdo->prepare("SELECT * FROM dokumen_berka WHERE sekolah_id = ? ORDER BY created_at DESC");
    $stmt->execute([$sekolah_id]);
    $dokumen_berka = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Buat konten dokumen berka
ob_start();
?>
<h2><i class="fas fa-file-alt me-2"></i>Dokumen Berka</h2>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-upload me-2"></i>Upload Dokumen</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" enctype="multipart/form-data">
                                        <?php echo getCsrfInput(); ?>
                                        <div class="mb-3">
                                            <label for="jenis_dokumen" class="form-label">Jenis Dokumen <span class="text-danger">*</span></label>
                                            <select class="form-control" id="jenis_dokumen" name="jenis_dokumen" required>
                                                <option value="">Pilih Jenis Dokumen</option>
                                                <option value="Surat Keterangan Aktif">Surat Keterangan Aktif</option>
                                                <option value="KTS">KTS</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="file_dokumen" class="form-label">File Dokumen <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="file_dokumen" name="file_dokumen" 
                                                   accept="image/*,.pdf" required>
                                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload me-2"></i>Upload Dokumen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-list me-2"></i>Daftar Dokumen</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($dokumen_berka)): ?>
                                        <?php foreach ($dokumen_berka as $dokumen): ?>
                                            <div class="border rounded p-3 mb-3">
                                                <h6><?php echo sanitizeOutput($dokumen['jenis_dokumen']); ?></h6>
                                                <p class="mb-1">
                                                    <strong>Status:</strong> 
                                                    <span class="badge bg-<?php echo $dokumen['status_dokumen'] == 'Diterima' ? 'success' : ($dokumen['status_dokumen'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                        <?php echo ucfirst($dokumen['status_dokumen']); ?>
                                                    </span>
                                                </p>
                                                <p class="mb-1"><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i', strtotime($dokumen['tanggal_upload'])); ?></p>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="showDokumenModal('../uploads/dokumen_berka/<?php echo htmlspecialchars($dokumen['file_dokumen']); ?>')">
                                                    <i class="fas fa-eye me-1"></i>Lihat
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada dokumen yang diupload</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

<!-- Modal Dokumen -->
<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dokumenModalLabel">Dokumen Berka</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" id="dokumenModalBody">
        <!-- Konten akan dimuat via JS -->
      </div>
    </div>
  </div>
</div>

<script>
function showDokumenModal(fileUrl) {
    let modalBody = document.getElementById('dokumenModalBody');
    let ext = fileUrl.split('.').pop().toLowerCase();
    
    // Tampilkan loading spinner sementara memuat konten
    modalBody.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    
    // Inisialisasi modal dan tampilkan
    var myModal = new bootstrap.Modal(document.getElementById('dokumenModal'));
    myModal.show();
    
    // Tentukan konten berdasarkan ekstensi (pdf vs image)
    setTimeout(() => {
        if (ext === 'pdf') {
            modalBody.innerHTML = `<iframe src="${fileUrl}" width="100%" height="500px" style="border: none;"></iframe>`;
        } else {
            modalBody.innerHTML = `<img src="${fileUrl}" class="img-fluid rounded" alt="Dokumen" style="max-height: 80vh; max-width: 100%;">`;
        }
    }, 300); // Sedikit delay agar modal terbuka lebih mulus
}
</script>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
