<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateCsrfToken();
}

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

// Proses approve peserta (Setuju)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'approve_peserta') {
    $peserta_id = $_POST['peserta_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE peserta SET status = 'Diterima' WHERE id = ?");
        $stmt->execute([$peserta_id]);
        
        $stmt = $pdo->prepare("SELECT sekolah_id FROM peserta WHERE id = ?");
        $stmt->execute([$peserta_id]);
        $peserta_data = $stmt->fetch();
        $sekolah_id = $peserta_data['sekolah_id'];
        
        $stmt = $pdo->prepare("UPDATE pembayaran SET status_pembayaran = 'Lunas' WHERE sekolah_id = ?");
        $stmt->execute([$sekolah_id]);
        
        $stmt = $pdo->prepare("UPDATE dokumen_berka SET status_dokumen = 'Diterima' WHERE sekolah_id = ?");
        $stmt->execute([$sekolah_id]);
        
        $success = 'Status peserta berhasil disetujui (Diterima)! Pembayaran dan dokumen otomatis divalidasi.';
    } catch (PDOException $e) {
        $error = 'Terjadi kesalahan dalam menyetujui peserta!';
    }
}

// Proses tambah peserta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $sekolah_id = $_POST['sekolah_id'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nisn = trim($_POST['nisn']);
    $tempat_lahir = trim($_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $kelas = trim($_POST['kelas']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);
    
    if (empty($sekolah_id) || empty($nama_lengkap) || empty($nisn)) {
        $error = 'Sekolah, Nama Lengkap, dan NISN harus diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM peserta WHERE nisn = ?");
            $stmt->execute([$nisn]);
            if ($stmt->fetch()) {
                $error = 'NISN sudah terdaftar!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO peserta (sekolah_id, nama_lengkap, nisn, tempat_lahir, tanggal_lahir, jenis_kelamin, kelas, no_hp, alamat, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
                $stmt->execute([$sekolah_id, $nama_lengkap, $nisn, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $kelas, $no_hp, $alamat]);
                $success = 'Peserta berhasil ditambahkan!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan dalam menambahkan data!';
        }
    }
}

