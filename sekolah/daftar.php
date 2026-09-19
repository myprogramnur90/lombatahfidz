<?php
session_start();
require_once '../config/database.php';

$sekolah_id = $_SESSION['user_id'];
$page_title = 'Daftar Peserta';
$success = '';
$error = '';

// Proses form pendaftaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            $stmt = $pdo->prepare("INSERT INTO peserta (sekolah_id, nama_lengkap, nisn, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_hp, kelas) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sekolah_id, $nama_lengkap, $nisn, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $kelas]);
            
            $success = 'Peserta berhasil didaftarkan!';
            
            // Reset form
            $_POST = array();
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan dalam menyimpan data!';
        }
    }
}

// Buat konten daftar peserta
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-plus me-2"></i>Daftar Peserta Baru</h2>
</div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-user-plus me-2"></i>Form Pendaftaran Peserta</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                               value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nisn" name="nisn" 
                                               value="<?php echo isset($_POST['nisn']) ? htmlspecialchars($_POST['nisn']) : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kelas" name="kelas" 
                                               value="<?php echo isset($_POST['kelas']) ? htmlspecialchars($_POST['kelas']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <!-- Kolom kosong untuk layout -->
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                                               value="<?php echo isset($_POST['tempat_lahir']) ? htmlspecialchars($_POST['tempat_lahir']) : ''; ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                                               value="<?php echo isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : ''; ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                            <option value="P" <?php echo (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="no_hp" name="no_hp" 
                                           value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>" required>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Peserta akan mengikuti Lomba Tahfidz Juz 30. Pastikan data yang diisi sudah benar.
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Peserta
                                    </button>
                                </div>
                            </form>
                        </div>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
