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

// Download template CSV
if (isset($_GET['action']) && $_GET['action'] === 'template') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=template_soal.csv');
    $output = fopen('php://output', 'w');
    // Header kolom
    fputcsv($output, ['soal1', 'soal2', 'soal3']);
    // Contoh baris
    fputcsv($output, ['Contoh teks Soal 1', 'Contoh teks Soal 2', 'Contoh teks Soal 3']);
    fputcsv($output, ['Isi soal 1 baris 2', 'Isi soal 2 baris 2', 'Isi soal 3 baris 2']);
    fclose($output);
    exit();
}

// Handle tambah soal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $jenis_lama = $_POST['jenis_lama'] ?? '';
    $soal1 = trim($_POST['soal1_lama'] ?? '');
    $soal2 = trim($_POST['soal2_lama'] ?? '');
    $soal3 = trim($_POST['soal3_lama'] ?? '');
    if ($jenis_lama && $soal1 && $soal2 && $soal3) {
        try {
            if ($jenis_lama === 'penyisihan') {
                if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'")->rowCount()) {
                    $nextNo = (int)$pdo->query("SELECT COALESCE(MAX(no_soal), 0) + 1 FROM soal_musabaqoh")->fetchColumn();
                    $stmt = $pdo->prepare("INSERT INTO soal_musabaqoh (no_soal, soal1, soal2, soal3) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nextNo, $soal1, $soal2, $soal3]);
                    $success = 'Soal penyisihan berhasil ditambahkan dengan nomor ' . $nextNo . '!';
                } else {
                    $error = 'Tabel soal_musabaqoh tidak ditemukan.';
                }
            } else {
                if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'")->rowCount()) {
                    $nextNo = (int)$pdo->query("SELECT COALESCE(MAX(no_soal), 0) + 1 FROM soal_musabaqoh_final")->fetchColumn();
                    $stmt = $pdo->prepare("INSERT INTO soal_musabaqoh_final (no_soal, soal1, soal2, soal3) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$nextNo, $soal1, $soal2, $soal3]);
                    $success = 'Soal final berhasil ditambahkan dengan nomor ' . $nextNo . '!';
                } else {
                    $error = 'Tabel soal_musabaqoh_final tidak ditemukan.';
                }
            }
        } catch (PDOException $e) {
            error_log('Gagal menambah soal: ' . $e->getMessage());
            $error = 'Gagal menambah soal. Silakan coba lagi.';
        }
    } else {
        $error = 'Semua field harus diisi.';
    }
}

// Handle hapus soal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $jenisDel = $_POST['jenis_del'] ?? '';
    $noDel = isset($_POST['no_soal_del']) ? (int)$_POST['no_soal_del'] : 0;
    if ($jenisDel && $noDel > 0) {
        try {
            if ($jenisDel === 'penyisihan') {
                if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'")->rowCount()) {
                    $stmt = $pdo->prepare("DELETE FROM soal_musabaqoh WHERE no_soal = ?");
                    $stmt->execute([$noDel]);
                    $success = 'Soal penyisihan No. ' . $noDel . ' berhasil dihapus!';
                } else {
                    $error = 'Tabel soal_musabaqoh tidak ditemukan.';
                }
            } else if ($jenisDel === 'final') {
                if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'")->rowCount()) {
                    $stmt = $pdo->prepare("DELETE FROM soal_musabaqoh_final WHERE no_soal = ?");
                    $stmt->execute([$noDel]);
                    $success = 'Soal final No. ' . $noDel . ' berhasil dihapus!';
                } else {
                    $error = 'Tabel soal_musabaqoh_final tidak ditemukan.';
                }
            }
        } catch (PDOException $e) {
            error_log('Gagal menghapus soal: ' . $e->getMessage());
            $error = 'Gagal menghapus soal. Silakan coba lagi.';
        }
    } else {
        $error = 'Data hapus tidak valid.';
    }
}

