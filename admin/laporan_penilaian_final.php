<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Ambil data laporan penilaian final
$query_laporan = "
    SELECT 
        pf.id as peserta_final_id,
        pf.peserta_id,
        pf.peringkat_penyisihan,
        pf.skor_penyisihan,
        p.nama_lengkap,
        p.nisn,
        s.nama_sekolah,
        penf.skor_spesialisasi,
        penf.catatan,
        penf.tanggal_penilaian,
        j.nama_lengkap as nama_juri,
        j.spesialisasi
    FROM peserta_final pf
    JOIN peserta p ON pf.peserta_id = p.id
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian_final penf ON pf.id = penf.peserta_final_id
    LEFT JOIN juri j ON penf.juri_id = j.id
    WHERE pf.status = 'Aktif'
    ORDER BY penf.skor_spesialisasi DESC, pf.peringkat_penyisihan
";
$result_laporan = $pdo->query($query_laporan);
$laporan_list = $result_laporan->fetchAll();

// Ambil statistik penilaian final
$query_stats = "
    SELECT 
        COUNT(DISTINCT pf.id) as total_peserta_final,
        COUNT(penf.id) as total_penilaian,
        AVG(penf.skor_spesialisasi) as rata_rata_skor,
        MAX(penf.skor_spesialisasi) as skor_tertinggi,
        MIN(penf.skor_spesialisasi) as skor_terendah
    FROM peserta_final pf
    LEFT JOIN penilaian_final penf ON pf.id = penf.peserta_final_id
    WHERE pf.status = 'Aktif'
";
$result_stats = $pdo->query($query_stats);
$stats = $result_stats->fetch();

// Ambil status sistem
$query_status = "
    SELECT nama_pengaturan, nilai 
    FROM pengaturan 
    WHERE nama_pengaturan IN ('status_penyisihan', 'status_final')
