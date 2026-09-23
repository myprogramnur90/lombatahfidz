<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai juri
if (!isset($_SESSION['juri_id'])) {
    header('Location: ../auth/login_juri.php');
    exit();
}

$juri_id = $_SESSION['juri_id'];
$peserta_id = $_GET['id'] ?? null;

if (!$peserta_id) {
    header('Location: dashboard.php');
    exit();
}

// Ambil peserta yang diterima (tanpa bergantung pada assignment)
$query_peserta = "
    SELECT p.*, s.nama_sekolah
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE p.id = ? AND p.status = 'Diterima'
";
$stmt_peserta = $pdo->prepare($query_peserta);
$stmt_peserta->execute([$peserta_id]);
$peserta = $stmt_peserta->fetch();

if (!$peserta) {
    header('Location: dashboard.php');
    exit();
}

// Ambil data juri untuk mengetahui spesialisasinya
$query_juri = "SELECT * FROM juri WHERE id = ?";
$stmt_juri = $pdo->prepare($query_juri);
$stmt_juri->execute([$juri_id]);
$juri_data = $stmt_juri->fetch();

// Mapping tampilan spesialisasi
$spes_display = $juri_data['spesialisasi'] == 'Kelancaran Hafalan' ? 'Kelancaran (Tahfidz)' : (
    $juri_data['spesialisasi'] == 'Makhraj' ? 'Makhraj Tajwid' : (
    $juri_data['spesialisasi'] == 'Tajwid dan Adab' ? 'Shifatul Huruf' : $juri_data['spesialisasi']
));

// Cek apakah sudah pernah dinilai oleh juri ini
$query_cek = "SELECT * FROM penilaian WHERE peserta_id = ? AND juri_id = ?";
$stmt_cek = $pdo->prepare($query_cek);
$stmt_cek->execute([$peserta_id, $juri_id]);
$penilaian_existing = $stmt_cek->fetch();

$success = '';
$error = '';

