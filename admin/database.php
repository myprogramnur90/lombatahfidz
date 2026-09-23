<?php
session_start();
require_once '../config/database.php';
require_once '../config/security.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$success = '';
$error = '';
$updateResults = [];

// Direktori backup
$backupDir = '../uploads/backup/';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// =============================================
// FUNGSI: Verifikasi Password Admin
// =============================================
function verifyAdminPassword($pdo, $adminId, $password) {
    $stmt = $pdo->prepare("SELECT password FROM admin WHERE id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password'])) {
        return true;
    }
    return false;
}

// Cek password untuk aksi sensitif (backup, restore, delete_backup, empty_table)
$sensitiveActions = ['backup', 'restore', 'delete_backup', 'empty_table'];
if (isset($_POST['action']) && in_array($_POST['action'], $sensitiveActions)) {
    if (empty($_POST['admin_password'])) {
        $error = 'Password admin wajib diisi untuk melakukan aksi ini!';
        $_POST['action'] = '';
    } elseif (!verifyAdminPassword($pdo, $_SESSION['admin_id'], $_POST['admin_password'])) {
        $error = 'Password admin salah! Aksi dibatalkan.';
        $_POST['action'] = '';
    }
}

// =============================================
// AKSI: BACKUP DATABASE
// =============================================
if (isset($_POST['action']) && $_POST['action'] === 'backup') {
    try {
        $tables = [];
        $stmt = $pdo->query("SHOW TABLES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        $sql = "-- ================================================\n";
        $sql .= "-- Backup Database: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Sistem: " . getPengaturan('nama_lomba') . "\n";
        $sql .= "-- ================================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            // CREATE TABLE
            $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $sql .= "DROP TABLE IF EXISTS `$table`;\n";
            $sql .= $row[1] . ";\n\n";
            
            // INSERT DATA
            $stmt = $pdo->query("SELECT * FROM `$table`");
            $rows = $stmt->fetchAll(PDO::FETCH_NUM);
            
            if (count($rows) > 0) {
                // Ambil nama kolom
                $stmtCols = $pdo->query("SHOW COLUMNS FROM `$table`");
                $cols = $stmtCols->fetchAll(PDO::FETCH_COLUMN);
                $colNames = '`' . implode('`, `', $cols) . '`';
                
                foreach ($rows as $row) {
                    $values = array_map(function($val) use ($pdo) {
                        if ($val === null) return 'NULL';
                        return $pdo->quote($val);
                    }, $row);
                    $sql .= "INSERT INTO `$table` ($colNames) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Simpan file backup
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . $filename;
        file_put_contents($filepath, $sql);
        
        $success = "Backup berhasil dibuat: <strong>$filename</strong> (" . round(filesize($filepath) / 1024, 1) . " KB)";
    } catch (Exception $e) {
        $error = "Gagal membuat backup: " . $e->getMessage();
    }
}

// =============================================
// AKSI: RESTORE DATABASE
// =============================================
if (isset($_POST['action']) && $_POST['action'] === 'restore') {
    if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['sql_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'sql') {
            $error = "File harus berformat .sql!";
        } else {
            try {
                $sqlContent = file_get_contents($_FILES['sql_file']['tmp_name']);
                
                // Eksekusi SQL
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $pdo->exec($sqlContent);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                
                $success = "Database berhasil di-restore dari file: <strong>" . htmlspecialchars($_FILES['sql_file']['name']) . "</strong>";
            } catch (PDOException $e) {
                $error = "Gagal restore database: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['restore_file'])) {
        // Restore dari file backup yang sudah ada
        $file = basename($_POST['restore_file']);
        $filepath = $backupDir . $file;
        if (file_exists($filepath) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            try {
                $sqlContent = file_get_contents($filepath);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                $pdo->exec($sqlContent);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $success = "Database berhasil di-restore dari: <strong>$file</strong>";
            } catch (PDOException $e) {
                $error = "Gagal restore database: " . $e->getMessage();
            }
        } else {
            $error = "File backup tidak ditemukan!";
        }
    } else {
        $error = "Pilih file SQL untuk di-restore!";
    }
}

// =============================================
// AKSI: HAPUS FILE BACKUP
// =============================================
if (isset($_POST['action']) && $_POST['action'] === 'delete_backup') {
    $file = basename($_POST['delete_file']);
    $filepath = $backupDir . $file;
    if (file_exists($filepath) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
        unlink($filepath);
        $success = "File backup <strong>$file</strong> berhasil dihapus.";
    } else {
        $error = "File tidak ditemukan!";
    }
}

// =============================================
// AKSI: DOWNLOAD BACKUP
// =============================================
if (isset($_GET['download'])) {
    $file = basename($_GET['download']);
    $filepath = $backupDir . $file;
    if (file_exists($filepath) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit();
    }
}

// =============================================
// AKSI: KOSONGKAN TABEL
// =============================================
if (isset($_POST['action']) && $_POST['action'] === 'empty_table') {
    $tablesToEmpty = $_POST['tables'] ?? [];
    
    if (!empty($tablesToEmpty) && is_array($tablesToEmpty)) {
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
            $successCount = 0;
            
            foreach ($tablesToEmpty as $tableName) {
                // Validasi nama tabel (hanya izinkan alfanumerik dan underscore)
                if (preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                    $pdo->exec("TRUNCATE TABLE `$tableName`");
                    $successCount++;
                }
            }
            
            if ($successCount > 0) {
                $success = "Sebanyak <strong>$successCount</strong> tabel berhasil dikosongkan.";
            } else {
                $error = "Tidak ada tabel yang dikosongkan (nama tabel tidak valid).";
            }
        } catch (PDOException $e) {
            $error = "Gagal mengosongkan tabel: " . $e->getMessage();
        } finally {
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
        }
    } else {
        $error = "Pilih minimal satu tabel untuk dikosongkan!";
    }
}

// =============================================
// AKSI: UPDATE DATABASE (Jalankan semua update script)
// =============================================
if (isset($_POST['action']) && $_POST['action'] === 'update_db') {
    $updateScripts = [
        ['file' => 'update_database.php', 'desc' => 'Update Database Dasar'],
        ['file' => 'update_database_assignment.php', 'desc' => 'Update Assignment Juri'],
        ['file' => 'update_database_final.php', 'desc' => 'Update Sistem Final'],
        ['file' => 'update_database_penilaian.php', 'desc' => 'Update Penilaian'],
        ['file' => 'update_database_spesialisasi.php', 'desc' => 'Update Spesialisasi'],
        ['file' => 'setup_musabaqoh_aktif.php', 'desc' => 'Setup Musabaqoh Aktif'],
    ];
    
    foreach ($updateScripts as &$script) {
        $scriptPath = '../' . $script['file'];
        if (file_exists($scriptPath)) {
            try {
                ob_start();
                include $scriptPath;
                $output = ob_get_clean();
                $script['status'] = 'success';
                $script['output'] = trim($output);
            } catch (Exception $e) {
                ob_end_clean();
                $script['status'] = 'error';
                $script['output'] = $e->getMessage();
            }
        } else {
            $script['status'] = 'skipped';
            $script['output'] = 'File tidak ditemukan';
        }
    }
    unset($script);
    
    $updateResults = $updateScripts;
    $success = "Update database selesai dijalankan!";
}

// =============================================
// Ambil daftar file backup yang ada
// =============================================
$backupFiles = [];
if (is_dir($backupDir)) {
    $files = scandir($backupDir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $f) {
        if (pathinfo($f, PATHINFO_EXTENSION) === 'sql') {
            $backupFiles[] = [
                'name' => $f,
                'size' => filesize($backupDir . $f),
                'date' => filemtime($backupDir . $f)
            ];
        }
    }
}

// Ambil info database
$dbInfo = [];
$tableList = [];
try {
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tableList[] = $row[0];
    }
    $dbInfo['tables'] = count($tableList);
    
    $totalRows = 0;
    foreach ($tableList as $tableName) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `$tableName`");
        $totalRows += $countStmt->fetchColumn();
    }
    $dbInfo['rows'] = $totalRows;
} catch (Exception $e) {
    $dbInfo['tables'] = 0;
    $dbInfo['rows'] = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Database - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; font-weight: 600; }
        .btn-success { border-radius: 10px; font-weight: 600; }
        .btn-warning { border-radius: 10px; font-weight: 600; }
        .btn-danger { border-radius: 10px; font-weight: 600; }
        .stat-card {
            border-radius: 15px;
            padding: 1.5rem;
            color: white;
            text-align: center;
        }
        .stat-card h3 { font-weight: 700; margin-bottom: 0.3rem; }
        .stat-card small { opacity: 0.8; }
        .backup-item {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem 1.2rem;
            margin-bottom: 0.8rem;
            transition: all 0.2s;
            background: white;
        }
        .backup-item:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.08); }
        .form-control { border-radius: 10px; border: 2px solid #e9ecef; padding: 10px 15px; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h2><i class="fas fa-database me-2"></i>Kelola Database</h2>
                    <p class="text-muted">Backup, restore, dan update database sistem</p>
                    
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

                    <!-- Hasil Update Database -->
                    <?php if (!empty($updateResults)): ?>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-clipboard-list me-2"></i>Hasil Update Database:</h6>
                            <table class="table table-sm table-bordered mt-2 mb-0 bg-white" style="border-radius: 8px; overflow: hidden;">
                                <thead class="table-light">
                                    <tr><th>Script</th><th>Status</th><th>Keterangan</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($updateResults as $res): ?>
                                        <tr>
                                            <td><code><?php echo $res['file']; ?></code><br><small class="text-muted"><?php echo $res['desc']; ?></small></td>
                                            <td>
                                                <?php if ($res['status'] === 'success'): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check"></i> Berhasil</span>
                                                <?php elseif ($res['status'] === 'skipped'): ?>
                                                    <span class="badge bg-secondary"><i class="fas fa-forward"></i> Dilewati</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Error</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><small><?php echo htmlspecialchars($res['output']); ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Info Database & Aksi Cepat -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                                <h3><?php echo $dbInfo['tables']; ?></h3>
                                <small><i class="fas fa-table me-1"></i> Total Tabel</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                                <h3><?php echo number_format($dbInfo['rows']); ?></h3>
                                <small><i class="fas fa-list me-1"></i> Total Baris Data</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #ee0979, #ff6a00);">
                                <h3><?php echo count($backupFiles); ?></h3>
                                <small><i class="fas fa-archive me-1"></i> File Backup</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" onsubmit="return confirm('Yakin ingin menjalankan update database? Disarankan backup terlebih dahulu.');">
                                <input type="hidden" name="action" value="update_db">
                                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb, #f5576c); cursor: pointer;" onclick="this.closest('form').submit();">
                                    <h3><i class="fas fa-sync-alt"></i></h3>
                                    <small>Klik untuk Update DB</small>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- BACKUP -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5><i class="fas fa-download me-2 text-success"></i>Backup Database</h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="text-muted">Buat salinan database saat ini untuk cadangan. File backup berformat .sql dan bisa digunakan untuk memulihkan data.</p>
                                    <form method="POST" id="formBackup" class="mt-auto">
                                        <input type="hidden" name="action" value="backup">
                                        <input type="hidden" name="admin_password" class="admin-password-field" value="">
                                        <button type="button" class="btn btn-success btn-lg w-100 btn-need-password" data-form="formBackup">
                                            <i class="fas fa-download me-2"></i>Buat Backup
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- RESTORE -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5><i class="fas fa-upload me-2 text-warning"></i>Restore Database</h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="text-muted">Upload file .sql untuk memulihkan database ke kondisi sebelumnya.</p>
                                    <form method="POST" enctype="multipart/form-data" id="formRestore" class="mt-auto">
                                        <input type="hidden" name="action" value="restore">
                                        <input type="hidden" name="admin_password" class="admin-password-field" value="">
                                        <div class="mb-3">
                                            <input type="file" class="form-control" name="sql_file" accept=".sql" required>
                                        </div>
                                        <button type="button" class="btn btn-warning btn-lg w-100 btn-need-password" data-form="formRestore" data-confirm="⚠️ PERINGATAN: Restore akan MENIMPA data yang ada saat ini. Pastikan Anda sudah backup terlebih dahulu. Lanjutkan?">
                                            <i class="fas fa-upload me-2"></i>Restore dari File
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- KOSONGKAN TABEL -->
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5><i class="fas fa-eraser me-2 text-danger"></i>Kosongkan Tabel</h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="text-muted mb-2">Pilih tabel yang ingin dikosongkan isinya secara permanen.</p>
                                    <form method="POST" id="formEmptyTable" class="mt-auto d-flex flex-column h-100">
                                        <input type="hidden" name="action" value="empty_table">
                                        <input type="hidden" name="admin_password" class="admin-password-field" value="">
                                        <div class="mb-3 flex-grow-1 p-2" style="max-height: 180px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; background: #fff;">
                                            <?php foreach ($tableList as $t): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tables[]" value="<?php echo htmlspecialchars($t); ?>" id="table_<?php echo htmlspecialchars($t); ?>">
                                                    <label class="form-check-label w-100" style="cursor:pointer;" for="table_<?php echo htmlspecialchars($t); ?>">
                                                        <?php echo htmlspecialchars($t); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-danger btn-lg w-100 btn-need-password" data-form="formEmptyTable" data-confirm="⚠️ PERINGATAN: Seluruh isi data pada tabel yang dicentang akan dihapus permanen. Lanjutkan?">
                                            <i class="fas fa-trash-alt me-2"></i>Kosongkan Pilihan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DAFTAR BACKUP -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-archive me-2"></i>Riwayat Backup</h5>
                            <span class="badge bg-primary"><?php echo count($backupFiles); ?> file</span>
                        </div>
                        <div class="card-body">
                            <?php if (count($backupFiles) > 0): ?>
                                <?php foreach ($backupFiles as $bf): ?>
                                    <div class="backup-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><i class="fas fa-file-code me-2 text-primary"></i><?php echo htmlspecialchars($bf['name']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-weight-hanging me-1"></i> <?php echo round($bf['size'] / 1024, 1); ?> KB
                                                &nbsp;|&nbsp;
                                                <i class="fas fa-clock me-1"></i> <?php echo date('d M Y, H:i', $bf['date']); ?>
                                            </small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <!-- Download -->
                                            <a href="database.php?download=<?php echo urlencode($bf['name']); ?>" class="btn btn-sm btn-outline-primary" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <!-- Restore -->
                                            <form method="POST" class="d-inline" id="formRestoreBackup_<?php echo md5($bf['name']); ?>">
                                                <input type="hidden" name="action" value="restore">
                                                <input type="hidden" name="restore_file" value="<?php echo htmlspecialchars($bf['name']); ?>">
                                                <input type="hidden" name="admin_password" class="admin-password-field" value="">
                                                <button type="button" class="btn btn-sm btn-outline-warning btn-need-password" title="Restore" data-form="formRestoreBackup_<?php echo md5($bf['name']); ?>" data-confirm="⚠️ Restore dari backup ini akan MENIMPA data saat ini. Lanjutkan?">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            <!-- Hapus -->
                                            <form method="POST" class="d-inline" id="formDeleteBackup_<?php echo md5($bf['name']); ?>">
                                                <input type="hidden" name="action" value="delete_backup">
                                                <input type="hidden" name="delete_file" value="<?php echo htmlspecialchars($bf['name']); ?>">
                                                <input type="hidden" name="admin_password" class="admin-password-field" value="">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-need-password" title="Hapus" data-form="formDeleteBackup_<?php echo md5($bf['name']); ?>" data-confirm="Hapus file backup ini?">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-2 d-block"></i>
                                    <p>Belum ada file backup. Klik tombol <strong>"Buat Backup Sekarang"</strong> untuk membuat backup pertama.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Password Admin -->
    <div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <h5 class="modal-title" id="modalPasswordLabel">
                        <i class="fas fa-shield-alt me-2"></i>Konfirmasi Password Admin
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-lock fa-3x text-muted mb-2"></i>
                        <p class="text-muted">Masukkan password admin Anda untuk melanjutkan aksi ini.</p>
                    </div>
                    <div class="mb-3">
                        <label for="inputAdminPassword" class="form-label fw-bold">Password Admin</label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius: 10px 0 0 10px;"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control" id="inputAdminPassword" placeholder="Masukkan password..." autofocus style="border-radius: 0 10px 10px 0;">
                        </div>
                        <div class="invalid-feedback" id="passwordError">Password tidak boleh kosong!</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="btnConfirmPassword" style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-check me-1"></i>Konfirmasi & Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentFormId = null;
        const modal = new bootstrap.Modal(document.getElementById('modalPassword'));
        const passwordInput = document.getElementById('inputAdminPassword');
        const btnConfirm = document.getElementById('btnConfirmPassword');

        // Semua tombol yang membutuhkan password
        document.querySelectorAll('.btn-need-password').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const formId = this.getAttribute('data-form');
                const confirmMsg = this.getAttribute('data-confirm');

                // Jika ada pesan konfirmasi, tanya dulu
                if (confirmMsg) {
                    if (!confirm(confirmMsg)) {
                        return;
                    }
                }

                // Validasi form (cek file upload jika ada)
                const form = document.getElementById(formId);
                const fileInput = form.querySelector('input[type="file"]');
                if (fileInput && fileInput.hasAttribute('required') && !fileInput.value) {
                    fileInput.reportValidity();
                    return;
                }

                currentFormId = formId;
                passwordInput.value = '';
                passwordInput.classList.remove('is-invalid');
                modal.show();

                // Fokus ke input password setelah modal muncul
                document.getElementById('modalPassword').addEventListener('shown.bs.modal', function() {
                    passwordInput.focus();
                }, { once: true });
            });
        });

        // Konfirmasi password
        btnConfirm.addEventListener('click', function() {
            const password = passwordInput.value.trim();
            if (!password) {
                passwordInput.classList.add('is-invalid');
                return;
            }

            if (currentFormId) {
                const form = document.getElementById(currentFormId);
                const pwField = form.querySelector('.admin-password-field');
                pwField.value = password;
                modal.hide();
                form.submit();
            }
        });

        // Enter key untuk submit
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                btnConfirm.click();
            }
        });

        // Reset state saat modal ditutup
        document.getElementById('modalPassword').addEventListener('hidden.bs.modal', function() {
            passwordInput.value = '';
            passwordInput.classList.remove('is-invalid');
            currentFormId = null;
        });
    });
    </script>
</body>
</html>
