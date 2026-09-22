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
try {
    $stmt = $pdo->query("SHOW TABLES");
    $dbInfo['tables'] = $stmt->rowCount();
    
    $totalRows = 0;
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$row[0]}`");
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-download me-2 text-success"></i>Backup Database</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Buat salinan database saat ini untuk cadangan. File backup berformat .sql dan bisa digunakan untuk memulihkan data.</p>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="backup">
                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-download me-2"></i>Buat Backup Sekarang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- RESTORE -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-upload me-2 text-warning"></i>Restore Database</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Upload file .sql untuk memulihkan database ke kondisi sebelumnya.</p>
                                    <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('⚠️ PERINGATAN: Restore akan MENIMPA data yang ada saat ini. Pastikan Anda sudah backup terlebih dahulu. Lanjutkan?');">
                                        <input type="hidden" name="action" value="restore">
                                        <div class="mb-3">
                                            <input type="file" class="form-control" name="sql_file" accept=".sql" required>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-lg w-100">
                                            <i class="fas fa-upload me-2"></i>Restore dari File
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
                                            <form method="POST" class="d-inline" onsubmit="return confirm('⚠️ Restore dari backup ini akan MENIMPA data saat ini. Lanjutkan?');">
                                                <input type="hidden" name="action" value="restore">
                                                <input type="hidden" name="restore_file" value="<?php echo htmlspecialchars($bf['name']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Restore">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            <!-- Hapus -->
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Hapus file backup ini?');">
                                                <input type="hidden" name="action" value="delete_backup">
                                                <input type="hidden" name="delete_file" value="<?php echo htmlspecialchars($bf['name']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