if ($_POST) {
    $skor_spesialisasi = (float)$_POST['skor_spesialisasi'];
    $catatan = $_POST['catatan'];
    
    // Validasi skor (0-100)
    if ($skor_spesialisasi < 0 || $skor_spesialisasi > 100) {
        $error = 'Skor harus berada dalam rentang 0-100!';
    } else {
        if ($penilaian_existing) {
            // Update penilaian yang sudah ada
            $query_update = "
                UPDATE penilaian 
                SET skor_spesialisasi = ?, catatan = ?, tanggal_penilaian = NOW()
                WHERE peserta_id = ? AND juri_id = ?
            ";
            $stmt_update = $pdo->prepare($query_update);
            
            if ($stmt_update->execute([$skor_spesialisasi, $catatan, $peserta_id, $juri_id])) {
                $success = 'Penilaian berhasil diperbarui!';
                $penilaian_existing = array_merge($penilaian_existing, [
                    'skor_spesialisasi' => $skor_spesialisasi,
                    'catatan' => $catatan,
                    'tanggal_penilaian' => date('Y-m-d H:i:s')
                ]);
            } else {
                $error = 'Gagal memperbarui penilaian!';
            }
        } else {
            // Insert penilaian baru
            $query_insert = "
                INSERT INTO penilaian (peserta_id, juri_id, skor_spesialisasi, catatan)
                VALUES (?, ?, ?, ?)
            ";
            $stmt_insert = $pdo->prepare($query_insert);
            
            if ($stmt_insert->execute([$peserta_id, $juri_id, $skor_spesialisasi, $catatan])) {
                $success = 'Penilaian berhasil disimpan!';
                $penilaian_existing = [
                    'skor_spesialisasi' => $skor_spesialisasi,
                    'catatan' => $catatan,
                    'tanggal_penilaian' => date('Y-m-d H:i:s')
                ];
            } else {
                $error = 'Gagal menyimpan penilaian!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian <?php echo htmlspecialchars($spes_display); ?> - Juri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .spesialisasi-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .panduan-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-gavel me-2"></i>
                Penilaian <?php echo htmlspecialchars($spes_display); ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title mb-0">
                                    <i class="fas fa-user-graduate me-2"></i>
                                    Penilaian Peserta
                                </h2>
                                <p class="text-muted mb-0">Spesialisasi: <span class="spesialisasi-badge"><?php echo htmlspecialchars($spes_display); ?></span></p>
                            </div>
                            <div class="text-end">
                                <h5 class="mb-1"><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></p>
                                <small class="text-muted">NISN: <?php echo htmlspecialchars($peserta['nisn']); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panduan Penilaian -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="panduan-box">
                    <h5><i class="fas fa-info-circle me-2"></i>Panduan Penilaian <?php echo htmlspecialchars($spes_display); ?></h5>
                    <?php if ($juri_data['spesialisasi'] == 'Makhraj'): ?>
                        <p class="mb-0">
                            <strong>Penilaian Makhraj Tajwid (Pengucapan Huruf):</strong><br>
                            • 90-100: Pengucapan huruf sangat tepat, jelas, dan konsisten<br>
                            • 80-89: Pengucapan huruf tepat dengan sedikit kesalahan<br>
                            • 70-79: Pengucapan huruf cukup tepat dengan beberapa kesalahan<br>
                            • 60-69: Pengucapan huruf kurang tepat dengan banyak kesalahan<br>
                            • 0-59: Pengucapan huruf tidak tepat dan tidak jelas
                        </p>
                    <?php elseif ($juri_data['spesialisasi'] == 'Kelancaran Hafalan'): ?>
                        <p class="mb-0">
                            <strong>Penilaian Kelancaran (Tahfidz):</strong><br>
                            • 90-100: Hafalan sangat lancar, tidak ada jeda atau kesalahan<br>
                            • 80-89: Hafalan lancar dengan sedikit jeda atau kesalahan<br>
                            • 70-79: Hafalan cukup lancar dengan beberapa jeda<br>
                            • 60-69: Hafalan kurang lancar dengan banyak jeda<br>
                            • 0-59: Hafalan tidak lancar dan banyak kesalahan
                        </p>
                    <?php elseif ($juri_data['spesialisasi'] == 'Tajwid dan Adab'): ?>
                        
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Penilaian -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Form Penilaian
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-4">
                                <label for="skor_spesialisasi" class="form-label">
                                    <strong>Skor <?php echo htmlspecialchars($spes_display); ?> (0-100)</strong>
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg" 
                                       id="skor_spesialisasi" 
                                       name="skor_spesialisasi" 
                                       min="0" 
                                       max="100" 
                                       step="0.1"
                                       value="<?php echo $penilaian_existing ? $penilaian_existing['skor_spesialisasi'] : ''; ?>"
                                       required>
                                <div class="form-text">
                                    Masukkan skor dari 0 sampai 100 untuk aspek <?php echo htmlspecialchars($spes_display); ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="catatan" class="form-label">
                                    <strong>Catatan Penilaian</strong>
                                </label>
                                <textarea class="form-control" 
                                          id="catatan" 
                                          name="catatan" 
                                          rows="4" 
                                          placeholder="Berikan catatan atau feedback untuk peserta..."><?php echo $penilaian_existing ? htmlspecialchars($penilaian_existing['catatan']) : ''; ?></textarea>
                                <div class="form-text">
                                    Catatan ini akan membantu peserta untuk memperbaiki performa mereka
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="dashboard.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $penilaian_existing ? 'Update Penilaian' : 'Simpan Penilaian'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Penilaian
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Juri:</strong><br>
                            <?php echo htmlspecialchars($juri_data['nama_lengkap']); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Spesialisasi:</strong><br>
                            <span class="spesialisasi-badge"><?php echo htmlspecialchars($spes_display); ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Peserta:</strong><br>
                            <?php echo htmlspecialchars($peserta['nama_lengkap']); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Sekolah:</strong><br>
                            <?php echo htmlspecialchars($peserta['nama_sekolah']); ?>
                        </div>
                        <?php if ($penilaian_existing): ?>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-success">Sudah Dinilai</span>
                            </div>
                            <div class="mb-3">
                                <strong>Skor:</strong><br>
                                <span class="h4 text-primary"><?php echo $penilaian_existing['skor_spesialisasi']; ?></span>
                            </div>
                            <div class="mb-3">
                                <strong>Tanggal Penilaian:</strong><br>
                                <?php 
                                if (isset($penilaian_existing['tanggal_penilaian']) && !empty($penilaian_existing['tanggal_penilaian'])) {
                                    echo date('d/m/Y H:i', strtotime($penilaian_existing['tanggal_penilaian']));
                                } else {
                                    echo 'Belum tersedia';
                                }
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-warning">Belum Dinilai</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time validation
        document.getElementById('skor_spesialisasi').addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) this.value = 0;
            if (value > 100) this.value = 100;
        });
    </script>
</body>
</html>
