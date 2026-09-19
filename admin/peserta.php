<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Proses delete peserta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $peserta_id = $_POST['peserta_id'];
    
    try {
        // Hapus peserta
        $stmt = $pdo->prepare("DELETE FROM peserta WHERE id = ?");
        $stmt->execute([$peserta_id]);
        
        $success = 'Data peserta berhasil dihapus!';
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam menghapus data!';
    }
}

// Proses update status peserta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $peserta_id = $_POST['peserta_id'];
    $status = $_POST['status'];
    $catatan = trim($_POST['catatan']);
    
    try {
        // Update status peserta
        $stmt = $pdo->prepare("UPDATE peserta SET status = ?, catatan = ? WHERE id = ?");
        $stmt->execute([$status, $catatan, $peserta_id]);
        
        // Ambil sekolah_id dari peserta
        $stmt = $pdo->prepare("SELECT sekolah_id FROM peserta WHERE id = ?");
        $stmt->execute([$peserta_id]);
        $peserta_data = $stmt->fetch();
        $sekolah_id = $peserta_data['sekolah_id'];
        
        // Validasi otomatis pembayaran dan dokumen berdasarkan status peserta
        if ($status == 'Diterima') {
            // Jika peserta diterima, set SEMUA pembayaran dan dokumen sekolah menjadi diterima
            $stmt = $pdo->prepare("UPDATE pembayaran SET status_pembayaran = 'Lunas' WHERE sekolah_id = ?");
            $stmt->execute([$sekolah_id]);
            
            $stmt = $pdo->prepare("UPDATE dokumen_berka SET status_dokumen = 'Diterima' WHERE sekolah_id = ?");
            $stmt->execute([$sekolah_id]);
            
            $success = 'Status peserta berhasil diupdate! Semua pembayaran dan dokumen sekolah otomatis divalidasi.';
        } elseif ($status == 'Ditolak') {
            // Jika peserta ditolak, set SEMUA pembayaran dan dokumen sekolah menjadi ditolak
            $stmt = $pdo->prepare("UPDATE pembayaran SET status_pembayaran = 'Ditolak' WHERE sekolah_id = ?");
            $stmt->execute([$sekolah_id]);
            
            $stmt = $pdo->prepare("UPDATE dokumen_berka SET status_dokumen = 'Ditolak' WHERE sekolah_id = ?");
            $stmt->execute([$sekolah_id]);
            
            $success = 'Status peserta berhasil diupdate! Semua pembayaran dan dokumen sekolah otomatis ditolak.';
        } else {
            $success = 'Status peserta berhasil diupdate!';
        }
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam mengupdate data!';
    }
}

