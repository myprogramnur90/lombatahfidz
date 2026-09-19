<?php
session_start();
require_once '../config/database.php';

// Cek login juri
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

// Ambil spesialisasi juri
$query_juri = "SELECT spesialisasi FROM juri WHERE id = ?";
$stmt_juri = $pdo->prepare($query_juri);
$stmt_juri->execute([$juri_id]);
$juri_data = $stmt_juri->fetch();
$spesialisasi = $juri_data['spesialisasi'];

// Sesuaikan tampilan spesialisasi
$spes_display = $spesialisasi;
if ($spesialisasi == 'Kelancaran Hafalan') $spes_display = 'Kelancaran (Tahfidz)';
else if ($spesialisasi == 'Makhraj') $spes_display = 'Makhraj Tajwid';
else if ($spesialisasi == 'Tajwid dan Adab') $spes_display = 'Shifatul Huruf';

// Cek apakah juri di-assign ke peserta final ini
$query_cek_assignment = "
    SELECT 
        ajf.*,
        pf.peserta_id,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        pf.skor_penyisihan,
        pf.peringkat_penyisihan
    FROM assignment_juri_final ajf
    JOIN peserta_final pf ON ajf.peserta_final_id = pf.id
    JOIN peserta p ON pf.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE ajf.juri_id = ? AND pf.peserta_id = ?
";
$stmt_cek = $pdo->prepare($query_cek_assignment);
$stmt_cek->execute([$juri_id, $peserta_id]);
$assignment = $stmt_cek->fetch();

if (!$assignment) {
    header('Location: dashboard.php');
    exit();
}

// Cek penilaian yang sudah ada
$query_penilaian = "
    SELECT * FROM penilaian_final 
    WHERE peserta_final_id = ? AND juri_id = ?
";
$stmt_penilaian = $pdo->prepare($query_penilaian);
$stmt_penilaian->execute([$assignment['peserta_final_id'], $juri_id]);
$penilaian_existing = $stmt_penilaian->fetch();

$success = '';
$error = '';

if ($_POST) {
    $skor_spesialisasi = (float)$_POST['skor_spesialisasi'];
    $catatan = $_POST['catatan'];
    
    // Validasi skor (0-100)
    if ($skor_spesialisasi < 0 || $skor_spesialisasi > 100) {
        $error = 'Skor harus berada dalam rentang 0-100!';
    } else {
        try {
            if ($penilaian_existing) {
                // Update penilaian yang sudah ada
                $query_update = "
                    UPDATE penilaian_final 
                    SET skor_spesialisasi = ?, catatan = ?, tanggal_penilaian = NOW()
                    WHERE peserta_final_id = ? AND juri_id = ?
                ";
                $stmt_update = $pdo->prepare($query_update);
                
                if ($stmt_update->execute([$skor_spesialisasi, $catatan, $assignment['peserta_final_id'], $juri_id])) {
                    $success = 'Penilaian final berhasil diperbarui!';
                    $penilaian_existing = array_merge($penilaian_existing, [
                        'skor_spesialisasi' => $skor_spesialisasi,
                        'catatan' => $catatan
                    ]);
                    
                    // Update status assignment
                    $pdo->exec("UPDATE assignment_juri_final SET status = 'Completed' WHERE juri_id = $juri_id AND peserta_final_id = " . $assignment['peserta_final_id']);
                } else {
                    $error = 'Gagal memperbarui penilaian!';
                }
            } else {
                // Insert penilaian baru
                $query_insert = "
                    INSERT INTO penilaian_final (peserta_final_id, juri_id, skor_spesialisasi, catatan)
                    VALUES (?, ?, ?, ?)
                ";
                $stmt_insert = $pdo->prepare($query_insert);
                
                if ($stmt_insert->execute([$assignment['peserta_final_id'], $juri_id, $skor_spesialisasi, $catatan])) {
                    $success = 'Penilaian final berhasil disimpan!';
                    $penilaian_existing = [
                        'skor_spesialisasi' => $skor_spesialisasi,
                        'catatan' => $catatan
                    ];
                    
                    // Update status assignment
                    $pdo->exec("UPDATE assignment_juri_final SET status = 'Completed' WHERE juri_id = $juri_id AND peserta_final_id = " . $assignment['peserta_final_id']);
                } else {
                    $error = 'Gagal menyimpan penilaian!';
                }
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Final - Juri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .skor-input {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .skor-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-trophy me-2"></i>
                Penilaian Final <?php echo htmlspecialchars($spes_display); ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
                <a class="nav-link" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Info Peserta Final -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            Peserta Final - Peringkat #<?php echo $assignment['peringkat_penyisihan']; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Nama:</strong> <?php echo htmlspecialchars($assignment['nama_lengkap']); ?></p>
                                <p class="mb-1"><strong>NISN:</strong> <?php echo htmlspecialchars($assignment['nisn']); ?></p>
                                <p class="mb-0"><strong>Sekolah:</strong> <?php echo htmlspecialchars($assignment['nama_sekolah']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Skor Penyisihan:</strong> 
                                    <span class="badge bg-success"><?php echo number_format($assignment['skor_penyisihan'], 2); ?></span>
                                </p>
                                <p class="mb-0"><strong>Status Assignment:</strong> 
                                    <span class="badge bg-<?php echo $assignment['status'] == 'Completed' ? 'success' : 'warning'; ?>">
                                        <?php echo $assignment['status']; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Penilaian -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Form Penilaian Final
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-primary fw-bold">
                                        <i class="fas fa-star me-2"></i>Bidang Penilaian
                                    </label>
                                    <div class="form-control bg-light">
                                        <span class="spesialisasi-badge">
                                            <?php echo htmlspecialchars($spes_display); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="skor_spesialisasi" class="form-label text-primary fw-bold">
                                        <i class="fas fa-sort-numeric-up-alt me-2"></i>Nilai (0-100)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg skor-input" 
                                           id="skor_spesialisasi" 
                                           name="skor_spesialisasi" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           value="<?php echo $penilaian_existing ? $penilaian_existing['skor_spesialisasi'] : ''; ?>"
                                           placeholder="Masukkan nilai"
                                           required>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="mb-3">
                                <label for="catatan" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Catatan Penilaian
                                </label>
                                <textarea class="form-control" 
                                          id="catatan" 
                                          name="catatan" 
                                          rows="4" 
                                          placeholder="Masukkan catatan penilaian..."><?php echo $penilaian_existing ? htmlspecialchars($penilaian_existing['catatan']) : ''; ?></textarea>
                            </div>

                            <!-- Tombol Submit -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="dashboard.php" class="btn btn-secondary me-md-2 btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $penilaian_existing ? 'Update Penilaian' : 'Simpan Penilaian'; ?>
                                </button>
                            </div>
                        </form>
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
