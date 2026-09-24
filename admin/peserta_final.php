<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Proses pilih peserta final
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] == 'pilih_final') {
        try {
            $pdo->beginTransaction();
            
            if (empty($_POST['peserta_id']) || !is_array($_POST['peserta_id'])) {
                throw new Exception("Pilih setidaknya satu peserta untuk masuk ke final!");
            }
            
            $selected_ids = $_POST['peserta_id'];
            
            // Hapus data peserta final yang sudah ada
            $pdo->exec("DELETE FROM peserta_final");
            $pdo->exec("DELETE FROM assignment_juri_final");
            $pdo->exec("DELETE FROM penilaian_final");
            
            // Ambil data skor peserta yang dipilih
            $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
            $query_selected = "
                SELECT 
                    p.id,
                    p.nama_lengkap,
                    p.nisn,
                    s.nama_sekolah,
                    (0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END), 0)
                     + 0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END), 0)
                     + 0.2 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END), 0)) as skor_terbobot
                FROM peserta p
                JOIN sekolah s ON p.sekolah_id = s.id
                LEFT JOIN penilaian pen ON p.id = pen.peserta_id
                LEFT JOIN juri j ON pen.juri_id = j.id
                WHERE p.id IN ($placeholders)
                GROUP BY p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
                ORDER BY skor_terbobot DESC
            ";
            
            $stmt_selected = $pdo->prepare($query_selected);
            $stmt_selected->execute($selected_ids);
            $peserta_selected = $stmt_selected->fetchAll();
            
            // Insert ke tabel peserta_final
            $stmt_insert = $pdo->prepare("
                INSERT INTO peserta_final (peserta_id, skor_penyisihan, peringkat_penyisihan) 
                VALUES (?, ?, ?)
            ");
            
            foreach ($peserta_selected as $index => $peserta) {
                $stmt_insert->execute([
                    $peserta['id'],
                    $peserta['skor_terbobot'],
                    $index + 1
                ]);
            }
            
            // Update status penyisihan dan final
            $pdo->exec("UPDATE pengaturan SET nilai = 'Selesai' WHERE nama_pengaturan = 'status_penyisihan'");
            $pdo->exec("UPDATE pengaturan SET nilai = 'Aktif' WHERE nama_pengaturan = 'status_final'");
            
            $pdo->commit();
            $success = "Berhasil memilih peserta final! Babak final telah dimulai.";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
    
    if ($_POST['action'] == 'reset_final') {
        try {
            $pdo->beginTransaction();
            
            // Hapus semua data final
            $pdo->exec("DELETE FROM peserta_final");
            $pdo->exec("DELETE FROM assignment_juri_final");
            $pdo->exec("DELETE FROM penilaian_final");
            
            // Reset status
            $pdo->exec("UPDATE pengaturan SET nilai = 'Aktif' WHERE nama_pengaturan = 'status_penyisihan'");
            $pdo->exec("UPDATE pengaturan SET nilai = 'Nonaktif' WHERE nama_pengaturan = 'status_final'");
            
            $pdo->commit();
            $success = "Data final berhasil direset! Kembali ke babak penyisihan.";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Ambil data peserta final
$query_peserta_final = "
    SELECT 
        pf.*,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah
    FROM peserta_final pf
    JOIN peserta p ON pf.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    ORDER BY pf.peringkat_penyisihan
";
$result_peserta_final = $pdo->query($query_peserta_final);
$peserta_final_list = $result_peserta_final->fetchAll();

// Ambil status sistem
$query_status = "
    SELECT nama_pengaturan, nilai 
    FROM pengaturan 
    WHERE nama_pengaturan IN ('status_penyisihan', 'status_final')
";
$result_status = $pdo->query($query_status);
$status_list = $result_status->fetchAll(PDO::FETCH_KEY_PAIR);

// Ambil data ranking penyisihan untuk preview
$query_ranking = "
    SELECT 
        p.id,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        (0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END), 0)
         + 0.4 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END), 0)
         + 0.2 * COALESCE(MAX(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END), 0)) as skor_terbobot
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id
    LEFT JOIN juri j ON pen.juri_id = j.id
    WHERE p.status = 'Diterima'
    GROUP BY p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
    ORDER BY skor_terbobot DESC
