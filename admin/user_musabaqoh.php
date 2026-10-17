<?php
require_once '../config/security.php';
initSecureSession();
setSecurityHeaders();
require_once '../config/database.php';

// Proteksi admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
}

// Handle tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $nama_lengkap && $password) {
        try {
            // Cek username sudah ada
            $cek = $pdo->prepare("SELECT COUNT(*) FROM user_musabaqoh WHERE username = ?");
            $cek->execute([$username]);
            if ($cek->fetchColumn() > 0) {
                $error = 'Username sudah digunakan!';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO user_musabaqoh (username, password, nama_lengkap, status) VALUES (?, ?, ?, 'Aktif')");
                $stmt->execute([$username, $hashedPassword, $nama_lengkap]);
                $success = 'User berhasil ditambahkan!';
            }
        } catch (PDOException $e) {
            error_log('Gagal menambah user musabaqoh: ' . $e->getMessage());
            $error = 'Gagal menambah user. Silakan coba lagi.';
        }
    } else {
        $error = 'Semua field harus diisi.';
    }
}

// Handle edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $username = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

    if ($id > 0 && $username && $nama_lengkap) {
        try {
            // Cek username sudah ada (selain user ini)
            $cek = $pdo->prepare("SELECT COUNT(*) FROM user_musabaqoh WHERE username = ? AND id != ?");
            $cek->execute([$username, $id]);
            if ($cek->fetchColumn() > 0) {
                $error = 'Username sudah digunakan oleh user lain!';
            } else {
                $stmt = $pdo->prepare("UPDATE user_musabaqoh SET username = ?, nama_lengkap = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$username, $nama_lengkap, $id]);
                $success = 'User berhasil diperbarui!';
            }
        } catch (PDOException $e) {
            error_log('Gagal memperbarui user musabaqoh: ' . $e->getMessage());
            $error = 'Gagal memperbarui user. Silakan coba lagi.';
        }
    } else {
        $error = 'Data edit tidak valid.';
    }
}

// Handle reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $password_baru = trim($_POST['password_baru'] ?? '');

    if ($id > 0 && $password_baru) {
        try {
            $hashedPassword = password_hash($password_baru, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE user_musabaqoh SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashedPassword, $id]);
            $success = 'Password berhasil direset!';
        } catch (PDOException $e) {
            error_log('Gagal reset password: ' . $e->getMessage());
            $error = 'Gagal reset password. Silakan coba lagi.';
        }
    } else {
        $error = 'Password baru harus diisi.';
    }
}

// Handle toggle status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status_baru = $_POST['status_baru'] ?? '';

    if ($id > 0 && in_array($status_baru, ['Aktif', 'Nonaktif'])) {
        try {
            $stmt = $pdo->prepare("UPDATE user_musabaqoh SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status_baru, $id]);
            $success = 'Status user berhasil diubah menjadi ' . $status_baru . '!';
        } catch (PDOException $e) {
            error_log('Gagal mengubah status: ' . $e->getMessage());
            $error = 'Gagal mengubah status. Silakan coba lagi.';
        }
    } else {
        $error = 'Data tidak valid.';
    }
}

// Handle hapus user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM user_musabaqoh WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'User berhasil dihapus!';
        } catch (PDOException $e) {
            error_log('Gagal menghapus user: ' . $e->getMessage());
            $error = 'Gagal menghapus user. Silakan coba lagi.';
        }
    } else {
        $error = 'Data hapus tidak valid.';
    }
}

