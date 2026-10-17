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

// Cek data peserta aktif untuk sesi ini
$pesertaAktif = null;
try {
    $stmtAktif = $pdo->prepare("
        SELECT p.*, s.nama_sekolah 
        FROM musabaqoh_aktif ma
        JOIN peserta p ON ma.peserta_id = p.id
        JOIN sekolah s ON p.sekolah_id = s.id
        WHERE ma.jenis = ? AND ma.status = 'Aktif'
        ORDER BY ma.updated_at DESC LIMIT 1
    ");
    $stmtAktif->execute([$jenis_soal]);
    $pesertaAktif = $stmtAktif->fetch();
} catch (Exception $e) {}

// Ambil pengaturan bel musabaqoh dari admin
$belJedaSetting = (int)getPengaturan('bel_jeda_teguran');
if ($belJedaSetting <= 0) $belJedaSetting = 8;
$belBunyiSetting = getPengaturan('bel_jenis_bunyi');
if (empty($belBunyiSetting)) $belBunyiSetting = 'kuningan';
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
            background: rgba(255, 255, 255, 0.94);
            border-radius: 20px;
            box-shadow: 0 14px 28px rgba(0,0,0,0.08);
            margin: 20px auto;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .soal-item {
            background: linear-gradient(180deg, #f9fbff, #eef3ff);
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 22px;
            border-left: 6px solid rgba(52, 152, 219, 0.5);
            cursor: pointer;
            transition: all .25s ease;
            position: relative;
        }
        
        .soal-item:last-child {
            margin-bottom: 0;
        }
        
        .soal-item.active-soal {
            border-left: 6px solid #198754 !important;
            background: linear-gradient(180deg, #f0fff4 0%, #e6fced 100%) !important;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.25), 0 8px 24px rgba(0,0,0,0.08) !important;
        }
        
        .soal-item.completed-soal {
            border-left: 6px solid #6c757d !important;
            opacity: 0.9;
        }
        
        .soal-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 12px;
            font-size: 1.15em;
        }
        
        .soal-content {
            color: #2c3e50;
            line-height: 1.9;
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
        
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        
        .badge {
            font-size: 1.1em;
            padding: 8px 16px;
        }

        /* Soft reveal */
        .hidden-soft { display: none; opacity: 0; transform: translateY(8px); }
        .hidden-soft.reveal { display: block; opacity: 1; transform: translateY(0); transition: opacity .35s ease, transform .35s ease; }
        .soal-item:hover { box-shadow: 0 8px 18px rgba(0,0,0,0.08); transform: translateY(-2px); }

        /* Panel Bel Styling */
        .bel-panel-card {
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 2px solid #5a78d1;
            overflow: hidden;
            background: white;
        }
        
        .btn-bel {
            border: none;
            border-radius: 14px;
            color: white;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .btn-bel:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.22);
            color: white;
        }
        .btn-bel:active, .btn-bel.ringing {
            transform: scale(0.96);
            box-shadow: 0 0 0 5px rgba(255, 255, 255, 0.7);
        }
        
        .btn-bel-start {
            background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);
        }
        .btn-bel-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        .btn-bel-next {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
        }
        .btn-bel-finish {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
        }
        
        .ring-indicator {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #28a745;
            box-shadow: 0 0 0 rgba(40, 167, 69, 0.4);
            animation: pulse-ring 1.5s infinite;
        }
        
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="mb-0"><i class="<?php echo $jenis_icon; ?> me-2 text-warning"></i>Musabaqoh Hifdzul Qur'an</h1>
                    <div class="text-end">
                        <a href="#" onclick="kembaliDanClear(); return false;" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <p class="mb-2">Sistem Pemilihan Soal & Protokol Bel MHQ Juz 30</p>
                <span class="badge" style="background-color: <?php echo $jenis_color; ?>; font-size: 1.1em; padding: 8px 18px;">
                    <i class="<?php echo $jenis_icon; ?> me-2"></i>Soal <?php echo $jenis_display; ?>
                </span>
            </div>

            <!-- Informasi Peserta Aktif (Jika Ada) -->
            <?php if ($pesertaAktif): ?>
            <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4" style="border-radius: 14px; border-left: 6px solid #667eea !important;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Peserta Tampil:</div>
                        <h5 class="mb-0 fw-bold">
                            <?php if (!empty($pesertaAktif['no_urut'])): ?>
                                <span class="badge bg-primary me-1">No. <?php echo str_pad($pesertaAktif['no_urut'], 2, '0', STR_PAD_LEFT); ?></span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($pesertaAktif['nama_lengkap']); ?>
                        </h5>
                        <small class="text-muted"><?php echo htmlspecialchars($pesertaAktif['nama_sekolah']); ?> | NISN: <?php echo htmlspecialchars($pesertaAktif['nisn']); ?></small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-success py-2 px-3 fs-6"><i class="fas fa-broadcast-tower me-1"></i>Sesi Aktif</span>
                </div>
            </div>
            <?php endif; ?>

            <!-- =============================================================== -->
            <!-- TAMPILAN SOAL JUZ 30 -->
            <!-- =============================================================== -->
            <div class="soal-display">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h3 class="mb-0"><i class="fas fa-list-ol me-2 text-primary"></i>Soal Nomor <?php echo $soal['no_soal']; ?> - Juz 30</h3>
                    <span class="badge bg-primary fs-6">3 Soal</span>
                </div>
                
                <?php if ($jenis_soal === 'final'): ?>
                    <!-- Soal Final: Sambung Ayat, Tebak Surah, Sambung Surah -->
                    <div class="soal-item" id="soalItem1" data-step="1">
                        <div class="soal-title d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-arrow-right"></i>
                                    Soal 1
                                </span>
                                (Sambung Ayat)
                            </div>
                            <span class="badge bg-light text-dark status-badge-item" id="badgeSoal1">Menunggu</span>
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal1']); ?>
                        </div>
                        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Tekan Bel 3x untuk pindah ke Soal 2</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" id="soalItem2" data-step="2">
                        <div class="soal-title d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-book"></i>
                                    Soal 2
                                </span>
                                (Tebak Surat)
                            </div>
                            <span class="badge bg-light text-dark status-badge-item" id="badgeSoal2">Menunggu</span>
                        </div>
                        <div class="soal-content">
                            <?php
                                $soal2Text = is_string($soal['soal2']) ? $soal['soal2'] : '';
                                $soal2Text = preg_replace('/^\s*Sambung\s*Ayat\s*-\s*/i', 'Tebak Surat - ', $soal2Text);
                                echo htmlspecialchars($soal2Text);
                            ?>
                        </div>
                        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Tekan Bel 3x untuk pindah ke Soal 3</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" id="soalItem3" data-step="3">
                        <div class="soal-title d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-exchange-alt"></i>
                                    Soal 3
                                </span>
                                (Sambung Surah)
                            </div>
                            <span class="badge bg-light text-dark status-badge-item" id="badgeSoal3">Menunggu</span>
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal3']); ?>
                        </div>
                        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Tekan Bel 4x untuk mengakhiri lomba</div>
                    </div>
                <?php else: ?>
                    <!-- Soal Penyisihan: 2 Sambung Ayat + 1 Sambung Surat -->
                    <div class="soal-item" id="soalItem1" data-step="1">
                        <div class="soal-title d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-arrow-right"></i>
                                    Soal 1
                                </span>
                                (Sambung Ayat)
                            </div>
                            <span class="badge bg-light text-dark status-badge-item" id="badgeSoal1">Menunggu</span>
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal1']); ?>
                        </div>
                        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Tekan Bel 3x untuk pindah ke Soal 2</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" id="soalItem2" data-step="2">
                        <div class="soal-title d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-arrow-right"></i>
                                    Soal 2
                                </span>
                                (Sambung Ayat)
                            </div>
                            <span class="badge bg-light text-dark status-badge-item" id="badgeSoal2">Menunggu</span>
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal2']); ?>
                        </div>
                        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Tekan Bel 3x untuk pindah ke Soal 3</div>
                    </div>
                    
                    <div class="soal-item hidden-soft" id="soalItem3" data-step="3">
                        <div class="soal-title d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-exchange-alt"></i>
                                    Soal 3
                                </span>
                                (Sambung Surat)
                            </div>
                            <span class="badge bg-light text-dark status-badge-item" id="badgeSoal3">Menunggu</span>
                        </div>
                        <div class="soal-content">
                            <?php echo htmlspecialchars($soal['soal3']); ?>
                        </div>
                        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Tekan Bel 4x untuk mengakhiri lomba</div>
                    </div>
                <?php endif; ?>
            </div>
            
<!-- =============================================================== -->
            <!-- PANEL KONTROL BEL MUSABAQOH (PROTOKOL RESMI MHQ) -->
            <!-- =============================================================== -->
            <div class="card bel-panel-card mb-4">
                <div class="card-header py-3 text-white d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bell fa-lg me-2 text-warning"></i>
                        <h5 class="mb-0 fw-bold">Kontrol Bel Musabaqoh</h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark d-none d-md-inline-block">
                            <i class="fas fa-keyboard me-1 text-primary"></i>Shortcut Keyboard: [1] [2] [3] [4] [Spasi]
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="testSound()" title="Uji Suara Bel">
                            <i class="fas fa-volume-up me-1"></i>Uji Suara
                        </button>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    <!-- Status Bar & Pengaturan Jeda -->
                    <div class="row align-items-center mb-3 pb-3 border-bottom g-2">
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center">
                                <span class="ring-indicator me-2" id="ringIndicator"></span>
                                <div>
                                    <span class="text-muted small">Status Bel / Alur:</span>
                                    <div id="belStatusText" class="fw-bold fs-6 text-primary">Menunggu lomba dimulai... (Tekan Bel 2x)</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-md-end align-items-center gap-3 flex-wrap">
                                <div class="d-flex align-items-center">
                                    <label for="selectBunyi" class="text-muted small me-2 text-nowrap"><i class="fas fa-music me-1 text-info"></i>Bunyi Bel:</label>
                                    <select id="selectBunyi" class="form-select form-select-sm w-auto">
                                        <option value="kuningan" <?php echo $belBunyiSetting === 'kuningan' ? 'selected' : ''; ?>>Lonceng Kuningan</option>
                                        <option value="digital" <?php echo $belBunyiSetting === 'digital' ? 'selected' : ''; ?>>Bel Digital</option>
                                        <option value="gong" <?php echo $belBunyiSetting === 'gong' ? 'selected' : ''; ?>>Gong</option>
                                        <option value="tabung" <?php echo $belBunyiSetting === 'tabung' ? 'selected' : ''; ?>>Lonceng Tabung</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-center">
                                    <label for="selectJeda" class="text-muted small me-2 text-nowrap"><i class="fas fa-stopwatch me-1 text-warning"></i>Jeda Teguran:</label>
                                    <select id="selectJeda" class="form-select form-select-sm w-auto">
                                        <?php
                                        $optionsJeda = [5, 8, 10, 15];
                                        if (!in_array($belJedaSetting, $optionsJeda)) {
                                            $optionsJeda[] = $belJedaSetting;
                                            sort($optionsJeda);
                                        }
                                        foreach ($optionsJeda as $detik) {
                                            $sel = ($detik == $belJedaSetting) ? 'selected' : '';
                                            echo "<option value=\"$detik\" $sel>{$detik} Detik</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <span id="activeSoalBadge" class="badge bg-secondary py-2 px-3">Belum Mulai</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Tombol Protokol Bel -->
                    <div class="row g-2 text-center">
                        <!-- 1. Bel 2x: Mulai Lomba -->
                        <div class="col-6 col-md-3">
                            <button type="button" class="btn btn-bel btn-bel-start w-100 p-3 h-100" id="btnBel2" onclick="triggerBel2()">
                                <div class="mb-1"><i class="fas fa-play-circle fa-2x"></i></div>
                                <div class="fw-bold fs-5">Bel 2x</div>
                                <div class="small text-white-50">Lomba Dimulai</div>
                                <div class="badge bg-white text-dark mt-2 shadow-sm">Tombol [2]</div>
                            </button>
                        </div>

                        <!-- 2. Bel 1x: Teguran Kesalahan -->
                        <div class="col-6 col-md-3">
                            <button type="button" class="btn btn-bel btn-bel-warning w-100 p-3 h-100" id="btnBel1" onclick="triggerBel1()">
                                <div class="mb-1"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                                <div class="fw-bold fs-5">Bel 1x</div>
                                <div class="small text-white-50">Tegur Kesalahan (+ Jeda)</div>
                                <div class="badge bg-white text-dark mt-2 shadow-sm">Tombol [1]</div>
                            </button>
                        </div>

                        <!-- 3. Bel 3x: Pindah Soal -->
                        <div class="col-6 col-md-3">
                            <button type="button" class="btn btn-bel btn-bel-next w-100 p-3 h-100" id="btnBel3" onclick="triggerBel3()">
                                <div class="mb-1"><i class="fas fa-step-forward fa-2x"></i></div>
                                <div class="fw-bold fs-5">Bel 3x</div>
                                <div class="small text-white-50">Perpindahan Soal</div>
                                <div class="badge bg-white text-dark mt-2 shadow-sm">Tombol [3]</div>
                            </button>
                        </div>

                        <!-- 4. Bel 4x: Lomba Berakhir -->
                        <div class="col-6 col-md-3">
                            <button type="button" class="btn btn-bel btn-bel-finish w-100 p-3 h-100" id="btnBel4" onclick="triggerBel4()">
                                <div class="mb-1"><i class="fas fa-flag-checkered fa-2x"></i></div>
                                <div class="fw-bold fs-5">Bel 4x</div>
                                <div class="small text-white-50">Lomba Berakhir</div>
                                <div class="badge bg-white text-dark mt-2 shadow-sm">Tombol [4]</div>
                            </button>
                        </div>
                    </div>

                    <!-- Kotak Animasi Countdown Jeda Waktu Kesalahan (Aktif saat Bel 1x) -->
                    <div id="jedaCountdownBox" class="mt-3 p-3 rounded-3 shadow-sm" style="display: none; background: #fff8e1; border: 2px solid #ffc107;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <div class="spinner-grow text-warning me-3" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-stopwatch me-1 text-danger"></i> Jeda Waktu Kesempatan Memperbaiki Hafalan (Tawaqquf)</h6>
                                    <small class="text-muted">Jika waktu habis, sistem otomatis membunyikan Bel 3x & lanjut ke soal berikutnya.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="fs-2 fw-bold text-danger font-monospace px-2" id="countdownTimer">08s</div>
                                <button type="button" class="btn btn-success fw-bold px-3 py-2" onclick="pesertaMelanjutkan()">
                                    <i class="fas fa-check me-1"></i> Peserta Lanjut <span class="badge bg-light text-dark ms-1">[Spasi]</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelCountdown()">
                                    Batal
                                </button>
                            </div>
                        </div>
                        <div class="progress mt-2" style="height: 10px;">
                            <div id="countdownProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%;"></div>
                        </div>
                    </div>

                    <!-- Notifikasi Selesai -->
                    <div id="finishNoticeBox" class="alert alert-success mt-3 mb-0 text-center py-3" style="display: none; border-radius: 12px;">
                        <h5 class="mb-1 fw-bold"><i class="fas fa-check-circle me-2 text-success"></i>Alhamdulillah, Seluruh Soal Telah Selesai Dibacakan!</h5>
                        <p class="mb-0 text-muted">Peserta menghentikan jawaban terakhir. Silakan kembali untuk memilih nomor soal berikutnya.</p>
                    </div>
                </div>
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
        // =========================================================
        // WEB AUDIO API: SYNTHESIS BUNYI BEL LOGAM KHAS MHQ
        // =========================================================
        let audioCtx = null;

        function getAudioContext() {
            if (!audioCtx) {
                const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                if (AudioContextClass) {
                    audioCtx = new AudioContextClass();
                }
            }
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            return audioCtx;
        }

        // Mainkan 1 denting bel sesuai pilihan suara
        function playBellDing() {
            const ctx = getAudioContext();
            if (!ctx) return;
            const now = ctx.currentTime;
            const soundType = document.getElementById('selectBunyi') ? document.getElementById('selectBunyi').value : 'kuningan';

            if (soundType === 'digital') {
                // Bel Digital / Ting Elektronik
                const partials = [1760, 2640, 3520];
                const master = ctx.createGain();
                master.gain.setValueAtTime(0.85, now);
                master.connect(ctx.destination);

                partials.forEach((f, i) => {
                    const osc = ctx.createOscillator();
                    const g = ctx.createGain();
                    osc.type = i === 0 ? 'sine' : 'triangle';
                    osc.frequency.setValueAtTime(f, now);
                    g.gain.setValueAtTime(0.001, now);
                    g.gain.linearRampToValueAtTime(0.4 / (i + 1), now + 0.002);
                    g.gain.exponentialRampToValueAtTime(0.0001, now + 0.55);
                    osc.connect(g);
                    g.connect(master);
                    osc.start(now);
                    osc.stop(now + 0.6);
                });
            } else if (soundType === 'gong') {
                // Nada Gong / Singing Bell: frekuensi rendah & berwibawa
                const freqs = [440, 554.37, 659.25, 880];
                const master = ctx.createGain();
                master.gain.setValueAtTime(0.9, now);
                master.connect(ctx.destination);

                freqs.forEach((f, i) => {
                    const osc = ctx.createOscillator();
                    const g = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(f, now);
                    g.gain.setValueAtTime(0.001, now);
                    g.gain.linearRampToValueAtTime(0.5 / (i + 1), now + 0.008);
                    g.gain.exponentialRampToValueAtTime(0.0001, now + 2.2);
                    osc.connect(g);
                    g.connect(master);
                    osc.start(now);
                    osc.stop(now + 2.3);
                });
            } else if (soundType === 'tabung') {
                // Lonceng Tabung / Tubular Chime: merdu bertingkat
                const freqs = [1318.5, 1760, 2637];
                const master = ctx.createGain();
                master.gain.setValueAtTime(0.9, now);
                master.connect(ctx.destination);

                freqs.forEach((f, i) => {
                    const osc = ctx.createOscillator();
                    const g = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(f, now);
                    g.gain.setValueAtTime(0.001, now);
                    g.gain.linearRampToValueAtTime(0.45 / (i + 1), now + 0.005);
                    g.gain.exponentialRampToValueAtTime(0.0001, now + 1.8);
                    osc.connect(g);
                    g.connect(master);
                    osc.start(now);
                    osc.stop(now + 1.9);
                });
            } else {
                // Default: Lonceng Kuningan / Brass Bell Klasik MHQ
                const partials = [
                    { f: 1046.5, g: 0.70, d: 1.6 },
                    { f: 1568.0, g: 0.45, d: 1.3 },
                    { f: 2093.0, g: 0.35, d: 1.0 },
                    { f: 2637.0, g: 0.22, d: 0.8 },
                    { f: 3136.0, g: 0.15, d: 0.6 }
                ];

                const masterGain = ctx.createGain();
                masterGain.gain.setValueAtTime(0.95, now);
                masterGain.connect(ctx.destination);

                try {
                    const bufferSize = Math.floor(ctx.sampleRate * 0.015);
                    const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
                    const data = buffer.getChannelData(0);
                    for (let i = 0; i < bufferSize; i++) {
                        data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.25));
                    }
                    const noise = ctx.createBufferSource();
                    noise.buffer = buffer;
                    const noiseGain = ctx.createGain();
                    noiseGain.gain.setValueAtTime(0.35, now);
                    noiseGain.connect(masterGain);
                    noise.start(now);
                } catch(e) {}

                partials.forEach((p, idx) => {
                    const osc = ctx.createOscillator();
                    const gainNode = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(p.f, now);
                    osc.detune.setValueAtTime((idx - 2) * 5, now);

                    gainNode.gain.setValueAtTime(0.001, now);
                    gainNode.gain.linearRampToValueAtTime(p.g, now + 0.004);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, now + p.d);

                    osc.connect(gainNode);
                    gainNode.connect(masterGain);

                    osc.start(now);
                    osc.stop(now + p.d + 0.05);
                });
            }
        }

        // Bunyikan bel N kali berurutan
        let isRinging = false;
        async function ringBellSequence(count, interval = 580) {
            getAudioContext();
            isRinging = true;

            const indicator = document.getElementById('ringIndicator');
            if (indicator) {
                indicator.style.background = '#e74c3c';
            }

            for (let i = 0; i < count; i++) {
                playBellDing();
                if (i < count - 1) {
                    await new Promise(r => setTimeout(r, interval));
                }
            }

            if (indicator) {
                indicator.style.background = '#28a745';
            }
            isRinging = false;
        }

        // Uji suara bel (1x)
        function testSound() {
            ringBellSequence(1);
            showStatus("Tes Suara Bel: 1x Denting");
        }

        // =========================================================
        // LOGIKA ALUR PROTOKOL BEL & JEDA WAKTU
        // =========================================================
        let currentStep = 0; // 0: Belum mulai, 1: Soal 1, 2: Soal 2, 3: Soal 3, 4: Selesai
        let errorCount = 0;
        let countdownTimerId = null;
        let countdownRemaining = 0;
        let countdownTotal = <?php echo $belJedaSetting; ?>;

        function showStatus(text, type = 'primary') {
            const el = document.getElementById('belStatusText');
            if (el) {
                el.innerText = text;
                el.className = `fw-bold fs-6 text-${type}`;
            }
        }

        function updateActiveBadge() {
            const badge = document.getElementById('activeSoalBadge');
            if (!badge) return;

            if (currentStep === 0) {
                badge.className = 'badge bg-secondary py-2 px-3';
                badge.innerText = 'Belum Mulai';
            } else if (currentStep === 1) {
                badge.className = 'badge bg-success py-2 px-3';
                badge.innerHTML = '<i class="fas fa-play me-1"></i> Soal 1 Aktif';
            } else if (currentStep === 2) {
                badge.className = 'badge bg-success py-2 px-3';
                badge.innerHTML = '<i class="fas fa-play me-1"></i> Soal 2 Aktif';
            } else if (currentStep === 3) {
                badge.className = 'badge bg-success py-2 px-3';
                badge.innerHTML = '<i class="fas fa-play me-1"></i> Soal 3 Aktif';
            } else if (currentStep >= 4) {
                badge.className = 'badge bg-danger py-2 px-3';
                badge.innerHTML = '<i class="fas fa-flag-checkered me-1"></i> Selesai';
            }
        }

        function highlightSoal(step) {
            for (let i = 1; i <= 3; i++) {
                const item = document.getElementById(`soalItem${i}`);
                const badge = document.getElementById(`badgeSoal${i}`);
                if (!item) continue;

                if (i < step) {
                    item.classList.remove('active-soal');
                    item.classList.add('completed-soal');
                    if (badge) {
                        badge.className = 'badge bg-secondary status-badge-item';
                        badge.innerText = 'Selesai';
                    }
                } else if (i === step) {
                    item.classList.remove('completed-soal', 'hidden-soft');
                    item.classList.add('reveal', 'active-soal');
                    if (badge) {
                        badge.className = 'badge bg-success status-badge-item';
                        badge.innerHTML = '<i class="fas fa-microphone-alt me-1"></i>Sedang Dibaca';
                    }
                    item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    item.classList.remove('active-soal', 'completed-soal');
                    if (badge) {
                        badge.className = 'badge bg-light text-dark status-badge-item';
                        badge.innerText = 'Menunggu';
                    }
                }
            }
        }

        // 1. BEL 2X: Tanda Lomba Dimulai (Juri membacakan Soal 1)
        function triggerBel2() {
            cancelCountdown();
            const btn = document.getElementById('btnBel2');
            btn.classList.add('ringing');
            setTimeout(() => btn.classList.remove('ringing'), 1200);

            ringBellSequence(2);

            currentStep = 1;
            errorCount = 0;
            updateActiveBadge();
            highlightSoal(1);
            showStatus("Bel 2x: Lomba dimulai! Juri membacakan Soal 1.", "success");

            const finishBox = document.getElementById('finishNoticeBox');
            if (finishBox) finishBox.style.display = 'none';
        }

        // 2. BEL 1X: Menegur Kesalahan Peserta (+ Jeda Waktu Otomatis)
        function triggerBel1() {
            if (currentStep === 0) {
                currentStep = 1;
                updateActiveBadge();
                highlightSoal(1);
            }
            if (currentStep > 3) return;

            const btn = document.getElementById('btnBel1');
            btn.classList.add('ringing');
            setTimeout(() => btn.classList.remove('ringing'), 600);

            ringBellSequence(1);
            errorCount++;

            showStatus(`Bel 1x: Teguran kesalahan #${errorCount} pada Soal ${currentStep}. Menghitung jeda kesempatan...`, "warning");
            startCountdown();
        }

        // Mulai Countdown Jeda Waktu
        function startCountdown() {
            cancelCountdown();

            const selectEl = document.getElementById('selectJeda');
            countdownTotal = parseInt(selectEl.value) || 8;
            countdownRemaining = countdownTotal;

            const box = document.getElementById('jedaCountdownBox');
            const timerText = document.getElementById('countdownTimer');
            const bar = document.getElementById('countdownProgressBar');

            box.style.display = 'block';
            timerText.innerText = (countdownRemaining < 10 ? '0' : '') + countdownRemaining + 's';
            bar.style.width = '100%';
            bar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';

            const startTime = Date.now();
            const durationMs = countdownTotal * 1000;

            countdownTimerId = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const remainingMs = Math.max(0, durationMs - elapsed);
                countdownRemaining = Math.ceil(remainingMs / 1000);

                timerText.innerText = (countdownRemaining < 10 ? '0' : '') + countdownRemaining + 's';
                const percent = (remainingMs / durationMs) * 100;
                bar.style.width = percent + '%';

                if (countdownRemaining <= 3) {
                    bar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-danger';
                }

                if (remainingMs <= 0) {
                    clearInterval(countdownTimerId);
                    countdownTimerId = null;
                    onCountdownTimeout();
                }
            }, 100);
        }

        // Saat waktu jeda habis -> otomatis bel perpindahan soal (Bel 3x atau Bel 4x)
        function onCountdownTimeout() {
            const box = document.getElementById('jedaCountdownBox');
            box.style.display = 'none';

            showStatus(`Waktu jeda habis! Otomatis lanjut ke soal berikutnya...`, "danger");

            if (currentStep === 1 || currentStep === 2) {
                // Bunyikan Bel 3x & lanjut soal berikutnya
                triggerBel3();
            } else if (currentStep === 3) {
                // Soal terakhir habis -> Lomba berakhir (Bel 4x)
                triggerBel4();
            }
        }

        // Peserta berhasil melanjutkan bacaan sebelum waktu habis
        function pesertaMelanjutkan() {
            if (countdownTimerId) {
                cancelCountdown();
                showStatus(`Peserta melanjutkan bacaan (Soal ${currentStep}).`, "success");
            }
        }

        // Batal Countdown
        function cancelCountdown() {
            if (countdownTimerId) {
                clearInterval(countdownTimerId);
                countdownTimerId = null;
            }
            const box = document.getElementById('jedaCountdownBox');
            if (box) box.style.display = 'none';
        }

        // 3. BEL 3X: Perpindahan Soal (Soal 1 -> 2, Soal 2 -> 3)
        function triggerBel3() {
            cancelCountdown();
            const btn = document.getElementById('btnBel3');
            btn.classList.add('ringing');
            setTimeout(() => btn.classList.remove('ringing'), 1800);

            ringBellSequence(3);

            if (currentStep === 0 || currentStep === 1) {
                currentStep = 2;
                errorCount = 0;
                updateActiveBadge();
                highlightSoal(2);
                showStatus("Bel 3x: Perpindahan Soal -> Beralih ke Soal 2.", "info");
            } else if (currentStep === 2) {
                currentStep = 3;
                errorCount = 0;
                updateActiveBadge();
                highlightSoal(3);
                showStatus("Bel 3x: Perpindahan Soal -> Beralih ke Soal 3 (Terakhir).", "info");
            } else if (currentStep === 3) {
                showStatus("Sudah di Soal 3 (Terakhir). Tekan Bel 4x untuk mengakhiri lomba.", "warning");
            }
        }

        // 4. BEL 4X: Tanda Lomba Berakhir (Peserta menghentikan jawaban terakhir)
        function triggerBel4() {
            cancelCountdown();
            const btn = document.getElementById('btnBel4');
            btn.classList.add('ringing');
            setTimeout(() => btn.classList.remove('ringing'), 2400);

            ringBellSequence(4);

            currentStep = 4;
            updateActiveBadge();
            highlightSoal(4);
            showStatus("Bel 4x: Lomba Berakhir! Peserta menghentikan jawaban terakhir.", "danger");

            const finishBox = document.getElementById('finishNoticeBox');
            if (finishBox) {
                finishBox.style.display = 'block';
                finishBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // =========================================================
        // KEYBOARD SHORTCUTS
        // =========================================================
        document.addEventListener('keydown', function(e) {
            // Abaikan jika user sedang mengetik di input form
            const tag = e.target.tagName.toLowerCase();
            if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

            if (e.key === '1') {
                e.preventDefault();
                triggerBel1();
            } else if (e.key === '2') {
                e.preventDefault();
                triggerBel2();
            } else if (e.key === '3') {
                e.preventDefault();
                triggerBel3();
            } else if (e.key === '4') {
                e.preventDefault();
                triggerBel4();
            } else if (e.code === 'Space') {
                e.preventDefault();
                if (countdownTimerId) {
                    pesertaMelanjutkan();
                } else {
                    triggerBel1();
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelCountdown();
            }
        });

        // =========================================================
        // KLIK MANUAL PADA SOAL (PROGRESSIVE REVEAL)
        // =========================================================
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.soal-item[data-step]');
            items.forEach(function(item) {
                item.addEventListener('click', function() {
                    const step = parseInt(item.getAttribute('data-step'));
                    if (step === 1 && currentStep < 1) {
                        triggerBel2();
                    } else if (step > currentStep) {
                        currentStep = step;
                        updateActiveBadge();
                        highlightSoal(step);
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
