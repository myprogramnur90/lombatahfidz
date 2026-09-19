<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

requireSekolahLogin();
$sekolah_id = $_SESSION['sekolah_id'];
$page_title = 'Edit Data Peserta';
$success = '';
$error = '';

// Ambil ID peserta dari URL
$peserta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($peserta_id <= 0) {
    header('Location: peserta.php');
    exit();
}

// Ambil data peserta
try {
    $stmt = $pdo->prepare("SELECT * FROM peserta WHERE id = ? AND sekolah_id = ?");
    $stmt->execute([$peserta_id, $sekolah_id]);
    $peserta = $stmt->fetch();
    
    if (!$peserta) {
        header('Location: peserta.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Proses update peserta
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateCsrfToken();
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nisn = trim($_POST['nisn']);
    $tempat_lahir = trim($_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = trim($_POST['alamat']);
    $no_hp = trim($_POST['no_hp']);
    $kelas = trim($_POST['kelas']);
    
    // Validasi
    if (empty($nama_lengkap) || empty($nisn) || empty($tempat_lahir) || empty($tanggal_lahir) || 
        empty($jenis_kelamin) || empty($alamat) || empty($no_hp) || empty($kelas)) {
        $error = 'Semua field harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE peserta SET nama_lengkap = ?, nisn = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, alamat = ?, no_hp = ?, kelas = ? WHERE id = ? AND sekolah_id = ?");
            $stmt->execute([$nama_lengkap, $nisn, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $kelas, $peserta_id, $sekolah_id]);
            
            $success = 'Data peserta berhasil diupdate!';
            
            // Update data peserta untuk ditampilkan
            $peserta['nama_lengkap'] = $nama_lengkap;
            $peserta['nisn'] = $nisn;
            $peserta['tempat_lahir'] = $tempat_lahir;
            $peserta['tanggal_lahir'] = $tanggal_lahir;
            $peserta['jenis_kelamin'] = $jenis_kelamin;
            $peserta['alamat'] = $alamat;
            $peserta['no_hp'] = $no_hp;
            $peserta['kelas'] = $kelas;
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan dalam mengupdate data!';
        }
    }
}

// Buat konten edit peserta
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit me-2"></i>Edit Data Peserta</h2>
    <a href="peserta.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-edit me-2"></i>Form Edit Data Peserta</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <?php echo getCsrfInput(); ?>
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                           value="<?php echo htmlspecialchars($peserta['nama_lengkap']); ?>" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nisn" name="nisn" 
                           value="<?php echo htmlspecialchars($peserta['nisn']); ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="kelas" name="kelas" 
                           value="<?php echo htmlspecialchars($peserta['kelas']); ?>" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="L" <?php echo $peserta['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?php echo $peserta['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                           value="<?php echo htmlspecialchars($peserta['tempat_lahir']); ?>" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                           value="<?php echo $peserta['tanggal_lahir']; ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($peserta['alamat']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="no_hp" name="no_hp" 
                       value="<?php echo htmlspecialchars($peserta['no_hp']); ?>" required>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="peserta.php" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Data
                </button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