// Handle import CSV (Excel-export)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import') {
    $jenisImport = $_POST['jenis_import'] ?? '';
    if (!isset($_FILES['file_csv']) || !is_uploaded_file($_FILES['file_csv']['tmp_name'])) {
        $error = 'File CSV tidak ditemukan.';
    } elseif (!$jenisImport || ($jenisImport !== 'penyisihan' && $jenisImport !== 'final')) {
        $error = 'Jenis import tidak valid.';
    } else {
        $tmpPath = $_FILES['file_csv']['tmp_name'];
        $imported = 0;
        $skipped = 0;
        try {
            $pdo->beginTransaction();
            if ($jenisImport === 'penyisihan') {
                if (!$pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'" )->rowCount()) {
                    throw new Exception('Tabel soal_musabaqoh tidak ditemukan.');
                }
                $nextNo = (int)$pdo->query("SELECT COALESCE(MAX(no_soal), 0) + 1 FROM soal_musabaqoh")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO soal_musabaqoh (no_soal, soal1, soal2, soal3) VALUES (?, ?, ?, ?)");
            } else {
                if (!$pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'" )->rowCount()) {
                    throw new Exception('Tabel soal_musabaqoh_final tidak ditemukan.');
                }
                $nextNo = (int)$pdo->query("SELECT COALESCE(MAX(no_soal), 0) + 1 FROM soal_musabaqoh_final")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO soal_musabaqoh_final (no_soal, soal1, soal2, soal3) VALUES (?, ?, ?, ?)");
            }

            $fh = fopen($tmpPath, 'r');
            if ($fh === false) {
                throw new Exception('Gagal membuka file CSV.');
            }

            // Deteksi header: jika baris pertama mengandung kata 'soal' maka treat as header
            $isFirst = true;
            while (($row = fgetcsv($fh, 0, ',')) !== false) {
                // Normalisasi kolom: harap 3 kolom: soal1, soal2, soal3
                if ($isFirst) {
                    $joined = strtolower(implode(' ', $row));
                    if (strpos($joined, 'soal') !== false) { // header terdeteksi
                        $isFirst = false;
                        continue;
                    }
                    $isFirst = false;
                }
                // Lewati baris kosong
                if (count($row) === 0) { $skipped++; continue; }
                // Pastikan minimal 3 kolom; jika kurang, skip
                $c1 = isset($row[0]) ? trim($row[0]) : '';
                $c2 = isset($row[1]) ? trim($row[1]) : '';
                $c3 = isset($row[2]) ? trim($row[2]) : '';
                if ($c1 === '' && $c2 === '' && $c3 === '') { $skipped++; continue; }
                if ($c1 === '' || $c2 === '' || $c3 === '') { $skipped++; continue; }

                $stmt->execute([$nextNo, $c1, $c2, $c3]);
                $imported++;
                $nextNo++;
            }
            fclose($fh);

            $pdo->commit();
            $success = 'Import selesai. Berhasil: ' . $imported . ', dilewati: ' . $skipped . '.';
        } catch (Exception $ex) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            error_log('Gagal import: ' . $ex->getMessage());
            $error = 'Gagal import. Silakan coba lagi.';
        }
    }
}

// Handle edit soal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $jenisEdit = $_POST['jenis_edit'] ?? '';
    $noEdit = isset($_POST['no_soal_edit']) ? (int)$_POST['no_soal_edit'] : 0;
    $soal1Edit = trim($_POST['soal1_edit'] ?? '');
    $soal2Edit = trim($_POST['soal2_edit'] ?? '');
    $soal3Edit = trim($_POST['soal3_edit'] ?? '');
    if ($jenisEdit && $noEdit > 0 && $soal1Edit && $soal2Edit && $soal3Edit) {
        try {
            if ($jenisEdit === 'penyisihan') {
                if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'")->rowCount()) {
                    $stmt = $pdo->prepare("UPDATE soal_musabaqoh SET soal1 = ?, soal2 = ?, soal3 = ? WHERE no_soal = ?");
                    $stmt->execute([$soal1Edit, $soal2Edit, $soal3Edit, $noEdit]);
                    $success = 'Soal penyisihan No. ' . $noEdit . ' berhasil diperbarui!';
                } else {
                    $error = 'Tabel soal_musabaqoh tidak ditemukan.';
                }
            } else if ($jenisEdit === 'final') {
                if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'")->rowCount()) {
                    $stmt = $pdo->prepare("UPDATE soal_musabaqoh_final SET soal1 = ?, soal2 = ?, soal3 = ? WHERE no_soal = ?");
                    $stmt->execute([$soal1Edit, $soal2Edit, $soal3Edit, $noEdit]);
                    $success = 'Soal final No. ' . $noEdit . ' berhasil diperbarui!';
                } else {
                    $error = 'Tabel soal_musabaqoh_final tidak ditemukan.';
                }
            }
        } catch (PDOException $e) {
            error_log('Gagal memperbarui soal: ' . $e->getMessage());
            $error = 'Gagal memperbarui soal. Silakan coba lagi.';
        }
    } else {
        $error = 'Data edit tidak valid.';
    }
}

