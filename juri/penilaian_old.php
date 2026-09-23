<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai juri
if (!isset($_SESSION['juri_id'])) {
    header('Location: ../auth/login_juri.php');
    exit();
}

$juri_id = $_SESSION['juri_id'];
$peserta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$peserta_id) {
    header('Location: dashboard.php');
    exit();
}

// Cek assignment juri ke peserta ini
$query_assignment = "
    SELECT a.*, p.*, s.nama_sekolah
    FROM assignment_juri a
    JOIN peserta p ON a.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE a.juri_id = ? AND a.peserta_id = ? AND p.status = 'Diterima'
";
$stmt_assignment = $pdo->prepare($query_assignment);
$stmt_assignment->execute([$juri_id, $peserta_id]);
$assignment = $stmt_assignment->fetch();

if (!$assignment) {
    header('Location: dashboard.php');
    exit();
}

$peserta = $assignment;

// Ambil data juri untuk mengetahui spesialisasinya
$query_juri = "SELECT * FROM juri WHERE id = ?";
$stmt_juri = $pdo->prepare($query_juri);
$stmt_juri->execute([$juri_id]);
$juri_data = $stmt_juri->fetch();

// Cek apakah sudah pernah dinilai oleh juri ini
$query_cek = "SELECT * FROM penilaian WHERE peserta_id = ? AND juri_id = ?";
$stmt_cek = $pdo->prepare($query_cek);
$stmt_cek->execute([$peserta_id, $juri_id]);
$penilaian_existing = $stmt_cek->fetch();

$success = '';
$error = '';

