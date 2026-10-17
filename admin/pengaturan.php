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

// Pastikan pengaturan nama_sekolah ada di database
try {
    $cekNamaSekolah = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'nama_sekolah'");
    $cekNamaSekolah->execute();
    if ($cekNamaSekolah->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('nama_sekolah', '', 'Nama Sekolah Penyelenggara')")->execute();
    }
} catch (PDOException $e) {
    // Abaikan jika sudah ada
}

// Pastikan pengaturan maksimal_peserta ada di database
try {
    $cekMaksimalPeserta = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'maksimal_peserta'");
    $cekMaksimalPeserta->execute();
    if ($cekMaksimalPeserta->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('maksimal_peserta', '100', 'Maksimal jumlah peserta keseluruhan')")->execute();
    }
} catch (PDOException $e) {
    // Abaikan jika sudah ada
}

// Pastikan pengaturan bel_jeda_teguran ada di database
try {
    $cekJeda = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'bel_jeda_teguran'");
    $cekJeda->execute();
    if ($cekJeda->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('bel_jeda_teguran', '8', 'Durasi jeda waktu teguran peserta (detik)')")->execute();
    }
} catch (PDOException $e) {}

// Pastikan pengaturan bel_jenis_bunyi ada di database
try {
    $cekBunyi = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE nama_pengaturan = 'bel_jenis_bunyi'");
    $cekBunyi->execute();
    if ($cekBunyi->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('bel_jenis_bunyi', 'kuningan', 'Jenis bunyi bel musabaqoh (kuningan, digital, gong, tabung)')")->execute();
    }
} catch (PDOException $e) {}

