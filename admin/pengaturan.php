<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Proses update pengaturan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lomba = trim($_POST['nama_lomba']);
    $tanggal_penutupan = $_POST['tanggal_penutupan'];
    $biaya_pendaftaran = trim($_POST['biaya_pendaftaran']);
    $kontak_panitia = trim($_POST['kontak_panitia']);
    $alamat_sekretariat = trim($_POST['alamat_sekretariat']);
    
    try {
        // Update pengaturan
        $pengaturan = [
            'nama_lomba' => $nama_lomba,
            'tanggal_penutupan' => $tanggal_penutupan,
            'biaya_pendaftaran' => $biaya_pendaftaran,
            'kontak_panitia' => $kontak_panitia,
            'alamat_sekretariat' => $alamat_sekretariat
        ];
        
        foreach ($pengaturan as $nama => $nilai) {
            $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = ?");
            $stmt->execute([$nilai, $nama]);
        }
        
        $success = 'Pengaturan berhasil disimpan!';
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam menyimpan pengaturan!';
    }
}

// Ambil data pengaturan
try {
    $stmt = $pdo->query("SELECT * FROM pengaturan ORDER BY nama_pengaturan");
    $pengaturan_list = $stmt->fetchAll();
    
    // Convert ke array asosiatif
    $pengaturan_data = [];
    foreach ($pengaturan_list as $p) {
        $pengaturan_data[$p['nama_pengaturan']] = $p['nilai'];
    }
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .form-control { border-radius: 10px; border: 2px solid #e9ecef; padding: 12px 15px; transition: all 0.3s ease; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 12px 30px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h2><i class="fas fa-cog me-2"></i>Pengaturan Sistem</h2>
                    
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
                        <div class="card-header">
                            <h5><i class="fas fa-cog me-2"></i>Konfigurasi Lomba</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nama_lomba" class="form-label">Nama Lomba <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lomba" name="nama_lomba" 
                                               value="<?php echo htmlspecialchars($pengaturan_data['nama_lomba'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_penutupan" class="form-label">Tanggal Penutupan Pendaftaran <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_penutupan" name="tanggal_penutupan" 
                                               value="<?php echo $pengaturan_data['tanggal_penutupan'] ?? ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="biaya_pendaftaran" class="form-label">Biaya Pendaftaran (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="biaya_pendaftaran" name="biaya_pendaftaran" 
                                               value="<?php echo $pengaturan_data['biaya_pendaftaran'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="kontak_panitia" class="form-label">Kontak Panitia <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kontak_panitia" name="kontak_panitia" 
                                               value="<?php echo htmlspecialchars($pengaturan_data['kontak_panitia'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="alamat_sekretariat" class="form-label">Alamat Sekretariat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat_sekretariat" name="alamat_sekretariat" rows="3" required><?php echo htmlspecialchars($pengaturan_data['alamat_sekretariat'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Informasi Sistem -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5><i class="fas fa-info-circle me-2"></i>Informasi Sistem</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Versi Sistem:</strong> 1.0.0</p>
                                    <p><strong>Database:</strong> MySQL</p>
                                    <p><strong>Framework:</strong> PHP Native</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Sekolah:</strong> 
                                        <?php 
                                        try {
                                            $stmt = $pdo->query("SELECT COUNT(*) FROM sekolah");
                                            echo $stmt->fetchColumn();
                                        } catch (PDOException $e) {
                                            echo "0";
                                        }
                                        ?>
                                    </p>
                                    <p><strong>Total Peserta:</strong> 
                                        <?php 
                                        try {
                                            $stmt = $pdo->query("SELECT COUNT(*) FROM peserta");
                                            echo $stmt->fetchColumn();
                                        } catch (PDOException $e) {
                                            echo "0";
                                        }
                                        ?>
                                    </p>
                                    <p><strong>Status Sistem:</strong> <span class="badge bg-success">Aktif</span></p>
                                </div>
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
