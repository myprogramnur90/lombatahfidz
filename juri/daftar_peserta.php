<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai juri
if (!isset($_SESSION['juri_id'])) {
    header('Location: ../auth/login_juri.php');
    exit();
}

$juri_id = $_SESSION['juri_id'];

// Keyword pencarian
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Helper pencarian multi-kata, nama, nisn, sekolah, kelas, dan nomor urut
if (!function_exists('buildPesertaSearchClause')) {
    function buildPesertaSearchClause($q, &$params) {
        $q = trim($q);
        if ($q === '') return '';
        $words = preg_split('/\s+/', $q);
        $clauses = [];
        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') continue;
            $sub = "(p.nama_lengkap LIKE ? OR p.nisn LIKE ? OR s.nama_sekolah LIKE ? OR p.kelas LIKE ?";
            $params[] = "%$word%";
            $params[] = "%$word%";
            $params[] = "%$word%";
            $params[] = "%$word%";
            $cleanNum = preg_replace('/^(?:no\.?|nomor|#)\s*/i', '', $word);
            if (is_numeric($cleanNum)) {
                $sub .= " OR p.no_urut = ?";
                $params[] = (int)$cleanNum;
            }
            $sub .= ")";
            $clauses[] = $sub;
        }
        return !empty($clauses) ? " AND (" . implode(" AND ", $clauses) . ")" : '';
    }
}

// Ambil daftar semua peserta yang diterima
$params_peserta = [$juri_id];
$search_clause = buildPesertaSearchClause($q, $params_peserta);

$query_peserta = "
    SELECT 
        p.*, 
        s.nama_sekolah,
        pen.id as penilaian_id,
        pen.skor_spesialisasi as skor_spesialisasi,
        pen.tanggal_penilaian
    FROM peserta p
    JOIN sekolah s ON p.sekolah_id = s.id
    LEFT JOIN penilaian pen ON p.id = pen.peserta_id AND pen.juri_id = ?
    WHERE p.status = 'Diterima' " . $search_clause . "
    ORDER BY CASE WHEN p.no_urut IS NULL OR p.no_urut = 0 THEN 1 ELSE 0 END, p.no_urut ASC, p.nama_lengkap ASC
";
$stmt_peserta = $pdo->prepare($query_peserta);
$stmt_peserta->execute($params_peserta);
$peserta_list = $stmt_peserta->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta - Juri</title>
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
                <i class="fas fa-list me-2"></i>
                Daftar Peserta
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
                        <h2 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            Daftar Semua Peserta
                        </h2>
                        <p class="text-muted mb-0">Daftar peserta yang telah diterima untuk dinilai</p>
                        <form class="mt-3" method="GET" action="daftar_peserta.php">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari nama, No. Urut, NISN, atau sekolah..." name="q" value="<?php echo htmlspecialchars($q); ?>">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                                <?php if ($q !== ''): ?>
                                    <a class="btn btn-secondary" href="daftar_peserta.php" title="Reset Pencarian"><i class="fas fa-times me-1"></i> Reset</a>
                                <?php endif; ?>
                            </div>
                        </form>
                        <?php if ($q !== ''): ?>
                            <div class="alert alert-info d-flex align-items-center justify-content-between mt-3 py-2 px-3 mb-0">
                                <div>
                                    <i class="fas fa-info-circle me-1"></i> Menampilkan hasil pencarian untuk "<strong><?php echo htmlspecialchars($q); ?></strong>" (Ditemukan: <?php echo count($peserta_list); ?> peserta)
                                </div>
                                <a href="daftar_peserta.php" class="btn btn-sm btn-outline-dark">Tampilkan Semua</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Peserta -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>
                    Data Peserta
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>NISN</th>
                                <th>Sekolah</th>
                                <th>Kelas</th>
                                <th>Status Penilaian</th>
                                <th>Skor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($peserta_list) > 0): ?>
                                <?php $no = 1; foreach ($peserta_list as $peserta): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php if (!empty($peserta['no_urut'])): ?>
                                                <span class="badge bg-primary me-1">No. <?php echo str_pad($peserta['no_urut'], 2, '0', STR_PAD_LEFT); ?></span>
                                            <?php endif; ?>
                                            <strong><?php echo htmlspecialchars($peserta['nama_lengkap']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo $peserta['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?> | 
                                                <?php echo date('d/m/Y', strtotime($peserta['tanggal_lahir'])); ?>
                                            </small>
                                        </td>
                                        <td><?php echo htmlspecialchars($peserta['nisn']); ?></td>
                                        <td><?php echo htmlspecialchars($peserta['nama_sekolah']); ?></td>
                                        <td><?php echo htmlspecialchars($peserta['kelas']); ?></td>
                                        <td>
                                            <?php if ($peserta['penilaian_id']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Sudah Dinilai
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Belum Dinilai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($peserta['penilaian_id']): ?>
                                                <span class="badge bg-primary">
                                                    <?php echo number_format($peserta['skor_spesialisasi'], 2); ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y', strtotime($peserta['tanggal_penilaian'])); ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="penilaian.php?id=<?php echo $peserta['id']; ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                <?php echo $peserta['penilaian_id'] ? 'Edit' : 'Nilai'; ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <?php if ($q !== ''): ?>
                                            <h5>Tidak Ada Peserta yang Cocok</h5>
                                            <p class="text-muted">Tidak ditemukan peserta dengan kata kunci "<strong><?php echo htmlspecialchars($q); ?></strong>".</p>
                                            <a href="daftar_peserta.php" class="btn btn-secondary btn-sm mt-2"><i class="fas fa-arrow-left me-1"></i> Tampilkan Semua Peserta</a>
                                        <?php else: ?>
                                            <h5>Belum ada peserta</h5>
                                            <p class="text-muted">Belum ada peserta yang diterima untuk dinilai.</p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