// Filter dan pagination
$filter_sekolah = isset($_GET['sekolah']) ? $_GET['sekolah'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query dengan filter
$where_conditions = [];
$params = [];

if (!empty($filter_sekolah)) {
    $where_conditions[] = "p.sekolah_id = ?";
    $params[] = $filter_sekolah;
}

if (!empty($filter_status)) {
    $where_conditions[] = "p.status = ?";
    $params[] = $filter_status;
}

if (!empty($search)) {
    $where_conditions[] = "(p.nama_lengkap LIKE ? OR p.nisn LIKE ? OR s.nama_sekolah LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Ambil data peserta dengan filter dan pagination
try {
    // Query untuk data dengan status pembayaran dan dokumen
    $query = "
        SELECT p.*, s.nama_sekolah,
               pb.status_pembayaran, pb.nominal as nominal_pembayaran, pb.bukti_pembayaran,
               db.status_dokumen, db.jenis_dokumen, db.file_dokumen
        FROM peserta p 
        JOIN sekolah s ON p.sekolah_id = s.id 
        LEFT JOIN (
            SELECT sekolah_id, status_pembayaran, nominal, bukti_pembayaran,
                   ROW_NUMBER() OVER (PARTITION BY sekolah_id ORDER BY created_at DESC) as rn
            FROM pembayaran
        ) pb ON p.sekolah_id = pb.sekolah_id AND pb.rn = 1
        LEFT JOIN (
            SELECT sekolah_id, status_dokumen, jenis_dokumen, file_dokumen,
                   ROW_NUMBER() OVER (PARTITION BY sekolah_id ORDER BY created_at DESC) as rn
            FROM dokumen_berka
        ) db ON p.sekolah_id = db.sekolah_id AND db.rn = 1
        $where_clause
        ORDER BY p.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $peserta_list = $stmt->fetchAll();
    
    // Query untuk total data (untuk pagination)
    $count_query = "
        SELECT COUNT(*) 
        FROM peserta p 
        JOIN sekolah s ON p.sekolah_id = s.id 
        $where_clause
    ";
    
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_records = $stmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);
    
    // Ambil data sekolah untuk filter
    $stmt = $pdo->query("SELECT id, nama_sekolah FROM sekolah ORDER BY nama_sekolah");
    $sekolah_list = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Terjadi kesalahan dalam mengambil data!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 8px 20px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-users me-2"></i>Data Peserta</h2>
                        <a href="export_peserta.php" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Export Excel
                        </a>
                    </div>
                    
                    <!-- Filter Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-filter me-2"></i>Filter Data</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Cari</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($search); ?>" 
                                           placeholder="Nama, NISN, atau Sekolah">
                                </div>
                                <div class="col-md-3">
                                    <label for="sekolah" class="form-label">Sekolah</label>
                                    <select class="form-control" id="sekolah" name="sekolah">
                                        <option value="">Semua Sekolah</option>
                                        <?php if (!empty($sekolah_list)): ?>
                                            <?php foreach ($sekolah_list as $sekolah): ?>
                                                <option value="<?php echo $sekolah['id']; ?>" 
                                                        <?php echo $filter_sekolah == $sekolah['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($sekolah['nama_sekolah']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Semua Status</option>
                                        <option value="Pending" <?php echo $filter_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Diterima" <?php echo $filter_status == 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
                                        <option value="Ditolak" <?php echo $filter_status == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>Filter
                                        </button>
                                        <a href="peserta.php" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
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
                    
                    <!-- Info Pagination -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="text-muted">
                                Menampilkan <?php echo count($peserta_list); ?> dari <?php echo $total_records; ?> data
                                <?php if (!empty($search) || !empty($filter_sekolah) || !empty($filter_status)): ?>
                                    (hasil filter)
                                <?php endif; ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-muted">Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></span>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Peserta</th>
                                            <th>NISN</th>
                                            <th>Sekolah</th>
                                            <th>Kelas</th>
                                            <th>Jenis Kelamin</th>
                                            <th>No HP</th>
                                            <th>Status</th>
                                            <th>Pembayaran</th>
                                            <th>Berkas</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($peserta_list)): ?>
                                            <?php foreach ($peserta_list as $index => $peserta): ?>
                                                <tr>
                                                    <td><?php echo $offset + $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['nisn']); ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['kelas']); ?></td>
                                                    <td><?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                                    <td><?php echo htmlspecialchars($peserta['no_hp']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $peserta['status'] == 'Diterima' ? 'success' : ($peserta['status'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                            <?php echo $peserta['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($peserta['status_pembayaran']): ?>
                                                            <span class="badge bg-<?php echo $peserta['status_pembayaran'] == 'Lunas' ? 'success' : ($peserta['status_pembayaran'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                                <?php echo $peserta['status_pembayaran']; ?>
                                                            </span>
                                                            <?php if ($peserta['nominal_pembayaran']): ?>
                                                                <br><small class="text-muted">Rp <?php echo number_format($peserta['nominal_pembayaran']); ?></small>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Belum Bayar</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($peserta['status_dokumen']): ?>
                                                            <span class="badge bg-<?php echo $peserta['status_dokumen'] == 'Diterima' ? 'success' : ($peserta['status_dokumen'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                                <?php echo $peserta['status_dokumen']; ?>
                                                            </span>
                                                            <?php if ($peserta['jenis_dokumen']): ?>
                                                                <br><small class="text-muted"><?php echo $peserta['jenis_dokumen']; ?></small>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Belum Upload</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($peserta['tanggal_daftar'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $peserta['id']; ?>" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#statusModal<?php echo $peserta['id']; ?>" title="Edit Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $peserta['id']; ?>" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal Detail -->
                                                <div class="modal fade" id="detailModal<?php echo $peserta['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detail Peserta</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p><strong>Nama Lengkap:</strong><br><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></p>
                                                                        <p><strong>NISN:</strong><br><?php echo htmlspecialchars($peserta['nisn']); ?></p>
                                                                        <p><strong>Sekolah:</strong><br><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></p>
                                                                        <p><strong>Kelas:</strong><br><?php echo htmlspecialchars($peserta['kelas']); ?></p>
                                                                        <p><strong>Jenis Kelamin:</strong><br><?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p><strong>Tempat Lahir:</strong><br><?php echo htmlspecialchars($peserta['tempat_lahir']); ?></p>
                                                                        <p><strong>Tanggal Lahir:</strong><br><?php echo date('d/m/Y', strtotime($peserta['tanggal_lahir'])); ?></p>
                                                                        <p><strong>No HP:</strong><br><?php echo htmlspecialchars($peserta['no_hp']); ?></p>
                                                                        <p><strong>Alamat:</strong><br><?php echo htmlspecialchars($peserta['alamat']); ?></p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <hr>
                                                                
                                                                <!-- Informasi Pembayaran -->
                                                                <h6><i class="fas fa-credit-card me-2"></i>Informasi Pembayaran</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <p><strong>Status Pembayaran:</strong><br>
                                                                            <?php if ($peserta['status_pembayaran']): ?>
                                                                                <span class="badge bg-<?php echo $peserta['status_pembayaran'] == 'Lunas' ? 'success' : ($peserta['status_pembayaran'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                                                    <?php echo $peserta['status_pembayaran']; ?>
                                                                                </span>
                                                                            <?php else: ?>
                                                                                <span class="badge bg-secondary">Belum Bayar</span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <p><strong>Nominal:</strong><br>
                                                                            <?php if ($peserta['nominal_pembayaran']): ?>
                                                                                Rp <?php echo number_format($peserta['nominal_pembayaran']); ?>
                                                                            <?php else: ?>
                                                                                <span class="text-muted">-</span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <p><strong>Bukti Pembayaran:</strong><br>
                                                                            <?php if ($peserta['bukti_pembayaran']): ?>
                                                                                <a href="../uploads/bukti_pembayaran/<?php echo $peserta['bukti_pembayaran']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                                    <i class="fas fa-eye me-1"></i>Lihat
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <span class="text-muted">-</span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <hr>
                                                                
                                                                <!-- Informasi Berkas -->
                                                                <h6><i class="fas fa-file-alt me-2"></i>Informasi Berkas</h6>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <p><strong>Status Berkas:</strong><br>
                                                                            <?php if ($peserta['status_dokumen']): ?>
                                                                                <span class="badge bg-<?php echo $peserta['status_dokumen'] == 'Diterima' ? 'success' : ($peserta['status_dokumen'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                                                    <?php echo $peserta['status_dokumen']; ?>
                                                                                </span>
                                                                            <?php else: ?>
                                                                                <span class="badge bg-secondary">Belum Upload</span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <p><strong>Jenis Dokumen:</strong><br>
                                                                            <?php if ($peserta['jenis_dokumen']): ?>
                                                                                <?php echo htmlspecialchars($peserta['jenis_dokumen']); ?>
                                                                            <?php else: ?>
                                                                                <span class="text-muted">-</span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <p><strong>File Dokumen:</strong><br>
                                                                            <?php if ($peserta['file_dokumen']): ?>
                                                                                <a href="../uploads/dokumen_berka/<?php echo $peserta['file_dokumen']; ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                                                                    <i class="fas fa-download me-1"></i>Download
                                                                                </a>
                                                                            <?php else: ?>
                                                                                <span class="text-muted">-</span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                
                                                                <?php if ($peserta['catatan']): ?>
                                                                    <hr>
                                                                    <p><strong>Catatan:</strong><br><?php echo htmlspecialchars($peserta['catatan']); ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Update Status -->
                                                <div class="modal fade" id="statusModal<?php echo $peserta['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Update Status Peserta</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="update_status">
                                                                <input type="hidden" name="peserta_id" value="<?php echo $peserta['id']; ?>">
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Nama Peserta</label>
                                                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($peserta['nama_lengkap']); ?>" readonly>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="status" class="form-label">Status</label>
                                                                        <select class="form-control" name="status" required>
                                                                            <option value="Pending" <?php echo $peserta['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                            <option value="Diterima" <?php echo $peserta['status'] == 'Diterima' ? 'selected' : ''; ?>>Diterima</option>
                                                                            <option value="Ditolak" <?php echo $peserta['status'] == 'Ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="catatan" class="form-label">Catatan</label>
                                                                        <textarea class="form-control" name="catatan" rows="3"><?php echo htmlspecialchars($peserta['catatan']); ?></textarea>
                                                                    </div>
                                                                    
                                                                    <div class="alert alert-info">
                                                                        <h6><i class="fas fa-info-circle me-2"></i>Validasi Otomatis:</h6>
                                                                        <ul class="mb-0">
                                                                            <li><strong>Diterima:</strong> SEMUA pembayaran dan dokumen sekolah otomatis divalidasi</li>
                                                                            <li><strong>Ditolak:</strong> SEMUA pembayaran dan dokumen sekolah otomatis ditolak</li>
                                                                            <li><strong>Pending:</strong> Tidak ada perubahan pada pembayaran/dokumen</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Delete -->
                                                <div class="modal fade" id="deleteModal<?php echo $peserta['id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="peserta_id" value="<?php echo $peserta['id']; ?>">
                                                                <div class="modal-body">
                                                                    <div class="alert alert-danger">
                                                                        <h6><i class="fas fa-warning me-2"></i>Peringatan!</h6>
                                                                        <p class="mb-0">Anda akan menghapus data peserta secara permanen. Tindakan ini tidak dapat dibatalkan!</p>
                                                                    </div>
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <p><strong>Nama Peserta:</strong><br><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></p>
                                                                            <p><strong>NISN:</strong><br><?php echo htmlspecialchars($peserta['nisn']); ?></p>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <p><strong>Sekolah:</strong><br><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></p>
                                                                            <p><strong>Kelas:</strong><br><?php echo htmlspecialchars($peserta['kelas']); ?></p>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="form-check mt-3">
                                                                        <input class="form-check-input" type="checkbox" id="confirmDelete<?php echo $peserta['id']; ?>" required>
                                                                        <label class="form-check-label text-danger" for="confirmDelete<?php echo $peserta['id']; ?>">
                                                                            <strong>Saya yakin ingin menghapus data peserta ini</strong>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger" id="deleteBtn<?php echo $peserta['id']; ?>" disabled>
                                                                        <i class="fas fa-trash me-2"></i>Hapus Data
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="12" class="text-center text-muted">Belum ada data peserta</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Page -->
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                                    </li>
                                    <?php if ($start_page > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>">
                                            <?php echo $total_pages; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <!-- Next Page -->
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable/disable delete button based on checkbox
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[id^="confirmDelete"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const deleteBtn = document.getElementById('deleteBtn' + this.id.replace('confirmDelete', ''));
                    if (deleteBtn) {
                        deleteBtn.disabled = !this.checked;
                    }
                });
            });
        });
    </script>
</body>
</html>
