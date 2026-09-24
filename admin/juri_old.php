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

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $nama_lengkap = $_POST['nama_lengkap'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $no_hp = $_POST['no_hp'];
            $spesialisasi = $_POST['spesialisasi'];
            
            // Cek apakah username sudah ada
            $query_cek = "SELECT id FROM juri WHERE username = ?";
            $stmt_cek = $pdo->prepare($query_cek);
            $stmt_cek->execute([$username]);
            
            if ($stmt_cek->fetch()) {
                $error = 'Username sudah digunakan!';
            } else {
                $query_insert = "INSERT INTO juri (nama_lengkap, username, password, email, no_hp, spesialisasi) VALUES (?, ?, MD5(?), ?, ?, ?)";
                $stmt_insert = $pdo->prepare($query_insert);
                
                if ($stmt_insert->execute([$nama_lengkap, $username, $password, $email, $no_hp, $spesialisasi])) {
                    $success = 'Data juri berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan data juri!';
                }
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $nama_lengkap = $_POST['nama_lengkap'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $no_hp = $_POST['no_hp'];
            $spesialisasi = $_POST['spesialisasi'];
            $status = $_POST['status'];
            
            // Cek apakah username sudah ada (kecuali untuk juri yang sama)
            $query_cek = "SELECT id FROM juri WHERE username = ? AND id != ?";
            $stmt_cek = $conn->prepare($query_cek);
            $stmt_cek->bind_param("si", $username, $id);
            $stmt_cek->execute();
            
            if ($stmt_cek->get_result()->num_rows > 0) {
                $error = 'Username sudah digunakan!';
            } else {
                $query_update = "UPDATE juri SET nama_lengkap = ?, username = ?, email = ?, no_hp = ?, spesialisasi = ?, status = ? WHERE id = ?";
                $stmt_update = $conn->prepare($query_update);
                $stmt_update->bind_param("ssssssi", $nama_lengkap, $username, $email, $no_hp, $spesialisasi, $status, $id);
                
                if ($stmt_update->execute()) {
                    $success = 'Data juri berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui data juri!';
                }
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = $_POST['id'];
            
            // Cek apakah juri sudah melakukan penilaian
            $query_cek = "SELECT COUNT(*) as total FROM penilaian WHERE juri_id = ?";
            $stmt_cek = $conn->prepare($query_cek);
            $stmt_cek->bind_param("i", $id);
            $stmt_cek->execute();
            $result = $stmt_cek->get_result()->fetch_assoc();
            
            if ($result['total'] > 0) {
                $error = 'Tidak dapat menghapus juri yang sudah melakukan penilaian!';
            } else {
                $query_delete = "DELETE FROM juri WHERE id = ?";
                $stmt_delete = $conn->prepare($query_delete);
                $stmt_delete->bind_param("i", $id);
                
                if ($stmt_delete->execute()) {
                    $success = 'Data juri berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus data juri!';
                }
            }
        } elseif ($_POST['action'] == 'reset_password') {
            $id = $_POST['id'];
            $new_password = $_POST['new_password'];
            
            $query_reset = "UPDATE juri SET password = MD5(?) WHERE id = ?";
            $stmt_reset = $conn->prepare($query_reset);
            $stmt_reset->bind_param("si", $new_password, $id);
            
            if ($stmt_reset->execute()) {
                $success = 'Password juri berhasil direset!';
            } else {
                $error = 'Gagal mereset password!';
            }
        }
    }
}

// Ambil data juri
$query_juri = "SELECT * FROM juri ORDER BY nama_lengkap";
$result_juri = $conn->query($query_juri);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Juri - Admin</title>
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
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .badge {
            border-radius: 20px;
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-gavel me-2"></i>
                Manajemen Juri
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title mb-0">
                                    <i class="fas fa-users-cog me-2"></i>
                                    Manajemen Juri
                                </h2>
                                <p class="text-muted mb-0">Kelola data juri penilaian lomba</p>
                            </div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
                                <i class="fas fa-plus me-2"></i>Tambah Juri
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabel Juri -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Daftar Juri
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Spesialisasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_juri->num_rows > 0): ?>
                                <?php $no = 1; while ($juri = $result_juri->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($juri['nama_lengkap']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($juri['username']); ?></td>
                                        <td><?php echo htmlspecialchars($juri['email']); ?></td>
                                        <td><?php echo htmlspecialchars($juri['no_hp']); ?></td>
                                        <td><?php echo htmlspecialchars($juri['spesialisasi']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $juri['status'] == 'Aktif' ? 'success' : 'danger'; ?>">
                                                <?php echo $juri['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editJuri(<?php echo htmlspecialchars(json_encode($juri)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="resetPassword(<?php echo $juri['id']; ?>, '<?php echo htmlspecialchars($juri['nama_lengkap']); ?>')">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteJuri(<?php echo $juri['id']; ?>, '<?php echo htmlspecialchars($juri['nama_lengkap']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <h5>Belum ada data juri</h5>
                                        <p class="text-muted">Klik tombol "Tambah Juri" untuk menambahkan juri pertama.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Juri -->
    <div class="modal fade" id="modalAdd" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus me-2"></i>Tambah Juri Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp">
                        </div>
                        <div class="mb-3">
                            <label for="spesialisasi" class="form-label">Spesialisasi</label>
                            <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" 
                                   placeholder="Contoh: Tajwid dan Makhraj">
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

    <!-- Modal Edit Juri -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Edit Data Juri
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="edit_no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="edit_no_hp" name="no_hp">
                        </div>
                        <div class="mb-3">
                            <label for="edit_spesialisasi" class="form-label">Spesialisasi</label>
                            <input type="text" class="form-control" id="edit_spesialisasi" name="spesialisasi">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal fade" id="modalResetPassword" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="id" id="reset_id">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-key me-2"></i>Reset Password
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Reset password untuk juri: <strong id="reset_nama"></strong></p>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-trash me-2"></i>Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus data juri:</p>
                        <p><strong id="delete_nama"></strong></p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editJuri(juri) {
            document.getElementById('edit_id').value = juri.id;
            document.getElementById('edit_nama_lengkap').value = juri.nama_lengkap;
            document.getElementById('edit_username').value = juri.username;
            document.getElementById('edit_email').value = juri.email;
            document.getElementById('edit_no_hp').value = juri.no_hp;
            document.getElementById('edit_spesialisasi').value = juri.spesialisasi;
            document.getElementById('edit_status').value = juri.status;
            
            new bootstrap.Modal(document.getElementById('modalEdit')).show();
        }
        
        function resetPassword(id, nama) {
            document.getElementById('reset_id').value = id;
            document.getElementById('reset_nama').textContent = nama;
            new bootstrap.Modal(document.getElementById('modalResetPassword')).show();
        }
        
        function deleteJuri(id, nama) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_nama').textContent = nama;
            new bootstrap.Modal(document.getElementById('modalDelete')).show();
        }
    </script>
</body>
</html>