// Cek flash message dari import
if (isset($_SESSION['import_success'])) {
    $success = $_SESSION['import_success'];
    unset($_SESSION['import_success']);
}
if (isset($_SESSION['import_error'])) {
    $error = $_SESSION['import_error'];
    unset($_SESSION['import_error']);
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

// Inisialisasi variabel untuk menghindari undefined variable
$peserta_list = [];
$total_records = 0;
$total_pages = 1;

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
            SELECT p1.sekolah_id, p1.status_pembayaran, p1.nominal, p1.bukti_pembayaran
            FROM pembayaran p1
            INNER JOIN (
                SELECT sekolah_id, MAX(created_at) as max_created
                FROM pembayaran
                GROUP BY sekolah_id
            ) p2 ON p1.sekolah_id = p2.sekolah_id AND p1.created_at = p2.max_created
        ) pb ON p.sekolah_id = pb.sekolah_id
        LEFT JOIN (
            SELECT d1.sekolah_id, d1.status_dokumen, d1.jenis_dokumen, d1.file_dokumen
            FROM dokumen_berka d1
            INNER JOIN (
                SELECT sekolah_id, MAX(created_at) as max_created
                FROM dokumen_berka
                GROUP BY sekolah_id
            ) d2 ON d1.sekolah_id = d2.sekolah_id AND d1.created_at = d2.max_created
        ) db ON p.sekolah_id = db.sekolah_id
        $where_clause
        ORDER BY p.created_at DESC
        LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
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
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 10px 25px; font-weight: 600; }
        .btn-action { border-radius: 10px; padding: 10px 20px; font-weight: 500; transition: all 0.3s ease; display: inline-flex; align-items: center; justify-content: center; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h2><i class="fas fa-users me-2"></i>Data Peserta</h2>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="export_peserta.php" class="btn btn-success btn-action" title="Export Excel">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </a>
                            <a href="export_peserta_pdf.php" target="_blank" class="btn btn-danger btn-action" title="Export PDF">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </a>
                            <button class="btn btn-info text-white btn-action" data-bs-toggle="modal" data-bs-target="#importPesertaModal">
                                <i class="fas fa-file-import me-2"></i>Import Excel
                            </button>
                            <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#addPesertaModal">
                                <i class="fas fa-plus me-2"></i>Tambah Peserta
                            </button>
                        </div>
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
                                        <?php 
                                        $modalsHtml = '';
                                        if (!empty($peserta_list)): ?>
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
                                                            <?php echo sanitizeOutput($peserta['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($peserta['status_pembayaran']): ?>
                                                            <span class="badge bg-<?php echo $peserta['status_pembayaran'] == 'Lunas' ? 'success' : ($peserta['status_pembayaran'] == 'Ditolak' ? 'danger' : 'warning'); ?>">
                                                                <?php echo sanitizeOutput($peserta['status_pembayaran']); ?>
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
                                                                <?php echo sanitizeOutput($peserta['status_dokumen']); ?>
                                                            </span>
                                                            <?php if ($peserta['jenis_dokumen']): ?>
                                                                <br><small class="text-muted"><?php echo $peserta['jenis_dokumen']; ?></small>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Belum Upload</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($peserta['tanggal_daftar'])); ?></td>
                                                    <td class="text-nowrap">
                                                        <?php if ($peserta['status'] != 'Diterima'): ?>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Setujui peserta ini? (Status akan menjadi Diterima, Pembayaran & Dokumen otomatis divalidasi)');">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="approve_peserta">
                                                            <input type="hidden" name="peserta_id" value="<?php echo $peserta['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Setujui (Diterima)">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <?php endif; ?>
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
                                                <?php ob_start(); ?>
                                                
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
                                                                <?php echo getCsrfInput(); ?>
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
                                                                <?php echo getCsrfInput(); ?>
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
                                                <?php $modalsHtml .= ob_get_clean(); ?>
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
                    
                    <?php echo $modalsHtml; ?>

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

    <!-- Modal Tambah Peserta -->
    <div class="modal fade" id="addPesertaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Peserta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sekolah_id" class="form-label">Sekolah <span class="text-danger">*</span></label>
                                <select class="form-control" id="sekolah_id" name="sekolah_id" required>
                                    <option value="">Pilih Sekolah...</option>
                                    <?php if (!empty($sekolah_list)): ?>
                                        <?php foreach ($sekolah_list as $sekolah): ?>
                                            <option value="<?php echo $sekolah['id']; ?>">
                                                <?php echo htmlspecialchars($sekolah['nama_sekolah']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nisn" name="nisn" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kelas" class="form-label">Kelas</label>
                                <input type="text" class="form-control" id="kelas" name="kelas">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="tel" class="form-control" id="no_hp" name="no_hp">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Peserta -->
    <div class="modal fade" id="importPesertaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #00b4db, #0083b0); color: white; border: none;">
                    <h5 class="modal-title"><i class="fas fa-file-import me-2"></i>Import Data Peserta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="import_peserta.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="alert alert-info py-2 mb-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Format kolom yang diharapkan (CSV):</strong><br>
                            Nama Lengkap, NISN, Username Sekolah, Tempat Lahir, Tanggal Lahir (YYYY-MM-DD), Jenis Kelamin (L/P), Kelas, No HP, Alamat
                        </div>
                        <div class="mb-3">
                            <label for="import_file" class="form-label fw-bold">Pilih File CSV</label>
                            <input type="file" class="form-control" id="import_file" name="file_import" accept=".csv" required>
                            <small class="text-muted">Format: .csv (maks 5MB)</small>
                        </div>
                        <div class="alert alert-warning py-2 mb-0" style="font-size: 0.82rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Catatan:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Baris pertama harus berisi header kolom</li>
                                <li>Pastikan Username Sekolah benar</li>
                                <li>NISN yang sudah ada akan dilewati</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-upload me-1"></i>Import Sekarang
                        </button>
                    </div>
                </form>
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