// Ambil ringkasan soal (skema lama)
$soalPenyisihan = [];
$soalFinal = [];
try {
    if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh'")->rowCount()) {
        $soalPenyisihan = $pdo->query("SELECT no_soal, soal1, soal2, soal3 FROM soal_musabaqoh ORDER BY no_soal")->fetchAll();
    }
} catch (PDOException $e) {}

try {
    if ($pdo->query("SHOW TABLES LIKE 'soal_musabaqoh_final'")->rowCount()) {
        $soalFinal = $pdo->query("SELECT no_soal, soal1, soal2, soal3 FROM soal_musabaqoh_final ORDER BY no_soal")->fetchAll();
    }
} catch (PDOException $e) {}

// (Skema baru dihapus)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal Musabaqoh - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-book-open me-2"></i>Soal Musabaqoh</h1>
                    <div class="btn-toolbar">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="export_soal_excel.php?jenis=semua"><i class="fas fa-layer-group me-2"></i>Semua Soal</a></li>
                                <li><a class="dropdown-item" href="export_soal_excel.php?jenis=penyisihan"><i class="fas fa-list me-2"></i>Soal Penyisihan</a></li>
                                <li><a class="dropdown-item" href="export_soal_excel.php?jenis=final"><i class="fas fa-trophy me-2"></i>Soal Final</a></li>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="export_soal_pdf.php?jenis=semua" target="_blank"><i class="fas fa-layer-group me-2"></i>Semua Soal</a></li>
                                <li><a class="dropdown-item" href="export_soal_pdf.php?jenis=penyisihan" target="_blank"><i class="fas fa-list me-2"></i>Soal Penyisihan</a></li>
                                <li><a class="dropdown-item" href="export_soal_pdf.php?jenis=final" target="_blank"><i class="fas fa-trophy me-2"></i>Soal Final</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-plus me-2"></i>Tambah Soal</div>
                    <div class="card-body">
                        <form method="POST">
                            <?php echo getCsrfInput(); ?>
                            <input type="hidden" name="action" value="add">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Jenis</label>
                                        <select class="form-select" name="jenis_lama" required>
                                            <option value="">Pilih</option>
                                            <option value="penyisihan">Penyisihan</option>
                                            <option value="final">Final</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="alert alert-info mt-4">
                                        Nomor soal akan diisi otomatis berurutan.
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Soal 1</label>
                                <textarea class="form-control" name="soal1_lama" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Soal 2</label>
                                <textarea class="form-control" name="soal2_lama" rows="2" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Soal 3</label>
                                <textarea class="form-control" name="soal3_lama" rows="2" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                        </form>
                        <hr>
                        <div class="mt-3">
                            <h6><i class="fas fa-file-import me-2"></i>Import Soal (CSV)</h6>
                            <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                                <?php echo getCsrfInput(); ?>
                                <input type="hidden" name="action" value="import">
                                <div class="col-md-3">
                                    <label class="form-label">Jenis</label>
                                    <select class="form-select" name="jenis_import" required>
                                        <option value="">Pilih</option>
                                        <option value="penyisihan">Penyisihan</option>
                                        <option value="final">Final</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">File CSV</label>
                                    <input type="file" class="form-control" name="file_csv" accept=".csv" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-upload me-2"></i>Import</button>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">Format kolom: soal1, soal2, soal3. Baris header akan otomatis dilewati jika ada.</small>
                                </div>
                            </form>
                            <div class="mt-2">
                                <a href="soal.php?action=template" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-download me-2"></i>Download Template CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-book-quran me-2"></i>
                        <strong>Skema Lama</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Penyisihan</h6>
                                <?php if (empty($soalPenyisihan)): ?>
                                    <p class="text-muted">Tidak ada data.</p>
                                <?php else: ?>
                                    <div class="accordion" id="accPenyisihan">
                                        <?php foreach ($soalPenyisihan as $idx => $s): ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="psh-<?php echo $idx; ?>">
                                                    <button class="accordion-button collapsed d-flex justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#psb-<?php echo $idx; ?>">
                                                        <span>Soal No. <?php echo $s['no_soal']; ?></span>
                                                        <form method="POST" onsubmit="return confirm('Hapus Soal Penyisihan No. <?php echo $s['no_soal']; ?>?');">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="jenis_del" value="penyisihan">
                                                            <input type="hidden" name="no_soal_del" value="<?php echo $s['no_soal']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2"><i class="fas fa-trash"></i> Del</button>
                                                        </form>
                                                    </button>
                                                </h2>
                                                <div id="psb-<?php echo $idx; ?>" class="accordion-collapse collapse" data-bs-parent="#accPenyisihan">
                                                    <div class="accordion-body">
                                                        <form method="POST" class="mb-3">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="edit">
                                                            <input type="hidden" name="jenis_edit" value="penyisihan">
                                                            <input type="hidden" name="no_soal_edit" value="<?php echo $s['no_soal']; ?>">
                                                            <div class="mb-2">
                                                                <label class="form-label">Soal 1</label>
                                                                <textarea class="form-control" name="soal1_edit" rows="2" required><?php echo htmlspecialchars($s['soal1']); ?></textarea>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Soal 2</label>
                                                                <textarea class="form-control" name="soal2_edit" rows="2" required><?php echo htmlspecialchars($s['soal2']); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Soal 3</label>
                                                                <textarea class="form-control" name="soal3_edit" rows="2" required><?php echo htmlspecialchars($s['soal3']); ?></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-save me-1"></i>Update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Final</h6>
                                <?php if (empty($soalFinal)): ?>
                                    <p class="text-muted">Tidak ada data.</p>
                                <?php else: ?>
                                    <div class="accordion" id="accFinal">
                                        <?php foreach ($soalFinal as $idx => $s): ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="fnl-<?php echo $idx; ?>">
                                                    <button class="accordion-button collapsed d-flex justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#fnb-<?php echo $idx; ?>">
                                                        <span>Soal No. <?php echo $s['no_soal']; ?></span>
                                                        <form method="POST" onsubmit="return confirm('Hapus Soal Final No. <?php echo $s['no_soal']; ?>?');">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="jenis_del" value="final">
                                                            <input type="hidden" name="no_soal_del" value="<?php echo $s['no_soal']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger ms-2"><i class="fas fa-trash"></i> Del</button>
                                                        </form>
                                                    </button>
                                                </h2>
                                                <div id="fnb-<?php echo $idx; ?>" class="accordion-collapse collapse" data-bs-parent="#accFinal">
                                                    <div class="accordion-body">
                                                        <form method="POST" class="mb-3">
                                                            <?php echo getCsrfInput(); ?>
                                                            <input type="hidden" name="action" value="edit">
                                                            <input type="hidden" name="jenis_edit" value="final">
                                                            <input type="hidden" name="no_soal_edit" value="<?php echo $s['no_soal']; ?>">
                                                            <div class="mb-2">
                                                                <label class="form-label">Soal 1</label>
                                                                <textarea class="form-control" name="soal1_edit" rows="2" required><?php echo htmlspecialchars($s['soal1']); ?></textarea>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label">Soal 2</label>
                                                                <textarea class="form-control" name="soal2_edit" rows="2" required><?php echo htmlspecialchars($s['soal2']); ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Soal 3</label>
                                                                <textarea class="form-control" name="soal3_edit" rows="2" required><?php echo htmlspecialchars($s['soal3']); ?></textarea>
                                                            </div>
                                                            <button type="submit" class="btn btn-sm btn-warning"><i class="fas fa-save me-1"></i>Update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


