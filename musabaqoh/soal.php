<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['musabaqoh_logged_in']) || !$_SESSION['musabaqoh_logged_in']) {
    header('Location: ../auth/login_musabaqoh.php');
    exit();
}

$nomor_soal = $_GET['nomor'] ?? null;
$jenis_soal = $_GET['jenis'] ?? 'penyisihan';
$jenis_display = ($jenis_soal === 'final') ? 'Final' : 'Penyisihan';
$jenis_color = ($jenis_soal === 'final') ? '#e74c3c' : '#3498db';
$jenis_icon = ($jenis_soal === 'final') ? 'fas fa-crown' : 'fas fa-trophy';

if ($nomor_soal) {
    // Ambil soal berdasarkan nomor yang dipilih dan jenis
    if ($jenis_soal === 'final') {
        $query = "SELECT * FROM soal_musabaqoh_final WHERE no_soal = ? AND status = 'Aktif'";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nomor_soal]);
        $soal = $stmt->fetch();
        
        if ($soal) {
            // Tandai soal sebagai terpakai
            markSoalFinalTerpakai($pdo, $nomor_soal);
        }
    } else {
        $query = "SELECT * FROM soal_musabaqoh WHERE no_soal = ? AND status = 'Aktif'";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nomor_soal]);
        $soal = $stmt->fetch();
        
        if ($soal) {
            // Tandai soal sebagai terpakai
            markSoalTerpakai($pdo, $nomor_soal);
        }
    }
    
    if (!$soal) {
        header('Location: index.php?jenis=' . $jenis_soal);
        exit();
    }
} else {
    header('Location: index.php?jenis=' . $jenis_soal);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal <?php echo $jenis_display; ?> - Musabaqoh Hifdzul Qur'an</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #A8C0FF 0%, #C1D3FF 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-main {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.08);
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
        
        .soal-display {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .soal-item {
            background: linear-gradient(180deg, #f9fbff, #eef3ff);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid rgba(52, 152, 219, 0.5);
            cursor: pointer;
            transition: box-shadow .25s ease, transform .25s ease, background .3s ease;
        }
        
        .soal-item:last-child {
            margin-bottom: 0;
        }
        
        .soal-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.1em;
        }
        
        .soal-content {
            color: #34495e;
            line-height: 1.8;
            font-size: 2em;
            font-weight: 500;
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
            box-shadow: 0 6px 16px rgba(0,0,0,0.18);
            color: white;
        }
        
        .user-info {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .user-info h5 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .user-info p {
            color: #7f8c8d;
            margin: 0;
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

        /* Soft reveal */
        .hidden-soft { display: none; opacity: 0; transform: translateY(8px); }
        .hidden-soft.reveal { display: block; opacity: 1; transform: translateY(0); transition: opacity .35s ease, transform .35s ease; }
        .soal-item:hover { box-shadow: 0 8px 18px rgba(0,0,0,0.08); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-main">
            <!-- Logout Button -->
            <?php if ($jenis_soal !== 'penyisihan'): ?>
            <div class="logout-btn">
                <a href="../auth/logout.php" class="btn btn-outline-secondary">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Header -->
            <div class="header">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="<?php echo $jenis_icon; ?> me-3"></i>Musabaqoh Hifdzul Qur'an</h1>
                    <div class="text-end">
                        <a href="#" onclick="kembaliDanClear(); return false;" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <p>Sistem Pemilihan Soal Juz 30</p>
                <span class="badge" style="background-color: <?php echo $jenis_color; ?>; font-size: 1.2em; padding: 10px 20px;">
                    <i class="<?php echo $jenis_icon; ?> me-2"></i>Soal <?php echo $jenis_display; ?>
                </span>
            </div>
            
            <!-- User Info -->
            <div class="user-info">
                <h5><i class="fas fa-user me-2"></i>Selamat Datang</h5>
                <p>Login sebagai: <strong><?php echo htmlspecialchars($_SESSION['musabaqoh_nama'] ?? $_SESSION['musabaqoh_user']); ?></strong></p>
            </div>
            
            <!-- Soal Display -->
            <div class="soal-display">
                <h3 class="mb-4"><i class="fas fa-list-ol me-2"></i>Soal Nomor <?php echo $soal['no_soal']; ?> - Juz 30:</h3>
                
                <?php if ($jenis_soal === 'final'): ?>
                    <!-- Soal Final: Sambung Ayat, Tebak Surah, Sambung Surah -->
                    <div class="soal-item" data-step="1">
                        <div class="soal-title">
                            <span class="badge bg-info me-2">
                                <i class="fas fa-arrow-right"></i>
                                Soal 1
                            </span>
                            (Sambung Ayat)
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal1']); ?>
                        </div>
                        <div class="text-muted small mt-2">Klik untuk menampilkan Soal 2</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" data-step="2">
                        <div class="soal-title">
                            <span class="badge bg-success me-2">
                                <i class="fas fa-book"></i>
                                Soal 2
                            </span>
                            (Tebak Surat)
                        </div>
                        <div class="soal-content">
                            <?php
                                $soal2Text = is_string($soal['soal2']) ? $soal['soal2'] : '';
                                // Normalisasi label yang salah pada konten: ganti leading "Sambung Ayat -" menjadi "Tebak Surat -"
                                $soal2Text = preg_replace('/^\s*Sambung\s*Ayat\s*-\s*/i', 'Tebak Surat - ', $soal2Text);
                                echo htmlspecialchars($soal2Text);
                            ?>
                        </div>
                        <div class="text-muted small mt-2">Klik untuk menampilkan Soal 3</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" data-step="3">
                        <div class="soal-title">
                            <span class="badge bg-warning me-2">
                                <i class="fas fa-exchange-alt"></i>
                                Soal 3
                            </span>
                            (Sambung Surah)
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal3']); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Soal Penyisihan: 2 Sambung Ayat + 1 Sambung Surat -->
                    <div class="soal-item" data-step="1">
                        <div class="soal-title">
                            <span class="badge bg-info me-2">
                                <i class="fas fa-arrow-right"></i>
                                Soal 1
                            </span>
                            (Sambung Ayat)
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal1']); ?>
                        </div>
                        <div class="text-muted small mt-2">Klik untuk menampilkan Soal 2</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" data-step="2">
                        <div class="soal-title">
                            <span class="badge bg-info me-2">
                                <i class="fas fa-arrow-right"></i>
                                Soal 2
                            </span>
                            (Sambung Ayat)
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal2']); ?>
                        </div>
                        <div class="text-muted small mt-2">Klik untuk menampilkan Soal 3</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" data-step="3">
                        <div class="soal-title">
                            <span class="badge bg-warning me-2">
                                <i class="fas fa-exchange-alt"></i>
                                Soal 3
                            </span>
                            (Sambung Surat)
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal3']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Controls -->
            <div class="controls">
                <a href="#" onclick="kembaliDanClear(); return false;" class="btn btn-custom">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Pilih Soal
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Progressive reveal: klik Soal 1 -> tampilkan Soal 2, klik Soal 2 -> tampilkan Soal 3
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.soal-item[data-step]');
            items.forEach(function(item) {
                item.addEventListener('click', function() {
                    const step = parseInt(item.getAttribute('data-step'));
                    const next = document.querySelector('.soal-item[data-step="' + (step + 1) + '"]');
                    if (next && next.classList.contains('hidden-soft')) {
                        // trigger soft reveal
                        requestAnimationFrame(() => {
                            next.classList.add('reveal');
                            next.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    }
                });
            });
        });

        function kembaliDanClear() {
            const formData = new FormData();
            formData.append('action', 'clear');
            formData.append('jenis', '<?php echo $jenis_soal; ?>');

            fetch('api_aktif.php', { method: 'POST', body: formData })
                .then(() => {
                    window.location.href = 'index.php?jenis=<?php echo $jenis_soal; ?>';
                })
                .catch(() => {
                    window.location.href = 'index.php?jenis=<?php echo $jenis_soal; ?>';
                });
        }
    </script>
</body>
</html>

