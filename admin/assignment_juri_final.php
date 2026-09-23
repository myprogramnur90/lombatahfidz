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

// Proses assignment juri
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] == 'assign') {
        try {
            $pdo->beginTransaction();
            
            // Hapus assignment yang sudah ada
            $pdo->exec("DELETE FROM assignment_juri_final");
            
            // Ambil data juri dan peserta final
            $juri_list = $pdo->query("SELECT * FROM juri WHERE status = 'Aktif' ORDER BY id")->fetchAll();
            $peserta_final_list = $pdo->query("SELECT * FROM peserta_final WHERE status = 'Aktif' ORDER BY peringkat_penyisihan")->fetchAll();
            
            if (count($juri_list) == 0) {
                throw new Exception("Tidak ada juri yang aktif! Silakan tambahkan atau aktifkan juri terlebih dahulu.");
            }
            
            // Assignment semua juri ke semua peserta final
            $stmt_assign = $pdo->prepare("
                INSERT INTO assignment_juri_final (juri_id, peserta_final_id) 
                VALUES (?, ?)
            ");
            
            foreach ($juri_list as $juri) {
                foreach ($peserta_final_list as $peserta_final) {
                    $stmt_assign->execute([
                        $juri['id'],
                        $peserta_final['id']
                    ]);
                }
            }
            
            $pdo->commit();
            $success = "Assignment juri berhasil dibuat! " . count($juri_list) . " juri telah di-assign ke " . count($peserta_final_list) . " peserta final.";
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
    
    if ($_POST['action'] == 'reset') {
        try {
            $pdo->exec("DELETE FROM assignment_juri_final");
            $pdo->exec("DELETE FROM penilaian_final");
            $success = "Assignment berhasil direset!";
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Ambil data assignment
$query_assignment = "
    SELECT 
        ajf.*,
        j.nama_lengkap as nama_juri,
        j.spesialisasi,
        pf.peringkat_penyisihan,
        p.nama_lengkap as nama_peserta,
        p.nisn,
        s.nama_sekolah
    FROM assignment_juri_final ajf
    JOIN juri j ON ajf.juri_id = j.id
    JOIN peserta_final pf ON ajf.peserta_final_id = pf.id
    JOIN peserta p ON pf.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    ORDER BY pf.peringkat_penyisihan
";
$result_assignment = $pdo->query($query_assignment);
$assignment_list = $result_assignment->fetchAll();

// Ambil data juri dan peserta final yang belum di-assign
$query_juri = "SELECT * FROM juri WHERE status = 'Aktif' ORDER BY id";
$result_juri = $pdo->query($query_juri);
$juri_list = $result_juri->fetchAll();

$query_peserta_final = "
    SELECT 
        pf.*,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah
    FROM peserta_final pf
    JOIN peserta p ON pf.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE pf.status = 'Aktif'
    ORDER BY pf.peringkat_penyisihan
";
$result_peserta_final = $pdo->query($query_peserta_final);
$peserta_final_list = $result_peserta_final->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Juri Final - Admin</title>
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
                        <i class="fas fa-user-tie me-2"></i>
                        Assignment Juri Final
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

                <!-- Info Assignment -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Info Assignment
                                </h5>
                                <p class="mb-1"><strong>Juri Tersedia:</strong> <?php echo count($juri_list); ?></p>
                                <p class="mb-1"><strong>Peserta Final:</strong> <?php echo count($peserta_final_list); ?></p>
                                <p class="mb-0"><strong>Assignment:</strong> <?php echo count($assignment_list); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aksi Assignment -->
                <?php if (empty($assignment_list) && !empty($peserta_final_list)): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-cog me-2"></i>
                                        Setup Assignment
                                    </h5>
                                    <p class="card-text">
                                        Sistem akan melakukan assignment otomatis 1:1 (1 juri = 1 peserta final).
                                        Assignment akan dilakukan berdasarkan urutan peringkat penyisihan.
                                    </p>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="assign">
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin membuat assignment juri?')">
                                            <i class="fas fa-user-tie me-2"></i>
                                            Buat Assignment Juri
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Daftar Assignment -->
                <?php if (!empty($assignment_list)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Daftar Assignment Juri Final
                                    </h5>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="reset">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin reset assignment?')">
                                            <i class="fas fa-undo me-1"></i>
                                            Reset Assignment
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Juri</th>
                                                    <th>Spesialisasi</th>
                                                    <th>Peserta Final</th>
                                                    <th>Peringkat</th>
                                                    <th>Sekolah</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($assignment_list as $index => $assignment): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($assignment['nama_juri']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?php echo htmlspecialchars($assignment['spesialisasi']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($assignment['nama_peserta']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">NISN: <?php echo htmlspecialchars($assignment['nisn']); ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning text-dark">
                                                                #<?php echo $assignment['peringkat_penyisihan']; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($assignment['nama_sekolah']); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $assignment['status'] == 'Completed' ? 'success' : 'warning'; ?>">
                                                                <?php echo $assignment['status']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="../juri/penilaian_final.php?peserta_id=<?php echo $assignment['peserta_final_id']; ?>" 
                                                               class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="fas fa-edit me-1"></i>
                                                                Penilaian
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="laporan_penilaian_final.php" class="btn btn-info">
                                            <i class="fas fa-chart-bar me-2"></i>
                                            Lihat Laporan Final
                                        </a>
                                        <a href="peserta_final.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Kembali ke Peserta Final
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Daftar Juri Tersedia -->
                <?php if (!empty($juri_list)): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-users me-2"></i>
                                        Daftar Juri Tersedia
                                    </h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Juri</th>
                                                    <th>Spesialisasi</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($juri_list as $index => $juri): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo htmlspecialchars($juri['nama_lengkap']); ?></td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?php echo htmlspecialchars($juri['spesialisasi']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                <?php echo $juri['status']; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