";
$result_ranking = $pdo->query($query_ranking);
$ranking_list = $result_ranking->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta Final - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-trophy me-2"></i>
                        Peserta Final
                    </h1>
                </div>

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

                <!-- Status Sistem -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Status Sistem
                                </h5>
                                <p class="mb-1">
                                    <strong>Penyisihan:</strong> 
                                    <span class="badge bg-<?php echo $status_list['status_penyisihan'] == 'Aktif' ? 'success' : 'secondary'; ?>">
                                        <?php echo $status_list['status_penyisihan']; ?>
                                    </span>
                                </p>
                                <p class="mb-0">
                                    <strong>Final:</strong> 
                                    <span class="badge bg-<?php echo $status_list['status_final'] == 'Aktif' ? 'success' : 'secondary'; ?>">
                                        <?php echo $status_list['status_final']; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Ranking Penyisihan -->
                <?php if (empty($peserta_final_list)): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Pilih Peserta untuk Babak Final
                                    </h5>
                                    <p class="card-text">Centang peserta yang ingin Anda masukkan ke babak final, kemudian klik tombol Simpan di bawah.</p>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="pilih_final">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover align-middle">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 50px;">Pilih</th>
                                                        <th>Peringkat</th>
                                                        <th>Nama Peserta</th>
                                                        <th>NISN</th>
                                                        <th>Sekolah</th>
                                                        <th>Skor Terbobot</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($ranking_list as $index => $peserta): ?>
                                                        <tr class="<?php echo $index < 6 ? 'table-success' : ''; ?>">
                                                            <td class="text-center">
                                                                <input class="form-check-input" type="checkbox" name="peserta_id[]" value="<?php echo $peserta['id']; ?>" <?php echo $index < 6 ? 'checked' : ''; ?> style="transform: scale(1.5);">
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?php echo $index < 6 ? 'success' : 'secondary'; ?>">
                                                                    <?php echo $index + 1; ?>
                                                                </span>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></td>
                                                            <td><?php echo htmlspecialchars($peserta['nisn']); ?></td>
                                                            <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                                            <td>
                                                                <strong><?php echo number_format($peserta['skor_terbobot'], 2); ?></strong>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Secara default, 6 peringkat teratas sudah dicentang otomatis. Anda bisa mengubahnya.
                                            </small>
                                            <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Yakin ingin menetapkan peserta tercentang untuk babak final?')">
                                                <i class="fas fa-trophy me-2"></i>
                                                Tetapkan Peserta Final
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Daftar Peserta Final -->
                <?php if (!empty($peserta_final_list)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-trophy me-2"></i>
                                        Daftar Peserta Final (6 Terbaik)
                                    </h5>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="reset_final">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin reset data final?')">
                                            <i class="fas fa-undo me-1"></i>
                                            Reset Final
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Peringkat</th>
                                                    <th>Nama Peserta</th>
                                                    <th>NISN</th>
                                                    <th>Sekolah</th>
                                                    <th>Skor Penyisihan</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($peserta_final_list as $peserta): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-warning text-dark">
                                                                <?php echo $peserta['peringkat_penyisihan']; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></td>
                                                        <td><?php echo htmlspecialchars($peserta['nisn']); ?></td>
                                                        <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                                        <td>
                                                            <strong><?php echo number_format($peserta['skor_penyisihan'], 2); ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $peserta['status'] == 'Aktif' ? 'success' : 'secondary'; ?>">
                                                                <?php echo $peserta['status']; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <?php if ($status_list['status_final'] == 'Aktif'): ?>
                                        <div class="mt-3">
                                            <a href="assignment_juri_final.php" class="btn btn-primary">
                                                <i class="fas fa-user-tie me-2"></i>
                                                Setup Assignment Juri Final
                                            </a>
                                            <a href="laporan_penilaian_final.php" class="btn btn-info">
                                                <i class="fas fa-chart-bar me-2"></i>
                                                Lihat Laporan Final
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

