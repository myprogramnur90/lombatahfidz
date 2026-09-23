<?php
session_start();
require_once '../config/database.php';
require_once '../config/security.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Pastikan pengaturan logo_sekolah ada di database
try {
    $cekLogo = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'logo_sekolah'");
    $cekLogo->execute();
    if ($cekLogo->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('logo_sekolah', '', 'Path file logo sekolah')")->execute();
    }
} catch (PDOException $e) {
    // Abaikan jika sudah ada
}

// Pastikan pengaturan seo ada di database
try {
    $cekSeo = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'seo'");
    $cekSeo->execute();
    if ($cekSeo->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('seo', '', 'Meta description untuk SEO')")->execute();
    }
} catch (PDOException $e) {
    // Abaikan jika sudah ada
}

// Pastikan pengaturan link_grup_wa ada di database
try {
    $cekGrupWa = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'link_grup_wa'");
    $cekGrupWa->execute();
    if ($cekGrupWa->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('link_grup_wa', '', 'Tautan grup WhatsApp')")->execute();
    }
} catch (PDOException $e) {
    // Abaikan jika sudah ada
}

// Proses update pengaturan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lomba = trim($_POST['nama_lomba']);
    $tanggal_penutupan = $_POST['tanggal_penutupan'];
    $biaya_pendaftaran = trim($_POST['biaya_pendaftaran']);
    $kontak_panitia = trim($_POST['kontak_panitia']);
    $link_grup_wa = trim($_POST['link_grup_wa']);
    $alamat_sekretariat = trim($_POST['alamat_sekretariat']);
    $seo = trim($_POST['seo'] ?? '');
    
    try {
        // Handle upload logo
        if (isset($_FILES['logo_sekolah']) && $_FILES['logo_sekolah']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/logo/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file = $_FILES['logo_sekolah'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if ($file['size'] > $maxSize) {
                $error = 'Ukuran logo maksimal 2MB!';
            } elseif (!in_array($ext, $allowedExts)) {
                $error = 'Format logo harus: JPG, PNG, GIF, WEBP, atau SVG!';
            } elseif (!in_array($mimeType, $allowedTypes) && $ext !== 'svg') {
                $error = 'Tipe file tidak diizinkan!';
            } else {
                // Hapus logo lama jika ada
                $logoLama = getPengaturan('logo_sekolah');
                if ($logoLama && file_exists('../' . $logoLama)) {
                    unlink('../' . $logoLama);
                }
                
                $namaFile = 'logo_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $namaFile;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    // Simpan path relatif ke database
                    $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = 'logo_sekolah'");
                    $stmt->execute(['uploads/logo/' . $namaFile]);
                    
                    // Buat/Update favicon.ico di root directory
                    copy($targetPath, '../favicon.ico');
                } else {
                    $error = 'Gagal mengupload logo!';
                }
            }
        }
        
        // Hapus logo jika tombol hapus ditekan
        if (isset($_POST['hapus_logo']) && $_POST['hapus_logo'] == '1') {
            $logoLama = getPengaturan('logo_sekolah');
            if ($logoLama && file_exists('../' . $logoLama)) {
                unlink('../' . $logoLama);
            }
            if (file_exists('../favicon.ico')) {
                unlink('../favicon.ico');
            }
            $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = '' WHERE nama_pengaturan = 'logo_sekolah'");
            $stmt->execute();
        }
        
        // Update pengaturan teks
        $pengaturan = [
            'nama_lomba' => $nama_lomba,
            'tanggal_penutupan' => $tanggal_penutupan,
            'biaya_pendaftaran' => $biaya_pendaftaran,
            'kontak_panitia' => $kontak_panitia,
            'link_grup_wa' => $link_grup_wa,
            'alamat_sekretariat' => $alamat_sekretariat,
            'seo' => $seo
        ];
        
        foreach ($pengaturan as $nama => $nilai) {
            $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = ?");
            $stmt->execute([$nilai, $nama]);
        }
        
        if (empty($error)) {
            $success = 'Pengaturan berhasil disimpan!';
        }
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

$logoPath = $pengaturan_data['logo_sekolah'] ?? '';
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
                            <form method="POST" enctype="multipart/form-data">
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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="link_grup_wa" class="form-label"><i class="fab fa-whatsapp me-1"></i>Link Grup WA</label>
                                        <input type="url" class="form-control" id="link_grup_wa" name="link_grup_wa" 
                                               value="<?php echo htmlspecialchars($pengaturan_data['link_grup_wa'] ?? ''); ?>" 
                                               placeholder="https://chat.whatsapp.com/...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="seo" class="form-label"><i class="fas fa-search me-1"></i>SEO (Meta Description)</label>
                                        <input type="text" class="form-control" id="seo" name="seo" 
                                               value="<?php echo htmlspecialchars($pengaturan_data['seo'] ?? ''); ?>" 
                                               placeholder="Masukkan deskripsi singkat untuk SEO">
                                    </div>
                                </div>

                                <!-- Logo Sekolah -->
                                <div class="mb-3">
                                    <label for="logo_sekolah" class="form-label"><i class="fas fa-image me-1"></i> Logo Sekolah / Lomba</label>
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <input type="file" class="form-control" id="logo_sekolah" name="logo_sekolah" accept="image/*" onchange="previewLogo(this)">
                                            <small class="text-muted">Format: JPG, PNG, GIF, WEBP, SVG. Maks: 2MB</small>
                                            <input type="hidden" name="hapus_logo" id="hapus_logo" value="0">
                                        </div>
                                        <div class="col-md-4 text-center mt-2 mt-md-0">
                                            <?php if ($logoPath && file_exists('../' . $logoPath)): ?>
                                                <div id="logo-preview">
                                                    <img src="../<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" style="max-height: 100px; max-width: 150px; border-radius: 10px; border: 2px solid #e9ecef; padding: 5px;">
                                                    <br>
                                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="hapusLogo()">
                                                        <i class="fas fa-trash me-1"></i>Hapus Logo
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div id="logo-preview">
                                                    <div style="width: 100px; height: 100px; border: 2px dashed #ccc; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #ccc;">
                                                        <i class="fas fa-image fa-2x"></i>
                                                    </div>
                                                    <small class="text-muted d-block mt-1">Belum ada logo</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
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
    <script>
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logo-preview').innerHTML = 
                    '<img src="' + e.target.result + '" alt="Preview Logo" style="max-height: 100px; max-width: 150px; border-radius: 10px; border: 2px solid #667eea; padding: 5px;">' +
                    '<br><small class="text-success mt-1 d-block"><i class="fas fa-check-circle"></i> Siap diupload</small>';
            }
            reader.readAsDataURL(input.files[0]);
            document.getElementById('hapus_logo').value = '0';
        }
    }
    function hapusLogo() {
        if (confirm('Yakin ingin menghapus logo?')) {
            document.getElementById('hapus_logo').value = '1';
            document.getElementById('logo_sekolah').value = '';
            document.getElementById('logo-preview').innerHTML = 
                '<div style="width: 100px; height: 100px; border: 2px dashed #e74c3c; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #e74c3c;">' +
                '<i class="fas fa-trash fa-2x"></i></div>' +
                '<small class="text-danger d-block mt-1">Logo akan dihapus saat disimpan</small>';
        }
    }
    </script>
</body>
</html>