";
$result_status = $pdo->query($query_status);
$status_list = $result_status->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penilaian Final - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .skor-high { color: #198754; font-weight: bold; }
        .skor-medium { color: #fd7e14; font-weight: bold; }
        .skor-low { color: #dc3545; font-weight: bold; }
        .ranking-1 { background: linear-gradient(45deg, #ffd700, #ffed4e); }
        .ranking-2 { background: linear-gradient(45deg, #c0c0c0, #e8e8e8); }
        .ranking-3 { background: linear-gradient(45deg, #cd7f32, #daa520); }
    </style>
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
                        Laporan Penilaian Final
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>
                            Print
                        </button>
                        <a href="export_penilaian_final.php" class="btn btn-outline-success">
                            <i class="fas fa-file-excel me-2"></i>
                            Export Excel
                        </a>
                    </div>
                </div>

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

                <!-- Statistik -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h5 class="card-title"><?php echo $stats['total_peserta_final']; ?></h5>
                                <p class="card-text">Peserta Final</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-edit fa-2x text-success mb-2"></i>
                                <h5 class="card-title"><?php echo $stats['total_penilaian']; ?></h5>
                                <p class="card-text">Penilaian Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                <h5 class="card-title"><?php echo number_format($stats['rata_rata_skor'], 2); ?></h5>
                                <p class="card-text">Rata-rata Skor</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-star fa-2x text-warning mb-2"></i>
                                <h5 class="card-title"><?php echo number_format($stats['skor_tertinggi'], 2); ?></h5>
                                <p class="card-text">Skor Tertinggi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Laporan -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    Daftar Hasil Penilaian Final
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($laporan_list)): ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada data penilaian final</h5>
                                        <p class="text-muted">Peserta final belum dinilai atau belum ada peserta final yang dipilih.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Peringkat Final</th>
                                                    <th>Nama Peserta</th>
                                                    <th>NISN</th>
                                                    <th>Sekolah</th>
                                                    <th>Skor Penyisihan</th>
                                                    <th>Juri</th>
                                                    <th>Skor Final</th>
                                                    <th>Tanggal</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $peringkat_final = 1;
                                                foreach ($laporan_list as $index => $laporan): 
                                                    if ($index > 0 && $laporan['skor_spesialisasi'] != $laporan_list[$index-1]['skor_spesialisasi']) {
                                                        $peringkat_final = $index + 1;
                                                    }
                                                ?>
                                                    <tr class="<?php echo $peringkat_final <= 3 ? 'ranking-' . $peringkat_final : ''; ?>">
                                                        <td>
                                                            <span class="badge bg-<?php echo $peringkat_final <= 3 ? 'warning' : 'secondary'; ?> text-dark">
                                                                #<?php echo $peringkat_final; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($laporan['nama_lengkap']); ?></strong>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($laporan['nisn']); ?></td>
                                                        <td><?php echo htmlspecialchars($laporan['nama_sekolah']); ?></td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?php echo number_format($laporan['skor_penyisihan'], 2); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($laporan['nama_juri']): ?>
                                                                <small>
                                                                    <?php echo htmlspecialchars($laporan['nama_juri']); ?><br>
                                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($laporan['spesialisasi']); ?></span>
                                                                </small>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong class="<?php echo $laporan['skor_spesialisasi'] >= 80 ? 'skor-high' : ($laporan['skor_spesialisasi'] >= 60 ? 'skor-medium' : 'skor-low'); ?>">
                                                                <?php echo $laporan['skor_spesialisasi'] ? number_format($laporan['skor_spesialisasi'], 2) : '-'; ?>
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <?php echo $laporan['tanggal_penilaian'] ? date('d/m/Y H:i', strtotime($laporan['tanggal_penilaian'])) : '-'; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($laporan['skor_spesialisasi']): ?>
                                                                <button class="btn btn-sm btn-outline-info" 
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#detailModal<?php echo $laporan['peserta_final_id']; ?>">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Detail -->
                                                    <?php if ($laporan['skor_spesialisasi']): ?>
                                                        <div class="modal fade" id="detailModal<?php echo $laporan['peserta_final_id']; ?>" tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">
                                                                            <i class="fas fa-user-graduate me-2"></i>
                                                                            Detail Penilaian Final
                                                                        </h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row mb-3">
                                                                            <div class="col-6">
                                                                                <strong>Nama:</strong><br>
                                                                                <?php echo htmlspecialchars($laporan['nama_lengkap']); ?>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <strong>NISN:</strong><br>
                                                                                <?php echo htmlspecialchars($laporan['nisn']); ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <div class="col-12">
                                                                                <strong>Sekolah:</strong><br>
                                                                                <?php echo htmlspecialchars($laporan['nama_sekolah']); ?>
                                                                            </div>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="row mb-3">
                                                                            <div class="col-12 text-center">
                                                                                <h5 class="text-primary">
                                                                                    Nilai <?php echo htmlspecialchars($laporan['spesialisasi'] ?? ''); ?>
                                                                                </h5>
                                                                                <h2 class="<?php echo $laporan['skor_spesialisasi'] >= 80 ? 'skor-high' : ($laporan['skor_spesialisasi'] >= 60 ? 'skor-medium' : 'skor-low'); ?>">
                                                                                    <?php echo number_format($laporan['skor_spesialisasi'], 2); ?>
                                                                                </h2>
                                                                            </div>
                                                                        </div>
                                                                        <?php if ($laporan['catatan']): ?>
                                                                            <hr>
                                                                            <div>
                                                                                <strong>Catatan Juri:</strong><br>
                                                                                <em><?php echo htmlspecialchars($laporan['catatan']); ?></em>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aksi Admin -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-cog me-2"></i>
                                    Aksi Admin
                                </h5>
                                <div class="btn-group" role="group">
                                    <a href="peserta_final.php" class="btn btn-outline-primary">
                                        <i class="fas fa-trophy me-2"></i>
                                        Kelola Peserta Final
                                    </a>
                                    <a href="assignment_juri_final.php" class="btn btn-outline-info">
                                        <i class="fas fa-user-tie me-2"></i>
                                        Assignment Juri
                                    </a>
                                    <a href="laporan_penilaian.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Laporan Penyisihan
                                    </a>
                                </div>
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
