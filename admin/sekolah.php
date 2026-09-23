<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';

// Proses tambah sekolah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nama_sekolah = trim($_POST['nama_sekolah']);
    $npsn = trim($_POST['npsn']);
    $alamat_sekolah = trim($_POST['alamat_sekolah']);
    $no_hp_sekolah = trim($_POST['no_hp_sekolah']);
    $email_sekolah = trim($_POST['email_sekolah']);
    $nama_kepala_sekolah = trim($_POST['nama_kepala_sekolah']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($nama_sekolah) || empty($username) || empty($password)) {
        $error = 'Nama sekolah, username, dan password harus diisi!';
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO sekolah (nama_sekolah, npsn, alamat_sekolah, no_hp_sekolah, email_sekolah, nama_kepala_sekolah, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nama_sekolah, $npsn, $alamat_sekolah, $no_hp_sekolah, $email_sekolah, $nama_kepala_sekolah, $username, $hashed_password]);
            $success = 'Sekolah berhasil ditambahkan!';
        } catch (PDOException $e) {
            $error = 'Username sudah digunakan atau terjadi kesalahan!';
        }
    }
}

// Proses edit sekolah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = (int)$_POST['id'];
    $nama_sekolah = trim($_POST['nama_sekolah']);
    $npsn = trim($_POST['npsn']);
    $alamat_sekolah = trim($_POST['alamat_sekolah']);
    $no_hp_sekolah = trim($_POST['no_hp_sekolah']);
    $email_sekolah = trim($_POST['email_sekolah']);
    $nama_kepala_sekolah = trim($_POST['nama_kepala_sekolah']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $status = trim($_POST['status']);
    
    if (empty($nama_sekolah) || empty($username)) {
        $error = 'Nama sekolah dan username harus diisi!';
    } else {
        try {
            // Cek apakah username sudah digunakan oleh sekolah lain
            $stmt = $pdo->prepare("SELECT id FROM sekolah WHERE username = ? AND id != ?");
            $stmt->execute([$username, $id]);
            if ($stmt->fetch()) {
                $error = 'Username sudah digunakan oleh sekolah lain!';
            } else {
                // Update data sekolah
                if (!empty($password)) {
                    // Jika password diisi, update dengan password baru
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE sekolah SET nama_sekolah = ?, npsn = ?, alamat_sekolah = ?, no_hp_sekolah = ?, email_sekolah = ?, nama_kepala_sekolah = ?, username = ?, password = ?, status = ? WHERE id = ?");
                    $stmt->execute([$nama_sekolah, $npsn, $alamat_sekolah, $no_hp_sekolah, $email_sekolah, $nama_kepala_sekolah, $username, $hashed_password, $status, $id]);
                } else {
                    // Jika password kosong, update tanpa mengubah password
                    $stmt = $pdo->prepare("UPDATE sekolah SET nama_sekolah = ?, npsn = ?, alamat_sekolah = ?, no_hp_sekolah = ?, email_sekolah = ?, nama_kepala_sekolah = ?, username = ?, status = ? WHERE id = ?");
                    $stmt->execute([$nama_sekolah, $npsn, $alamat_sekolah, $no_hp_sekolah, $email_sekolah, $nama_kepala_sekolah, $username, $status, $id]);
                }
                $success = 'Data sekolah berhasil diperbarui!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan dalam memperbarui data!';
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

// Ambil data sekolah
try {
    $stmt = $pdo->query("SELECT * FROM sekolah ORDER BY created_at DESC");
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
    <title>Kelola Sekolah - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .form-control { border-radius: 10px; border: 2px solid #e9ecef; padding: 12px 15px; transition: all 0.3s ease; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
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
                        <h2><i class="fas fa-school me-2"></i>Kelola Sekolah</h2>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="export_sekolah.php" class="btn btn-success btn-action" title="Export Excel">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </a>
                            <a href="export_sekolah_pdf.php" class="btn btn-danger btn-action" title="Export PDF">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </a>
                            <button class="btn btn-info text-white btn-action" data-bs-toggle="modal" data-bs-target="#importSekolahModal">
                                <i class="fas fa-file-import me-2"></i>Import Excel
                            </button>
                            <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#addSekolahModal">
                                <i class="fas fa-plus me-2"></i>Tambah Sekolah
                            </button>
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
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Sekolah</th>
                                            <th>NPSN</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($sekolah_list)): ?>
                                            <?php foreach ($sekolah_list as $index => $sekolah): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($sekolah['nama_sekolah']); ?></td>
                                                    <td><?php echo htmlspecialchars($sekolah['npsn']); ?></td>
                                                    <td><?php echo htmlspecialchars($sekolah['username']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $sekolah['status'] == 'Aktif' ? 'success' : 'danger'; ?>">
                                                            <?php echo $sekolah['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($sekolah['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="editSekolah(<?php echo $sekolah['id']; ?>, '<?php echo htmlspecialchars($sekolah['nama_sekolah'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($sekolah['npsn'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($sekolah['alamat_sekolah'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($sekolah['no_hp_sekolah'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($sekolah['email_sekolah'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($sekolah['nama_kepala_sekolah'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($sekolah['username'], ENT_QUOTES); ?>', '<?php echo $sekolah['status']; ?>')">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Belum ada data sekolah</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Sekolah -->
    <div class="modal fade" id="addSekolahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_sekolah" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="npsn" class="form-label">NPSN</label>
                                <input type="text" class="form-control" id="npsn" name="npsn">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_sekolah" class="form-label">Alamat Sekolah</label>
                            <textarea class="form-control" id="alamat_sekolah" name="alamat_sekolah" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_hp_sekolah" class="form-label">No HP Sekolah</label>
                                <input type="tel" class="form-control" id="no_hp_sekolah" name="no_hp_sekolah">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_sekolah" class="form-label">Email Sekolah</label>
                                <input type="email" class="form-control" id="email_sekolah" name="email_sekolah">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nama_kepala_sekolah" class="form-label">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control" id="nama_kepala_sekolah" name="nama_kepala_sekolah">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
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

    <!-- Modal Edit Sekolah -->
    <div class="modal fade" id="editSekolahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editSekolahForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama_sekolah" class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_sekolah" name="nama_sekolah" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_npsn" class="form-label">NPSN</label>
                                <input type="text" class="form-control" id="edit_npsn" name="npsn">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_alamat_sekolah" class="form-label">Alamat Sekolah</label>
                            <textarea class="form-control" id="edit_alamat_sekolah" name="alamat_sekolah" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_no_hp_sekolah" class="form-label">No HP Sekolah</label>
                                <input type="tel" class="form-control" id="edit_no_hp_sekolah" name="no_hp_sekolah">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_email_sekolah" class="form-label">Email Sekolah</label>
                                <input type="email" class="form-control" id="edit_email_sekolah" name="email_sekolah">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama_kepala_sekolah" class="form-label">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control" id="edit_nama_kepala_sekolah" name="nama_kepala_sekolah">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_username" name="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="edit_password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import Sekolah -->
    <div class="modal fade" id="importSekolahModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #00b4db, #0083b0); color: white; border: none;">
                    <h5 class="modal-title"><i class="fas fa-file-import me-2"></i>Import Data Sekolah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="import_sekolah.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="alert alert-info py-2 mb-3" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Format kolom yang diharapkan:</strong><br>
                            Nama Sekolah, NPSN, Alamat, No HP, Email, Nama Kepala Sekolah, Username
                        </div>
                        <div class="mb-3">
                            <label for="import_file" class="form-label fw-bold">Pilih File Excel / CSV</label>
                            <input type="file" class="form-control" id="import_file" name="file_import" accept=".xls,.xlsx,.csv" required>
                            <small class="text-muted">Format: .xls, .xlsx, atau .csv (maks 5MB)</small>
                        </div>
                        <div class="alert alert-warning py-2 mb-0" style="font-size: 0.82rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Catatan:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <li>Baris pertama harus berisi header kolom</li>
                                <li>Password default: <code>123456</code></li>
                                <li>Username yang sudah ada akan dilewati</li>
                                <li>Bisa gunakan file hasil <strong>Export Excel</strong> sebagai template</li>
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
        // Fungsi untuk mengisi form edit dengan data sekolah
        function editSekolah(id, namaSekolah, npsn, alamatSekolah, noHpSekolah, emailSekolah, namaKepalaSekolah, username, status) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama_sekolah').value = namaSekolah;
            document.getElementById('edit_npsn').value = npsn || '';
            document.getElementById('edit_alamat_sekolah').value = alamatSekolah || '';
            document.getElementById('edit_no_hp_sekolah').value = noHpSekolah || '';
            document.getElementById('edit_email_sekolah').value = emailSekolah || '';
            document.getElementById('edit_nama_kepala_sekolah').value = namaKepalaSekolah || '';
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_status').value = status;
            
            // Tampilkan modal edit
            var editModal = new bootstrap.Modal(document.getElementById('editSekolahModal'));
            editModal.show();
        }
    </script>
</body>
</html>
