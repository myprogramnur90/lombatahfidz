<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['musabaqoh_logged_in']) || !$_SESSION['musabaqoh_logged_in']) {
    header('Location: ../auth/login_musabaqoh.php');
    exit();
}

// Ambil jenis soal dari parameter
$jenis_soal = $_GET['jenis'] ?? 'penyisihan';
$jenis_display = ($jenis_soal === 'final') ? 'Final' : 'Penyisihan';
$jenis_color = ($jenis_soal === 'final') ? '#e74c3c' : '#3498db';
$jenis_icon = ($jenis_soal === 'final') ? 'fas fa-crown' : 'fas fa-trophy';

// Ambil data soal berdasarkan jenis
if ($jenis_soal === 'final') {
    $soalBelumTerpakai = getSoalFinalBelumTerpakai($pdo);
    $totalSoal = count($soalBelumTerpakai);
} else {
    $soalBelumTerpakai = getSoalBelumTerpakai($pdo);
    $totalSoal = count($soalBelumTerpakai);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musabaqoh Hifdzul Qur'an - Soal <?php echo $jenis_display; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-main {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .soal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        
        .soal-number {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 20px 10px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            text-decoration: none;
        }
        
        .soal-number:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .soal-number.used {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .controls {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, <?php echo $jenis_color; ?>, <?php echo $jenis_color; ?>dd);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            color: white;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .btn-reset:hover {
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        
        .btn-logout:hover {
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .stats {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .stat-item {
            display: inline-block;
            margin: 0 20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, <?php echo $jenis_color; ?>, <?php echo $jenis_color; ?>dd);
            color: white;
            border-radius: 20px;
            font-weight: bold;
        }
        
        
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .badge {
            font-size: 1.2em;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-main">
            <!-- Logout Button removed -->
            
            <!-- Header -->
            <div class="header">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="<?php echo $jenis_icon; ?> me-3"></i>Musabaqoh Hifdzul Qur'an</h1>
                    <a href="pilih_jenis.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <p>Sistem Pemilihan Soal Juz 30</p>
                <span class="badge" style="background-color: <?php echo $jenis_color; ?>; font-size: 1.2em; padding: 10px 20px;">
                    <i class="<?php echo $jenis_icon; ?> me-2"></i>Soal <?php echo $jenis_display; ?>
                </span>
            </div>
            
            <!-- Panel Pilih Peserta & Soal Aktif -->
            <div class="card mb-4" style="border: 2px solid <?php echo $jenis_color; ?>; border-radius: 15px; overflow: hidden;">
                <div class="card-header text-white py-3" style="background: linear-gradient(135deg, <?php echo $jenis_color; ?>, <?php echo $jenis_color; ?>cc);">
                    <h5 class="mb-0"><i class="fas fa-broadcast-tower me-2"></i>Peserta & Soal Aktif <small class="opacity-75">(Tampil di layar Juri)</small></h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-12">
                            <label class="form-label fw-bold"><i class="fas fa-user-graduate me-1"></i> Pilih Peserta</label>
                            <select id="selectPeserta" class="form-select form-select-lg" style="border-radius: 10px;">
                                <option value="">-- Pilih Peserta --</option>
                                <?php
                                try {
                                    $joinFinal = ($jenis_soal === 'final') ? "JOIN peserta_final pf ON p.id = pf.peserta_id" : "";
                                    
                                    // Hanya tampilkan peserta yang belum masuk di musabaqoh_aktif untuk sesi ini
                                    $stmtP = $pdo->prepare("
                                        SELECT p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
                                        FROM peserta p
                                        JOIN sekolah s ON p.sekolah_id = s.id
                                        $joinFinal
                                        WHERE p.status = 'Diterima'
                                          AND p.id NOT IN (SELECT peserta_id FROM musabaqoh_aktif WHERE jenis = ?)
                                        ORDER BY p.nama_lengkap
                                    ");
                                    $stmtP->execute([$jenis_soal]);
                                    $pesertaList = $stmtP->fetchAll();
                                    foreach ($pesertaList as $ps) {
                                        echo '<option value="' . $ps['id'] . '">' . htmlspecialchars($ps['nama_lengkap']) . ' - ' . htmlspecialchars($ps['nama_sekolah']) . '</option>';
                                    }
                                } catch (PDOException $e) {}
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- Status aktif saat ini -->
                    <div id="statusAktif" class="mt-3" style="display: none;">
                        <div class="alert mb-0" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border: none; border-radius: 10px;">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-success" style="font-size: 1em; padding: 8px 15px; animation: pulse 2s infinite;">
                                        <i class="fas fa-broadcast-tower me-1"></i> LIVE
                                    </span>
                                </div>
                                <div>
                                    <strong id="aktifNama" class="fs-5"></strong>
                                    <span class="text-muted ms-2" id="aktifSekolah"></span>
                                    <span class="badge bg-primary ms-2" id="aktifSoal" style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
            </style>
            
            <!-- Statistics -->
            <div class="stats">
                <div class="stat-item">
                    <i class="fas fa-list me-2"></i>
                    Total Soal: <?php echo $totalSoal; ?>
                </div>
                <div class="stat-item">
                    <i class="fas fa-check-circle me-2"></i>
                    Tersedia: <?php echo $totalSoal; ?>
                </div>
            </div>
            
            <!-- Soal Grid -->
            <div class="soal-grid">
                <?php if (empty($soalBelumTerpakai)): ?>
                    <div class="col-12 text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Semua soal sudah terpakai!</strong><br>
                            Silakan reset soal untuk menggunakannya kembali.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($soalBelumTerpakai as $soal): ?>
                        <a href="#" onclick="pilihSoalDanTampilkan(<?php echo $soal['no_soal']; ?>); return false;" class="soal-number">
                            <?php echo $soal['no_soal']; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Controls -->
            <div class="controls">
                <a href="pilih_jenis.php" class="btn btn-custom">
                    <i class="fas fa-home me-2"></i>Pilih Jenis Soal
                </a>
                <button class="btn btn-custom btn-reset" onclick="resetAll()">
                    <i class="fas fa-redo me-2"></i>Reset Semua
                </button>
                
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const jenisParam = '<?php echo $jenis_soal; ?>';

        function setAktif() {
            const pesertaId = document.getElementById('selectPeserta').value;
            
            if (!pesertaId) {
                alert('Pilih peserta terlebih dahulu!');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'set');
            formData.append('jenis', jenisParam);
            formData.append('peserta_id', pesertaId);

            fetch('api_aktif.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        // Hilangkan dari opsi dropdown
                        const pOpt = document.querySelector('#selectPeserta option[value="'+pesertaId+'"]');
                        if (pOpt) pOpt.remove();
                        // Reset pilihan
                        document.getElementById('selectPeserta').value = "";
                        
                        loadAktif();
                    } else {
                        alert('Gagal: ' + (data.message || 'Error'));
                    }
                })
                .catch(err => alert('Terjadi kesalahan!'));
        }

        function pilihSoalDanTampilkan(noSoal) {
            const pesertaId = document.getElementById('selectPeserta').value;
            
            if (!pesertaId) {
                alert('Silakan pilih peserta dari dropdown di atas terlebih dahulu!');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'set');
            formData.append('jenis', jenisParam);
            formData.append('peserta_id', pesertaId);
            formData.append('no_soal', noSoal);

            fetch('api_aktif.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        // Langsung tampilkan di layar soal operator
                        window.location.href = 'soal.php?nomor=' + noSoal + '&jenis=' + jenisParam;
                    } else {
                        alert('Gagal: ' + (data.message || 'Error'));
                    }
                })
                .catch(err => alert('Terjadi kesalahan!'));
        }

        function clearAktif() {
            if (!confirm('Kosongkan peserta aktif?')) return;
            
            const formData = new FormData();
            formData.append('action', 'clear');
            formData.append('jenis', jenisParam);

            fetch('api_aktif.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('statusAktif').style.display = 'none';
                })
                .catch(err => alert('Terjadi kesalahan!'));
        }

        function loadAktif() {
            fetch('api_aktif.php?action=get&jenis=' + jenisParam)
                .then(r => r.json())
                .then(data => {
                    const box = document.getElementById('statusAktif');
                    if (data.status === 'ok' && data.data) {
                        const d = data.data;
                        document.getElementById('aktifNama').textContent = d.nama_lengkap;
                        document.getElementById('aktifSekolah').textContent = '(' + d.nama_sekolah + ')';
                        
                        const soalBadge = document.getElementById('aktifSoal');
                        if (d.no_soal) {
                            soalBadge.textContent = 'Soal No. ' + d.no_soal;
                            soalBadge.style.display = 'inline';
                        } else {
                            soalBadge.style.display = 'none';
                        }
                        box.style.display = 'block';
                    } else {
                        box.style.display = 'none';
                    }
                })
                .catch(() => {});
        }

        // Load status saat halaman dibuka
        loadAktif();

        function resetAll() {
            if (confirm('Apakah Anda yakin ingin mereset semua soal?')) {
                window.location.href = 'reset.php?jenis=<?php echo $jenis_soal; ?>';
            }
        }
    </script>
</body>
</html>
