<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

$peserta_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$peserta_id) {
    header('Location: laporan_penilaian.php');
    exit();
}

// Ambil data peserta
$stmt = $pdo->prepare("SELECT p.*, s.nama_sekolah FROM peserta p JOIN sekolah s ON p.sekolah_id = s.id WHERE p.id = ?");
$stmt->execute([$peserta_id]);
$peserta = $stmt->fetch();

if (!$peserta) {
    header('Location: laporan_penilaian.php');
    exit();
}

// Ambil semua juri
$juri_list = $pdo->query("SELECT * FROM juri ORDER BY spesialisasi, nama_lengkap")->fetchAll();

// Ambil penilaian yang sudah ada untuk peserta ini
$stmt_pen = $pdo->prepare("SELECT pen.*, j.nama_lengkap as nama_juri, j.spesialisasi FROM penilaian pen JOIN juri j ON pen.juri_id = j.id WHERE pen.peserta_id = ?");
$stmt_pen->execute([$peserta_id]);
$existing = $stmt_pen->fetchAll();

// Index existing by juri_id
$existing_map = [];
foreach ($existing as $e) {
    $existing_map[$e['juri_id']] = $e;
}

$success = '';
$error = '';

// Handle POST (simpan nilai)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        // Simpan / update nilai per juri
        $juri_id = (int)($_POST['juri_id'] ?? 0);
        $skor = floatval($_POST['skor'] ?? 0);
        $catatan = trim($_POST['catatan'] ?? '');

        if ($juri_id <= 0 || $skor < 0 || $skor > 100) {
            $error = 'Data tidak valid. Pastikan juri dipilih dan skor antara 0-100.';
        } else {
            if (isset($existing_map[$juri_id])) {
                // Update
                $upd = $pdo->prepare("UPDATE penilaian SET skor_spesialisasi = ?, catatan = ?, tanggal_penilaian = NOW() WHERE peserta_id = ? AND juri_id = ?");
                $upd->execute([$skor, $catatan, $peserta_id, $juri_id]);
            } else {
                // Insert
                $ins = $pdo->prepare("INSERT INTO penilaian (peserta_id, juri_id, skor_spesialisasi, catatan, tanggal_penilaian) VALUES (?, ?, ?, ?, NOW())");
                $ins->execute([$peserta_id, $juri_id, $skor, $catatan]);
            }
            $success = 'Nilai berhasil disimpan!';
            // Refresh existing
            $stmt_pen->execute([$peserta_id]);
            $existing = $stmt_pen->fetchAll();
            $existing_map = [];
            foreach ($existing as $e) {
                $existing_map[$e['juri_id']] = $e;
            }
        }
    } elseif ($action === 'delete_juri') {
        $juri_id = (int)($_POST['juri_id'] ?? 0);
        if ($juri_id > 0) {
            $del = $pdo->prepare("DELETE FROM penilaian WHERE peserta_id = ? AND juri_id = ?");
            $del->execute([$peserta_id, $juri_id]);
            $success = 'Nilai juri berhasil dihapus!';
            $stmt_pen->execute([$peserta_id]);
            $existing = $stmt_pen->fetchAll();
            $existing_map = [];
            foreach ($existing as $e) {
                $existing_map[$e['juri_id']] = $e;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Nilai Penilaian - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="card-title mb-0">
                                        <i class="fas fa-edit me-2"></i>Edit Nilai Penilaian
                                    </h2>
                                    <p class="text-muted mb-0">
                                        <strong><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></strong>
                                        &mdash; <?php echo htmlspecialchars($peserta['nama_sekolah']); ?>
                                        (NISN: <?php echo htmlspecialchars($peserta['nisn']); ?>)
                                    </p>
                                </div>
                                <a href="laporan_penilaian.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Form Tambah / Edit Nilai -->
                        <div class="col-md-5 mb-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah / Update Nilai</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="save">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pilih Juri</label>
                                            <select name="juri_id" id="juriSelect" class="form-select" required onchange="prefillSkor(this)">
                                                <option value="">-- Pilih Juri --</option>
                                                <?php foreach ($juri_list as $j): ?>
                                                    <option value="<?php echo $j['id']; ?>"
                                                        data-skor="<?php echo isset($existing_map[$j['id']]) ? $existing_map[$j['id']]['skor_spesialisasi'] : ''; ?>"
                                                        data-catatan="<?php echo isset($existing_map[$j['id']]) ? htmlspecialchars($existing_map[$j['id']]['catatan'] ?? '') : ''; ?>">
                                                        <?php echo htmlspecialchars($j['nama_lengkap']); ?>
                                                        (<?php echo htmlspecialchars($j['spesialisasi']); ?>)
                                                        <?php echo isset($existing_map[$j['id']]) ? ' ✓' : ''; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Skor (0 – 100)</label>
                                            <input type="number" name="skor" id="skorInput" class="form-control" min="0" max="100" step="0.1" placeholder="Contoh: 85.5" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Catatan (opsional)</label>
                                            <textarea name="catatan" id="catatanInput" class="form-control" rows="3" placeholder="Catatan penilaian..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-2"></i>Simpan Nilai
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Nilai yang Sudah Ada -->
                        <div class="col-md-7 mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Nilai Saat Ini</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Juri</th>
                                                    <th>Spesialisasi</th>
                                                    <th class="text-center">Skor</th>
                                                    <th>Catatan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (count($existing) > 0): ?>
                                                    <?php foreach ($existing as $pen): ?>
                                                        <tr>
                                                            <td><strong><?php echo htmlspecialchars($pen['nama_juri']); ?></strong></td>
                                                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($pen['spesialisasi']); ?></span></td>
                                                            <td class="text-center fw-bold <?php echo $pen['skor_spesialisasi'] >= 80 ? 'text-success' : ($pen['skor_spesialisasi'] >= 60 ? 'text-primary' : 'text-danger'); ?>">
                                                                <?php echo number_format($pen['skor_spesialisasi'], 1); ?>
                                                            </td>
                                                            <td><small class="text-muted"><?php echo htmlspecialchars($pen['catatan'] ?? '-'); ?></small></td>
                                                            <td class="text-center">
                                                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus nilai dari juri ini?');">
                                                                    <input type="hidden" name="action" value="delete_juri">
                                                                    <input type="hidden" name="juri_id" value="<?php echo $pen['juri_id']; ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted">
                                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                            Belum ada nilai untuk peserta ini.
                                                        </td>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function prefillSkor(sel) {
            const opt = sel.options[sel.selectedIndex];
            const skor = opt.dataset.skor;
            const catatan = opt.dataset.catatan;
            document.getElementById('skorInput').value = skor || '';
            document.getElementById('catatanInput').value = catatan || '';
        }
    </script>
</body>
</html>
