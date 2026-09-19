<?php
require_once '../config/database.php';

$peserta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$peserta_id) {
    echo '<div class="alert alert-danger">ID peserta tidak valid!</div>';
    exit();
}

// Ambil data peserta
$query_peserta = "
    SELECT p.*, s.nama_sekolah
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    WHERE p.id = ?
";
$stmt_peserta = $pdo->prepare($query_peserta);
$stmt_peserta->execute([$peserta_id]);
$peserta = $stmt_peserta->fetch();

if (!$peserta) {
    echo '<div class="alert alert-danger">Data peserta tidak ditemukan!</div>';
    exit();
}

// Ambil detail penilaian dari semua juri
$query_penilaian = "
    SELECT pen.*, j.nama_lengkap as nama_juri, j.spesialisasi
    FROM penilaian pen
    JOIN juri j ON pen.juri_id = j.id
    WHERE pen.peserta_id = ?
    ORDER BY pen.tanggal_penilaian
";
$stmt_penilaian = $pdo->prepare($query_penilaian);
$stmt_penilaian->execute([$peserta_id]);
$penilaian_list = $stmt_penilaian->fetchAll();

// Hitung rata-rata
$query_avg = "
    SELECT 
        AVG(CASE WHEN j.spesialisasi IN ('Tajwid dan Adab','Shifatul Huruf') THEN pen.skor_spesialisasi END) as avg_tajwid,
        AVG(CASE WHEN j.spesialisasi IN ('Kelancaran Hafalan','Kelancaran (Tahfidz)') THEN pen.skor_spesialisasi END) as avg_fluency,
        AVG(CASE WHEN j.spesialisasi IN ('Makhraj','Makhraj Tajwid') THEN pen.skor_spesialisasi END) as avg_makhraj,
        AVG(pen.skor_spesialisasi) as avg_keseluruhan,
        COUNT(*) as jumlah_penilaian
    FROM penilaian pen
    JOIN juri j ON pen.juri_id = j.id
    WHERE pen.peserta_id = ?
";
$stmt_avg = $pdo->prepare($query_avg);
$stmt_avg->execute([$peserta_id]);
$rata_rata = $stmt_avg->fetch();
?>

<div class="row">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="fas fa-user-graduate me-2"></i>
            <?php echo htmlspecialchars($peserta['nama_lengkap']); ?>
        </h5>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Data Peserta</h6>
            </div>
            <div class="card-body">
                <p><strong>NISN:</strong> <?php echo htmlspecialchars($peserta['nisn']); ?></p>
                <p><strong>Sekolah:</strong> <?php echo htmlspecialchars($peserta['nama_sekolah']); ?></p>
                <p><strong>Kelas:</strong> <?php echo htmlspecialchars($peserta['kelas']); ?></p>
                <p><strong>Jenis Kelamin:</strong> <?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">Rekapitulasi Skor</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-2">
                        <div class="border rounded p-2">
                            <strong><?php echo number_format($rata_rata['avg_tajwid'], 2); ?></strong>
                            <br><small class="text-muted">Tajwid</small>
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="border rounded p-2">
                            <strong><?php echo number_format($rata_rata['avg_fluency'], 2); ?></strong>
                            <br><small class="text-muted">Fluency</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <strong><?php echo number_format($rata_rata['avg_makhraj'], 2); ?></strong>
                            <br><small class="text-muted">Makhraj</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2 bg-primary text-white">
                            <strong><?php echo number_format($rata_rata['avg_keseluruhan'], 2); ?></strong>
                            <br><small>Keseluruhan</small>
                        </div>
                    </div>
                </div>
                <hr>
                <p class="text-center mb-0">
                    <strong>Jumlah Penilaian:</strong> 
                    <span class="badge bg-info"><?php echo $rata_rata['jumlah_penilaian']; ?>/3</span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h6 class="mb-3">
            <i class="fas fa-clipboard-list me-2"></i>
            Detail Penilaian per Juri
        </h6>
        
                        <?php if (count($penilaian_list) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Juri</th>
                                            <th>Spesialisasi</th>
                                            <th>Skor Spesialisasi</th>
                                            <th>Tanggal</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($penilaian_list as $penilaian): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($penilaian['nama_juri']); ?></strong>
                                </td>
                                <td>
                                    <small><?php echo htmlspecialchars(
                                        $penilaian['spesialisasi'] == 'Kelancaran Hafalan' ? 'Kelancaran (Tahfidz)' : (
                                        $penilaian['spesialisasi'] == 'Makhraj' ? 'Makhraj Tajwid' : (
                                        $penilaian['spesialisasi'] == 'Tajwid dan Adab' ? 'Shifatul Huruf' : $penilaian['spesialisasi']
                                        ))
                                    ); ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?php echo number_format($penilaian['skor_spesialisasi'], 2); ?></span>
                                </td>
                                <td>
                                    <small><?php echo date('d/m/Y H:i', strtotime($penilaian['tanggal_penilaian'])); ?></small>
                                </td>
                                <td>
                                    <?php if ($penilaian['catatan']): ?>
                                        <small><?php echo htmlspecialchars($penilaian['catatan']); ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Belum ada penilaian untuk peserta ini.
            </div>
        <?php endif; ?>
    </div>
</div>