// Proses update pengaturan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_sekolah = trim($_POST['nama_sekolah'] ?? '');
    $nama_lomba = trim($_POST['nama_lomba']);
    $tanggal_penutupan = $_POST['tanggal_penutupan'];
    $biaya_pendaftaran = trim($_POST['biaya_pendaftaran']);
    $biaya_pendaftaran = str_replace('.', '', $biaya_pendaftaran); // hilangkan titik sebelum disimpan
    $kontak_panitia = trim($_POST['kontak_panitia']);
    $link_grup_wa = trim($_POST['link_grup_wa']);
    $alamat_sekretariat = trim($_POST['alamat_sekretariat']);
    $seo = trim($_POST['seo'] ?? '');
    $maksimal_peserta = trim($_POST['maksimal_peserta'] ?? '100');
    $bel_jeda_teguran = (int)($_POST['bel_jeda_teguran'] ?? 8);
    if ($bel_jeda_teguran <= 0) $bel_jeda_teguran = 8;
    $bel_jenis_bunyi = trim($_POST['bel_jenis_bunyi'] ?? 'kuningan');
    $allowed_bunyi = ['kuningan', 'digital', 'gong', 'tabung'];
    if (!in_array($bel_jenis_bunyi, $allowed_bunyi)) $bel_jenis_bunyi = 'kuningan';
    
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
            'nama_sekolah' => $nama_sekolah,
            'nama_lomba' => $nama_lomba,
            'tanggal_penutupan' => $tanggal_penutupan,
            'biaya_pendaftaran' => $biaya_pendaftaran,
            'kontak_panitia' => $kontak_panitia,
            'link_grup_wa' => $link_grup_wa,
            'alamat_sekretariat' => $alamat_sekretariat,
            'seo' => $seo,
            'maksimal_peserta' => $maksimal_peserta,
            'bel_jeda_teguran' => (string)$bel_jeda_teguran,
            'bel_jenis_bunyi' => $bel_jenis_bunyi
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
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
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
                                    <div class="col-md-12 mb-3">
                                        <label for="nama_sekolah" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" 
                                               value="<?php echo htmlspecialchars($pengaturan_data['nama_sekolah'] ?? ''); ?>" required>
                                    </div>
                                </div>
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
                                        <input type="text" class="form-control" id="biaya_pendaftaran" name="biaya_pendaftaran" 
                                               value="<?php echo !empty($pengaturan_data['biaya_pendaftaran']) ? number_format((int)$pengaturan_data['biaya_pendaftaran'], 0, '', '.') : ''; ?>" 
                                               required onkeyup="formatRupiah(this)">
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
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="maksimal_peserta" class="form-label">Maksimal Peserta <span class="text-danger">*</span></label>
                                        <input type="number" min="1" class="form-control" id="maksimal_peserta" name="maksimal_peserta" 
                                               value="<?php echo htmlspecialchars($pengaturan_data['maksimal_peserta'] ?? '100'); ?>" required>
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
                                
                                <!-- Pengaturan Bel Musabaqoh (MHQ) -->
                                <div class="card my-4 p-3" style="background: #fdfefe; border: 2px solid #5a78d1; border-radius: 14px;">
                                    <h5 class="fw-bold mb-3" style="color: #2c3e50;"><i class="fas fa-bell me-2 text-warning"></i>Pengaturan Bel Musabaqoh (MHQ)</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="bel_jeda_teguran" class="form-label fw-bold">
                                                <i class="fas fa-stopwatch me-1 text-warning"></i> Jeda Waktu Teguran Bel 1x (Detik) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" min="3" max="60" class="form-control" id="bel_jeda_teguran" name="bel_jeda_teguran" 
                                                       value="<?php echo htmlspecialchars($pengaturan_data['bel_jeda_teguran'] ?? '8'); ?>" required>
                                                <span class="input-group-text">Detik</span>
                                            </div>
                                            <small class="text-muted">Batas jeda waktu kesempatan peserta tawaqquf/memperbaiki hafalan sebelum otomatis pindah soal (umumnya 5, 8, atau 10 detik).</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="bel_jenis_bunyi" class="form-label fw-bold">
                                                <i class="fas fa-music me-1 text-info"></i> Pilihan Karakter Bunyi Bel <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <select class="form-select" id="bel_jenis_bunyi" name="bel_jenis_bunyi">
                                                    <option value="kuningan" <?php echo ($pengaturan_data['bel_jenis_bunyi'] ?? 'kuningan') === 'kuningan' ? 'selected' : ''; ?>>Lonceng Kuningan / Brass Bell (Klasik MHQ)</option>
                                                    <option value="digital" <?php echo ($pengaturan_data['bel_jenis_bunyi'] ?? '') === 'digital' ? 'selected' : ''; ?>>Bel Digital / Ting Elektronik (Tajam & Modern)</option>
                                                    <option value="gong" <?php echo ($pengaturan_data['bel_jenis_bunyi'] ?? '') === 'gong' ? 'selected' : ''; ?>>Gong / Singing Bell (Gema Berwibawa)</option>
                                                    <option value="tabung" <?php echo ($pengaturan_data['bel_jenis_bunyi'] ?? '') === 'tabung' ? 'selected' : ''; ?>>Lonceng Tabung / Tubular Chime (Merdu)</option>
                                                </select>
                                                <button type="button" class="btn btn-primary" onclick="testSelectedBellSound()" title="Dengarkan Contoh Bunyi Bel">
                                                    <i class="fas fa-play me-1"></i> Uji Bunyi
                                                </button>
                                            </div>
                                            <small class="text-muted">Pilih karakter nada bel untuk layar soal musabaqoh.</small>
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
    function formatRupiah(input) {
        let value = input.value.replace(/[^0-9]/g, '');
        let formatted = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        input.value = formatted;
    }

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
    // Web Audio API Synthesizer Preview untuk Admin Pengaturan Bel
    let previewAudioCtx = null;
    function getPreviewAudioCtx() {
        if (!previewAudioCtx) {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (AC) previewAudioCtx = new AC();
        }
        if (previewAudioCtx && previewAudioCtx.state === 'suspended') {
            previewAudioCtx.resume();
        }
        return previewAudioCtx;
    }

    function testSelectedBellSound() {
        const type = document.getElementById('bel_jenis_bunyi').value;
        const ctx = getPreviewAudioCtx();
        if (!ctx) return;
        const now = ctx.currentTime;

        if (type === 'digital') {
            // Bel Digital / Ting Elektronik
            const partials = [1760, 2640, 3520];
            const master = ctx.createGain();
            master.gain.setValueAtTime(0.85, now);
            master.connect(ctx.destination);

            partials.forEach((f, i) => {
                const osc = ctx.createOscillator();
                const g = ctx.createGain();
                osc.type = i === 0 ? 'sine' : 'triangle';
                osc.frequency.setValueAtTime(f, now);
                g.gain.setValueAtTime(0.001, now);
                g.gain.linearRampToValueAtTime(0.4 / (i + 1), now + 0.002);
                g.gain.exponentialRampToValueAtTime(0.0001, now + 0.55);
                osc.connect(g);
                g.connect(master);
                osc.start(now);
                osc.stop(now + 0.6);
            });
        } else if (type === 'gong') {
            // Gong / Singing Bell: frekuensi rendah, resonansi panjang
            const freqs = [440, 554.37, 659.25, 880];
            const master = ctx.createGain();
            master.gain.setValueAtTime(0.9, now);
            master.connect(ctx.destination);

            freqs.forEach((f, i) => {
                const osc = ctx.createOscillator();
                const g = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(f, now);
                g.gain.setValueAtTime(0.001, now);
                g.gain.linearRampToValueAtTime(0.5 / (i + 1), now + 0.008);
                g.gain.exponentialRampToValueAtTime(0.0001, now + 2.2);
                osc.connect(g);
                g.connect(master);
                osc.start(now);
                osc.stop(now + 2.3);
            });
        } else if (type === 'tabung') {
            // Lonceng Tabung / Tubular Chime
            const freqs = [1318.5, 1760, 2637];
            const master = ctx.createGain();
            master.gain.setValueAtTime(0.9, now);
            master.connect(ctx.destination);

            freqs.forEach((f, i) => {
                const osc = ctx.createOscillator();
                const g = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(f, now);
                g.gain.setValueAtTime(0.001, now);
                g.gain.linearRampToValueAtTime(0.45 / (i + 1), now + 0.005);
                g.gain.exponentialRampToValueAtTime(0.0001, now + 1.8);
                osc.connect(g);
                g.connect(master);
                osc.start(now);
                osc.stop(now + 1.9);
            });
        } else {
            // Default: Kuningan / Brass Bell MHQ
            const partials = [
                { f: 1046.5, g: 0.70, d: 1.6 },
                { f: 1568.0, g: 0.45, d: 1.3 },
                { f: 2093.0, g: 0.35, d: 1.0 },
                { f: 2637.0, g: 0.22, d: 0.8 },
                { f: 3136.0, g: 0.15, d: 0.6 }
            ];
            const master = ctx.createGain();
            master.gain.setValueAtTime(0.95, now);
            master.connect(ctx.destination);

            partials.forEach((p, idx) => {
                const osc = ctx.createOscillator();
                const g = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(p.f, now);
                osc.detune.setValueAtTime((idx - 2) * 5, now);
                g.gain.setValueAtTime(0.001, now);
                g.gain.linearRampToValueAtTime(p.g, now + 0.004);
                g.gain.exponentialRampToValueAtTime(0.0001, now + p.d);
                osc.connect(g);
                g.connect(master);
                osc.start(now);
                osc.stop(now + p.d + 0.05);
            });
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