// Ambil semua user musabaqoh
$users = [];
try {
    $users = $pdo->query("SELECT * FROM user_musabaqoh ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $error = 'Gagal mengambil data user.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Soal/Musabaqoh - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-user-lock me-2"></i>User Soal / Musabaqoh</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus me-1"></i>Tambah User
                    </button>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Info Card -->
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi:</strong> User di halaman ini adalah akun yang digunakan untuk mengakses sistem <strong>Soal Musabaqoh</strong> (login di halaman <code>/auth/login_musabaqoh.php</code>). Kelola user untuk menambah, mengubah, atau menonaktifkan akses ke sistem soal.
                </div>

                <!-- Tabel User -->
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-users me-2"></i>
                        <strong>Daftar User Musabaqoh</strong>
                        <span class="badge bg-primary ms-2"><?php echo count($users); ?> user</span>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                                <p>Belum ada user musabaqoh.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Username</th>
                                            <th>Nama Lengkap</th>
                                            <th>Status</th>
                                            <th>Dibuat</th>
                                            <th>Terakhir Update</th>
                                            <th width="280">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $i => $u): ?>
                                            <tr>
                                                <td><?php echo $i + 1; ?></td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($u['username']); ?></code>
                                                </td>
                                                <td><?php echo htmlspecialchars($u['nama_lengkap']); ?></td>
                                                <td>
                                                    <?php if ($u['status'] === 'Aktif'): ?>
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Nonaktif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($u['created_at'])); ?></small></td>
                                                <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($u['updated_at'])); ?></small></td>
                                                <td>
                                                    <!-- Edit -->
                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $u['id']; ?>" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <!-- Reset Password -->
                                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalResetPw<?php echo $u['id']; ?>" title="Reset Password">
                                                        <i class="fas fa-key"></i>
                                                    </button>
                                                    <!-- Toggle Status -->
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Ubah status user <?php echo htmlspecialchars($u['username']); ?>?');">
                                                        <?php echo getCsrfInput(); ?>
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                        <input type="hidden" name="status_baru" value="<?php echo $u['status'] === 'Aktif' ? 'Nonaktif' : 'Aktif'; ?>">
                                                        <?php if ($u['status'] === 'Aktif'): ?>
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Nonaktifkan">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Aktifkan">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </form>
                                                    <!-- Hapus -->
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('HAPUS user <?php echo htmlspecialchars($u['username']); ?>? Aksi ini tidak bisa dibatalkan!');">
                                                        <?php echo getCsrfInput(); ?>
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit -->
                                            <div class="modal fade" id="modalEdit<?php echo $u['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="edit">
                                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit User</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Username</label>
                                                                    <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($u['username']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Nama Lengkap</label>
                                                                    <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($u['nama_lengkap']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Reset Password -->
                                            <div class="modal fade" id="modalResetPw<?php echo $u['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="reset_password">
                                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><i class="fas fa-key me-2"></i>Reset Password</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Reset password untuk user: <strong><?php echo htmlspecialchars($u['username']); ?></strong></p>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Password Baru</label>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" name="password_baru" id="pw_<?php echo $u['id']; ?>" required minlength="4">
                                                                        <button class="btn btn-outline-secondary" type="button" onclick="generatePassword(<?php echo $u['id']; ?>)" title="Generate password acak">
                                                                            <i class="fas fa-random"></i>
                                                                        </button>
                                                                    </div>
                                                                    <small class="text-muted">Minimal 4 karakter. Klik <i class="fas fa-random"></i> untuk generate otomatis.</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-info text-white"><i class="fas fa-key me-1"></i>Reset Password</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Modal Tambah User -->
                <div class="modal fade" id="modalTambah" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <?php echo getCsrfInput(); ?>
                                <input type="hidden" name="action" value="add">
                                <div class="modal-header">
                                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah User Musabaqoh</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="username" required placeholder="Masukkan username">
                                        <small class="text-muted">Username harus unik.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nama_lengkap" required placeholder="Masukkan nama lengkap">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="password" id="pw_new" required minlength="4" placeholder="Masukkan password">
                                            <button class="btn btn-outline-secondary" type="button" onclick="generatePassword('new')" title="Generate password acak">
                                                <i class="fas fa-random"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Minimal 4 karakter.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function generatePassword(id) {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let password = '';
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('pw_' + id).value = password;
    }
    </script>
</body>
</html>
