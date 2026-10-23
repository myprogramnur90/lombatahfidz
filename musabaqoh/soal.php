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
            padding: 25px 30px;
            max-width: 1600px;
        }
        
        /* Sticky Bel Diganti Floating Dock */
        
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

        /* Panel Bel Melayang (Floating Dock) Styling */
        .soal-main-layout {
            position: relative;
            width: 100%;
        }

        .floating-bel-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
            border: 2px solid #5a78d1;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .floating-bel-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            user-select: none;
        }
        
        .btn-bel {
            border: none;
            border-radius: 10px;
            color: white;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }
        .btn-bel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            color: white;
        }
        .btn-bel:active, .btn-bel.ringing {
            transform: scale(0.95);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.7);
        }
        
        /* Tombol Bel Ukuran Lebih Kecil (Compact) */
        .btn-bel-sm {
            padding: 7px 6px;
            font-size: 0.84rem;
            line-height: 1.2;
        }

        .bel-key-badge {
            background: rgba(255, 255, 255, 0.92);
            color: #212529;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }

        .bel-subtext {
            font-size: 0.69rem;
            color: rgba(255, 255, 255, 0.9);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        @media (min-width: 992px) {
            .header {
                padding-right: 285px;
                transition: padding-right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .header.panel-collapsed {
                padding-right: 65px;
            }
            .peserta-aktif-box {
                margin-right: 285px;
                transition: margin-right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .peserta-aktif-box.panel-collapsed {
                margin-right: 65px;
            }
            .soal-content-area {
                padding-right: 285px;
                transition: padding-right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .soal-content-area.panel-collapsed {
                padding-right: 65px;
            }
            .floating-bel-container {
                position: fixed;
                right: max(20px, calc((100vw - 1600px) / 2 + 20px));
                top: 85px;
                width: 265px;
                max-height: calc(100vh - 105px);
                overflow-y: auto;
                z-index: 1040;
            }
            .floating-bel-container.collapsed {
                width: 52px;
                overflow: visible;
            }
        }

        @media (max-width: 991.98px) {
            .soal-content-area {
                padding-right: 0;
                padding-bottom: 220px;
            }
            .floating-bel-container {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                max-height: 55vh;
                overflow-y: auto;
                z-index: 1040;
                border-radius: 20px 20px 0 0;
                border-bottom: none;
                box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.18);
            }
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
    <div class="container-fluid px-2 px-md-4">
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
            <div class="alert alert-light border shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 peserta-aktif-box" style="border-radius: 14px; border-left: 6px solid #667eea !important;">
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

            <!-- Layout Soal & Panel Melayang -->
            <div class="soal-main-layout">
                <!-- Area Tampilan Soal (Lebar Penuh) -->
                <div class="soal-content-area">
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
                    
                    <!-- Notifikasi Selesai di Bawah Soal -->
                    <div id="finishNoticeBoxMain" class="alert alert-success mt-4 mb-4 text-center py-3 shadow-sm" style="display: none; border-radius: 14px;">
                        <h4 class="mb-1 fw-bold text-success"><i class="fas fa-check-circle me-2"></i>Alhamdulillah, Seluruh Soal Telah Selesai Dibacakan!</h4>
                        <p class="mb-2 text-muted">Peserta menghentikan jawaban terakhir. Silakan kembali untuk memilih nomor soal berikutnya.</p>
                        <a href="#" onclick="kembaliDanClear(); return false;" class="btn btn-success fw-bold px-4 py-2 mt-1">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Pilih Soal
                        </a>
                    </div>
                    
                    <div class="controls text-start mb-4">
                        <a href="#" onclick="kembaliDanClear(); return false;" class="btn btn-custom ms-0">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Pilih Soal
                        </a>
                    </div>
                </div>

                <!-- =============================================================== -->
                <!-- PANEL KONTROL BEL MUSABAQOH MELAYANG (FLOATING DOCK KANAN)     -->
                <!-- =============================================================== -->
                <div class="floating-bel-container" id="floatingBelPanel">
                    <!-- Header Panel Melayang -->
                    <div class="floating-bel-header text-white d-flex justify-content-between align-items-center py-2 px-3">
                        <div class="d-flex align-items-center" id="floatingBelTitle">
                            <i class="fas fa-bell text-warning me-2"></i>
                            <span class="fw-bold" style="font-size: 0.88rem;">Kontrol Bel</span>
                        </div>
                        <div class="d-flex align-items-center gap-1 ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-light py-0 px-2" id="btnTesSuaraHeader" style="font-size: 0.72rem; border-radius: 6px;" onclick="testSound()" title="Uji Suara Bel">
                                <i class="fas fa-volume-up me-1"></i>Tes
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-light py-0 px-2" id="btnToggleCollapse" style="font-size: 0.72rem; border-radius: 6px;" onclick="toggleBelCollapse()" title="Sembunyikan / Tampilkan Panel">
                                <i class="fas fa-chevron-right" id="iconToggleCollapse"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Mini Strip (Aktif Saat Mode Collapsed) -->
                    <div id="floatingBelMini" class="p-2 d-none flex-column align-items-center gap-2">
                        <button type="button" class="btn btn-bel btn-bel-start p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 10px;" onclick="triggerBel2()" title="Bel 2x - Mulai (Tekan 2)">
                            <span class="fw-bold" style="font-size: 0.85rem;">2x</span>
                        </button>
                        <button type="button" class="btn btn-bel btn-bel-warning p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 10px;" onclick="triggerBel1()" title="Bel 1x - Teguran (Tekan 1)">
                            <span class="fw-bold" style="font-size: 0.85rem;">1x</span>
                        </button>
                        <button type="button" class="btn btn-bel btn-bel-next p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 10px;" onclick="triggerBel3()" title="Bel 3x - Pindah (Tekan 3)">
                            <span class="fw-bold" style="font-size: 0.85rem;">3x</span>
                        </button>
                        <button type="button" class="btn btn-bel btn-bel-finish p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-radius: 10px;" onclick="triggerBel4()" title="Bel 4x - Selesai (Tekan 4)">
                            <span class="fw-bold" style="font-size: 0.85rem;">4x</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary p-0 d-flex align-items-center justify-content-center mt-1" style="width: 32px; height: 32px; border-radius: 8px;" onclick="toggleBelCollapse()" title="Buka Panel Lengkap">
                            <i class="fas fa-chevron-left" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>

                    <!-- Body Panel Melayang -->
                    <div class="floating-bel-body p-2 p-md-3" id="floatingBelBody">
                        <!-- Status Ring & Active Soal -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center text-truncate me-1" style="min-width: 0;">
                                <span class="ring-indicator me-2 flex-shrink-0" id="ringIndicator"></span>
                                <span id="belStatusText" class="fw-bold small text-primary text-truncate" style="font-size: 0.78rem;">Menunggu lomba...</span>
                            </div>
                            <span id="activeSoalBadge" class="badge bg-secondary py-1 px-2 flex-shrink-0" style="font-size: 0.72rem;">Belum Mulai</span>
                        </div>

                        <!-- 4 Tombol Protokol Bel (Dibuat Lebih Kecil & Kompak) -->
                        <div class="row g-1 text-center">
                            <!-- 1. Bel 2x: Mulai Lomba -->
                            <div class="col-6">
                                <button type="button" class="btn btn-bel btn-bel-start btn-bel-sm w-100" id="btnBel2" onclick="triggerBel2()" title="Bel 2x - Mulai Lomba (Shortcut: 2)">
                                    <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                        <i class="fas fa-play-circle fa-sm"></i>
                                        <span class="fw-bold">Bel 2x</span>
                                        <span class="bel-key-badge">[2]</span>
                                    </div>
                                    <div class="bel-subtext">Mulai Lomba</div>
                                </button>
                            </div>

                            <!-- 2. Bel 1x: Teguran Kesalahan -->
                            <div class="col-6">
                                <button type="button" class="btn btn-bel btn-bel-warning btn-bel-sm w-100" id="btnBel1" onclick="triggerBel1()" title="Bel 1x - Teguran Kesalahan (Shortcut: 1)">
                                    <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                        <i class="fas fa-exclamation-triangle fa-sm"></i>
                                        <span class="fw-bold">Bel 1x</span>
                                        <span class="bel-key-badge">[1]</span>
                                    </div>
                                    <div class="bel-subtext">Tegur (+Jeda)</div>
                                </button>
                            </div>

                            <!-- 3. Bel 3x: Pindah Soal -->
                            <div class="col-6">
                                <button type="button" class="btn btn-bel btn-bel-next btn-bel-sm w-100" id="btnBel3" onclick="triggerBel3()" title="Bel 3x - Pindah Soal (Shortcut: 3)">
                                    <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                        <i class="fas fa-step-forward fa-sm"></i>
                                        <span class="fw-bold">Bel 3x</span>
                                        <span class="bel-key-badge">[3]</span>
                                    </div>
                                    <div class="bel-subtext">Pindah Soal</div>
                                </button>
                            </div>

                            <!-- 4. Bel 4x: Lomba Berakhir -->
                            <div class="col-6">
                                <button type="button" class="btn btn-bel btn-bel-finish btn-bel-sm w-100" id="btnBel4" onclick="triggerBel4()" title="Bel 4x - Lomba Berakhir (Shortcut: 4)">
                                    <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                        <i class="fas fa-flag-checkered fa-sm"></i>
                                        <span class="fw-bold">Bel 4x</span>
                                        <span class="bel-key-badge">[4]</span>
                                    </div>
                                    <div class="bel-subtext">Selesai</div>
                                </button>
                            </div>
                        </div>

                        <!-- Kotak Animasi Countdown Jeda Waktu Kesalahan (Aktif saat Bel 1x) -->
                        <div id="jedaCountdownBox" class="mt-2 p-2 rounded-3 shadow-sm" style="display: none; background: #fff8e1; border: 2px solid #ffc107;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-grow spinner-grow-sm text-warning me-1" role="status" style="width: 1rem; height: 1rem;"></div>
                                    <span class="fw-bold text-dark" style="font-size: 0.75rem;">Jeda Tawaqquf</span>
                                </div>
                                <div class="fs-5 fw-bold text-danger font-monospace" id="countdownTimer">08s</div>
                            </div>
                            <div class="progress mb-2" style="height: 5px;">
                                <div id="countdownProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%;"></div>
                            </div>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-success btn-sm fw-bold flex-grow-1 py-1" style="font-size: 0.75rem;" onclick="pesertaMelanjutkan()">
                                    <i class="fas fa-check me-1"></i>Lanjut [Spasi]
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" style="font-size: 0.75rem;" onclick="cancelCountdown()">
                                    Batal
                                </button>
                            </div>
                        </div>

                        <!-- Pengaturan Bunyi & Jeda (Dropdown Ringkas) -->
                        <div class="mt-2 pt-2 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a class="text-muted small text-decoration-none" data-bs-toggle="collapse" href="#belSettingsCollapse" role="button" aria-expanded="false" style="font-size: 0.72rem;">
                                    <i class="fas fa-cog me-1"></i>Pengaturan <i class="fas fa-caret-down ms-1"></i>
                                </a>
                                <span class="text-muted" style="font-size: 0.68rem;"><i class="fas fa-keyboard me-1"></i>1 2 3 4 Spasi</span>
                            </div>
                            <div class="collapse" id="belSettingsCollapse">
                                <div class="row g-1 pt-1" style="font-size: 0.75rem;">
                                    <div class="col-6">
                                        <label for="selectBunyi" class="text-muted small mb-0 d-block" style="font-size: 0.7rem;"><i class="fas fa-music me-1 text-info"></i>Bunyi:</label>
                                        <select id="selectBunyi" class="form-select form-select-sm py-0 px-1" style="font-size: 0.72rem; height: 26px;">
                                            <option value="kuningan" <?php echo $belBunyiSetting === 'kuningan' ? 'selected' : ''; ?>>Kuningan</option>
                                            <option value="digital" <?php echo $belBunyiSetting === 'digital' ? 'selected' : ''; ?>>Digital</option>
                                            <option value="gong" <?php echo $belBunyiSetting === 'gong' ? 'selected' : ''; ?>>Gong</option>
                                            <option value="tabung" <?php echo $belBunyiSetting === 'tabung' ? 'selected' : ''; ?>>Tabung</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="selectJeda" class="text-muted small mb-0 d-block" style="font-size: 0.7rem;"><i class="fas fa-stopwatch me-1 text-warning"></i>Jeda:</label>
                                        <select id="selectJeda" class="form-select form-select-sm py-0 px-1" style="font-size: 0.72rem; height: 26px;">
                                            <?php
                                            $optionsJeda = [5, 8, 10, 15];
                                            if (!in_array($belJedaSetting, $optionsJeda)) {
                                                $optionsJeda[] = $belJedaSetting;
                                                sort($optionsJeda);
                                            }
                                            foreach ($optionsJeda as $detik) {
                                                $sel = ($detik == $belJedaSetting) ? 'selected' : '';
                                                echo "<option value=\"$detik\" $sel>{$detik}d</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifikasi Selesai di Dalam Panel -->
                        <div id="finishNoticeBox" class="alert alert-success mt-2 mb-0 text-center py-2 px-2" style="display: none; border-radius: 8px; font-size: 0.75rem;">
                            <strong class="d-block text-success mb-1"><i class="fas fa-check-circle me-1"></i>Lomba Selesai!</strong>
                            <span class="text-muted">Seluruh soal telah dibacakan.</span>
                        </div>
                    </div>
                </div>
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
            const ctx = getAudioContext();
            if (ctx && ctx.state === 'suspended') {
                try {
                    await ctx.resume();
                } catch(e) {}
            }
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
            const finishBoxMain = document.getElementById('finishNoticeBoxMain');
            if (finishBoxMain) finishBoxMain.style.display = 'none';
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

            if (isBelCollapsed && window.innerWidth >= 992) {
                toggleBelCollapse(false);
            }

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
            if (finishBox) finishBox.style.display = 'block';

            const finishBoxMain = document.getElementById('finishNoticeBoxMain');
            if (finishBoxMain) {
                finishBoxMain.style.display = 'block';
                finishBoxMain.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // =========================================================
        // TOGGLE COLLAPSE / MINIMIZE PANEL MELAYANG
        // =========================================================
        let isBelCollapsed = false;

        function toggleBelCollapse(forceState = null) {
            const panel = document.getElementById('floatingBelPanel');
            const soalArea = document.querySelector('.soal-content-area');
            const header = document.querySelector('.header');
            const pesertaBox = document.querySelector('.peserta-aktif-box');
            const headerTitle = document.getElementById('floatingBelTitle');
            const headerTes = document.getElementById('btnTesSuaraHeader');
            const bodyEl = document.getElementById('floatingBelBody');
            const miniEl = document.getElementById('floatingBelMini');
            const iconToggle = document.getElementById('iconToggleCollapse');

            if (forceState !== null) {
                isBelCollapsed = forceState;
            } else {
                isBelCollapsed = !isBelCollapsed;
            }

            if (window.innerWidth < 992) {
                // Tampilan Mobile
                if (bodyEl) {
                    if (isBelCollapsed) {
                        bodyEl.style.display = 'none';
                        if (iconToggle) iconToggle.className = 'fas fa-chevron-up';
                    } else {
                        bodyEl.style.display = 'block';
                        if (iconToggle) iconToggle.className = 'fas fa-chevron-down';
                    }
                }
                return;
            }

            // Tampilan Desktop
            if (isBelCollapsed) {
                if (panel) panel.classList.add('collapsed');
                if (soalArea) soalArea.classList.add('panel-collapsed');
                if (header) header.classList.add('panel-collapsed');
                if (pesertaBox) pesertaBox.classList.add('panel-collapsed');
                if (bodyEl) bodyEl.classList.add('d-none');
                if (miniEl) {
                    miniEl.classList.remove('d-none');
                    miniEl.classList.add('d-flex');
                }
                if (headerTitle) headerTitle.classList.add('d-none');
                if (headerTes) headerTes.classList.add('d-none');
                if (iconToggle) iconToggle.className = 'fas fa-chevron-left';
            } else {
                if (panel) panel.classList.remove('collapsed');
                if (soalArea) soalArea.classList.remove('panel-collapsed');
                if (header) header.classList.remove('panel-collapsed');
                if (pesertaBox) pesertaBox.classList.remove('panel-collapsed');
                if (bodyEl) bodyEl.classList.remove('d-none');
                if (miniEl) {
                    miniEl.classList.add('d-none');
                    miniEl.classList.remove('d-flex');
                }
                if (headerTitle) headerTitle.classList.remove('d-none');
                if (headerTes) headerTes.classList.remove('d-none');
                if (iconToggle) iconToggle.className = 'fas fa-chevron-right';
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