if ($_POST) {
    $skor_tajwid = (float)$_POST['skor_tajwid'];
    $skor_fluency = (float)$_POST['skor_fluency'];
    $skor_makhraj = (float)$_POST['skor_makhraj'];
    $catatan = $_POST['catatan'];
    
    // Hitung skor keseluruhan otomatis dari rata-rata 3 aspek
    $skor_keseluruhan = round(($skor_tajwid + $skor_fluency + $skor_makhraj) / 3, 2);
    
    // Validasi skor (0-100)
    if ($skor_tajwid < 0 || $skor_tajwid > 100 || 
        $skor_fluency < 0 || $skor_fluency > 100 || 
        $skor_makhraj < 0 || $skor_makhraj > 100) {
        $error = 'Skor harus berada dalam rentang 0-100!';
    } else {
        if ($penilaian_existing) {
            // Update penilaian yang sudah ada
            $query_update = "
                UPDATE penilaian 
                SET skor_tajwid = ?, skor_fluency = ?, skor_makhraj = ?, 
                    skor_keseluruhan = ?, catatan = ?, tanggal_penilaian = NOW()
                WHERE peserta_id = ? AND juri_id = ?
            ";
            $stmt_update = $pdo->prepare($query_update);
            
            if ($stmt_update->execute([$skor_tajwid, $skor_fluency, $skor_makhraj, 
                                     $skor_keseluruhan, $catatan, $peserta_id, $juri_id])) {
                $success = 'Penilaian berhasil diperbarui!';
                $penilaian_existing = array_merge($penilaian_existing, [
                    'skor_tajwid' => $skor_tajwid,
                    'skor_fluency' => $skor_fluency,
                    'skor_makhraj' => $skor_makhraj,
                    'skor_keseluruhan' => $skor_keseluruhan,
                    'catatan' => $catatan
                ]);
            } else {
                $error = 'Gagal memperbarui penilaian!';
            }
        } else {
            // Insert penilaian baru
            $query_insert = "
                INSERT INTO penilaian (peserta_id, juri_id, skor_tajwid, skor_fluency, 
                                     skor_makhraj, skor_keseluruhan, catatan)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
            $stmt_insert = $pdo->prepare($query_insert);
            
            if ($stmt_insert->execute([$peserta_id, $juri_id, $skor_tajwid, 
                                     $skor_fluency, $skor_makhraj, $skor_keseluruhan, $catatan])) {
                $success = 'Penilaian berhasil disimpan!';
                $penilaian_existing = [
                    'skor_tajwid' => $skor_tajwid,
                    'skor_fluency' => $skor_fluency,
                    'skor_makhraj' => $skor_makhraj,
                    'skor_keseluruhan' => $skor_keseluruhan,
                    'catatan' => $catatan
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
    <title>Form Penilaian - Lomba Tahfidz</title>
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
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .score-input {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
        }
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-arrow-left me-2"></i>
                Form Penilaian
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Info Peserta -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">
                            <i class="fas fa-user-graduate me-2"></i>
                            Data Peserta
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($peserta['nama_lengkap']); ?></p>
                                <p><strong>NISN:</strong> <?php echo htmlspecialchars($peserta['nisn']); ?></p>
                                <p><strong>Kelas:</strong> <?php echo htmlspecialchars($peserta['kelas']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Sekolah:</strong> <?php echo htmlspecialchars($peserta['nama_sekolah']); ?></p>
                                <p><strong>Jenis Kelamin:</strong> <?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                                <p><strong>Tanggal Lahir:</strong> <?php echo date('d/m/Y', strtotime($peserta['tanggal_lahir'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Penilaian -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Form Penilaian
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="skor_tajwid" class="form-label">
                                        <i class="fas fa-book-quran me-2"></i>
                                        Skor Tajwid (0-100)
                                    </label>
                                    <input type="number" class="form-control score-input" id="skor_tajwid" 
                                           name="skor_tajwid" min="0" max="100" step="0.01" 
                                           value="<?php echo $penilaian_existing ? $penilaian_existing['skor_tajwid'] : ''; ?>" required>
                                    <div class="form-text">Penilaian ketepatan hukum tajwid</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="skor_fluency" class="form-label">
                                        <i class="fas fa-microphone me-2"></i>
                                        Skor Fluency (0-100)
                                    </label>
                                    <input type="number" class="form-control score-input" id="skor_fluency" 
                                           name="skor_fluency" min="0" max="100" step="0.01" 
                                           value="<?php echo $penilaian_existing ? $penilaian_existing['skor_fluency'] : ''; ?>" required>
                                    <div class="form-text">Penilaian kelancaran membaca</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="skor_makhraj" class="form-label">
                                        <i class="fas fa-volume-up me-2"></i>
                                        Skor Makhraj (0-100)
                                    </label>
                                    <input type="number" class="form-control score-input" id="skor_makhraj" 
                                           name="skor_makhraj" min="0" max="100" step="0.01" 
                                           value="<?php echo $penilaian_existing ? $penilaian_existing['skor_makhraj'] : ''; ?>" required>
                                    <div class="form-text">Penilaian ketepatan makhraj huruf</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="skor_keseluruhan" class="form-label">
                                        <i class="fas fa-star me-2"></i>
                                        Skor Keseluruhan (Otomatis)
                                    </label>
                                    <input type="number" class="form-control score-input" id="skor_keseluruhan" 
                                           name="skor_keseluruhan" min="0" max="100" step="0.01" 
                                           value="<?php echo $penilaian_existing ? $penilaian_existing['skor_keseluruhan'] : ''; ?>" 
                                           readonly style="background-color: #f8f9fa;">
                                    <div class="form-text">Dihitung otomatis dari rata-rata 3 aspek penilaian</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="catatan" class="form-label">
                                    <i class="fas fa-comment-alt me-2"></i>
                                    Catatan Penilaian
                                </label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="4" 
                                          placeholder="Masukkan catatan atau feedback untuk peserta..."><?php echo $penilaian_existing ? htmlspecialchars($penilaian_existing['catatan']) : ''; ?></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="dashboard.php" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $penilaian_existing ? 'Perbarui Penilaian' : 'Simpan Penilaian'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panduan Penilaian -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Panduan Penilaian
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-book-quran me-2"></i>Tajwid (0-100)</h6>
                                <ul class="small">
                                    <li>90-100: Sangat baik, tidak ada kesalahan tajwid</li>
                                    <li>80-89: Baik, ada 1-2 kesalahan minor</li>
                                    <li>70-79: Cukup, ada beberapa kesalahan</li>
                                    <li>60-69: Kurang, banyak kesalahan tajwid</li>
                                    <li>0-59: Sangat kurang, kesalahan tajwid fatal</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-microphone me-2"></i>Fluency (0-100)</h6>
                                <ul class="small">
                                    <li>90-100: Sangat lancar, tempo konsisten</li>
                                    <li>80-89: Lancar, sedikit terputus</li>
                                    <li>70-79: Cukup lancar, ada jeda</li>
                                    <li>60-69: Kurang lancar, sering terputus</li>
                                    <li>0-59: Tidak lancar, banyak terputus</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6><i class="fas fa-volume-up me-2"></i>Makhraj (0-100)</h6>
                                <ul class="small">
                                    <li>90-100: Makhraj sangat tepat</li>
                                    <li>80-89: Makhraj baik, sedikit kurang</li>
                                    <li>70-79: Makhraj cukup</li>
                                    <li>60-69: Makhraj kurang tepat</li>
                                    <li>0-59: Makhraj salah</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-star me-2"></i>Keseluruhan (Otomatis)</h6>
                                <ul class="small">
                                    <li><strong>Dihitung otomatis</strong> dari rata-rata 3 aspek</li>
                                    <li>90-100: Performa sangat baik</li>
                                    <li>80-89: Performa baik</li>
                                    <li>70-79: Performa cukup</li>
                                    <li>60-69: Performa kurang</li>
                                    <li>0-59: Performa sangat kurang</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto calculate average score
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = ['skor_tajwid', 'skor_fluency', 'skor_makhraj'];
            const keseluruhanInput = document.getElementById('skor_keseluruhan');
            
            // Tambahkan event listener untuk setiap input
            inputs.forEach(function(inputId) {
                const input = document.getElementById(inputId);
                input.addEventListener('input', calculateAverage);
                input.addEventListener('change', calculateAverage);
            });
            
            // Hitung rata-rata otomatis
            function calculateAverage() {
                let total = 0;
                let count = 0;
                
                inputs.forEach(function(inputId) {
                    const value = parseFloat(document.getElementById(inputId).value);
                    if (!isNaN(value) && value >= 0 && value <= 100) {
                        total += value;
                        count++;
                    }
                });
                
                if (count === 3) {
                    // Hitung rata-rata dan bulatkan ke 2 desimal (sama dengan backend)
                    const average = Math.round((total / count) * 100) / 100;
                    keseluruhanInput.value = average.toFixed(2);
                } else {
                    keseluruhanInput.value = '';
                }
            }
            
            // Hitung ulang saat halaman dimuat jika ada nilai
            calculateAverage();
        });
    </script>
</body>
</html>
