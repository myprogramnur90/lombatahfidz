<?php
session_start();
require_once '../config/database.php';

$sekolah_id = $_SESSION['user_id'];
$page_title = 'Data Peserta';
$success = '';
$error = '';


// Ambil data peserta sekolah
try {
    $stmt = $pdo->prepare("SELECT * FROM peserta WHERE sekolah_id = ? ORDER BY created_at DESC");
    $stmt->execute([$sekolah_id]);
    $peserta_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}

// Buat konten data peserta
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fas fa-users me-2"></i>Data Peserta</h2>
    <button class="btn btn-success" onclick="printData()">
        <i class="fas fa-print me-2"></i>Print Data
    </button>
</div>
                    
                    <!-- Desktop Table View -->
                    <div class="card d-none d-md-block">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>NISN</th>
                                            <th>Kelas</th>
                                            <th>Jenis Kelamin</th>
                                            <th>No HP</th>
                                            <th>Status</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($peserta_list)): ?>
                                            <?php foreach ($peserta_list as $index => $peserta): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['nisn']); ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['kelas']); ?></td>
                                                    <td><?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['no_hp']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $peserta['status'] == 'Diterima' ? 'success' : ($peserta['status'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                            <?php echo $peserta['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($peserta['tanggal_daftar'])); ?></td>
                                                    <td>
                                                        <a href="edit_peserta.php?id=<?php echo $peserta['id']; ?>" class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">Belum ada peserta terdaftar</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        <?php if (!empty($peserta_list)): ?>
                            <?php foreach ($peserta_list as $index => $peserta): ?>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0"><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></h6>
                                            <span class="badge bg-<?php echo $peserta['status'] == 'Diterima' ? 'success' : ($peserta['status'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                <?php echo $peserta['status']; ?>
                                            </span>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">NISN:</small><br>
                                                <span><?php echo htmlspecialchars($peserta['nisn']); ?></span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Kelas:</small><br>
                                                <span><?php echo htmlspecialchars($peserta['kelas']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <small class="text-muted">Jenis Kelamin:</small><br>
                                                <span><?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">No HP:</small><br>
                                                <span><?php echo htmlspecialchars($peserta['no_hp']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <small class="text-muted">Tanggal Daftar:</small><br>
                                                <span><?php echo date('d/m/Y', strtotime($peserta['tanggal_daftar'])); ?></span>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <a href="edit_peserta.php?id=<?php echo $peserta['id']; ?>" class="btn btn-sm btn-outline-warning w-100">
                                                <i class="fas fa-edit me-2"></i>Edit Data
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-body text-center text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>Belum ada peserta terdaftar</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <script>
        function printData() {
            // Buat window baru untuk print
            const printWindow = window.open('', '_blank');
            
            // Tulis konten HTML untuk print
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Data Peserta - <?php echo $_SESSION['nama_sekolah']; ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        .table th, .table td { border: 1px solid #333; padding: 8px; text-align: left; }
                        .table th { background-color: #f2f2f2; font-weight: bold; }
                        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                        .bg-success { background-color: #28a745; color: white; }
                        .bg-warning { background-color: #ffc107; color: black; }
                        .bg-danger { background-color: #dc3545; color: white; }
                        h2 { color: #333; margin-bottom: 20px; }
                        .print-header { text-align: center; margin-bottom: 30px; }
                        .print-info { margin-bottom: 20px; }
                        @media print {
                            body { margin: 0; }
                            .print-header { margin-bottom: 20px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h2>Data Peserta Lomba Tahfidz</h2>
                        <h3><?php echo $_SESSION['nama_sekolah']; ?></h3>
                    </div>
                    <div class="print-info">
                        <p><strong>Tanggal Cetak:</strong> ${new Date().toLocaleDateString('id-ID')}</p>
                        <p><strong>Total Peserta:</strong> <?php echo count($peserta_list); ?> orang</p>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Jenis Kelamin</th>
                                <th>No HP</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($peserta_list)): ?>
                                <?php foreach ($peserta_list as $index => $peserta): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($peserta['nisn']); ?></td>
                                        <td><?php echo htmlspecialchars($peserta['kelas']); ?></td>
                                        <td><?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                        <td><?php echo htmlspecialchars($peserta['no_hp']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $peserta['status'] == 'Diterima' ? 'success' : ($peserta['status'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                <?php echo $peserta['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($peserta['tanggal_daftar'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada peserta terdaftar</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </body>
                </html>
            `);
            
            // Tunggu sebentar lalu print
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>
<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
