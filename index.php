<?php
require_once 'config/security.php';
initSecureSession();
setSecurityHeaders();
require_once 'config/database.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit();
} elseif (isset($_SESSION['sekolah_id'])) {
    header('Location: sekolah/dashboard.php');
    exit();
} elseif (isset($_SESSION['juri_id'])) {
    header('Location: juri/dashboard.php');
    exit();
} elseif (isset($_SESSION['musabaqoh_logged_in'])) {
    header('Location: musabaqoh/pilih_jenis.php');
    exit();
}

// Ambil pengaturan
$namaLomba = getPengaturan('nama_lomba');
$tanggalPenutupan = getPengaturan('tanggal_penutupan');
$biayaPendaftaran = getPengaturan('biaya_pendaftaran');
$kontakPanitia = getPengaturan('kontak_panitia');
$alamatSekretariat = getPengaturan('alamat_sekretariat');
$logoSekolah = getPengaturan('logo_sekolah');

// Format tanggal
$tanggalFormatted = '';
if ($tanggalPenutupan) {
    $date = new DateTime($tanggalPenutupan);
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $tanggalFormatted = $date->format('d') . ' ' . $bulan[(int)$date->format('m')] . ' ' . $date->format('Y');
}

// Logic untuk menghitung kunjungan
$stmtKunjungan = $pdo->prepare("SELECT nilai FROM pengaturan WHERE nama_pengaturan = 'jumlah_kunjungan'");
$stmtKunjungan->execute();
$kunjungan = $stmtKunjungan->fetch();

if (!$kunjungan) {
    // Jika belum ada, buat pengaturan baru
    $pdo->prepare("INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES ('jumlah_kunjungan', '1', 'Jumlah total kunjungan halaman utama')")->execute();
    $jumlahKunjungan = 1;
} else {
    $jumlahKunjungan = (int)$kunjungan['nilai'];
    // Hitung per sesi agar tidak setiap refresh nambah terus (opsional)
    if (!isset($_SESSION['has_visited_frontpage'])) {
        $jumlahKunjungan++;
        $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE nama_pengaturan = 'jumlah_kunjungan'")->execute([$jumlahKunjungan]);
        $_SESSION['has_visited_frontpage'] = true;
    }
}

// Ambil posting publik (Published & target Umum)
$stmtPosts = $pdo->prepare("SELECT p.*, s.nama_sekolah 
                             FROM posts p 
                             LEFT JOIN sekolah s ON p.sekolah_id = s.id 
                             WHERE p.status = 'Published' 
                             AND p.target_audience = 'Umum'
                             ORDER BY p.created_at DESC");
$stmtPosts->execute();
$posts = $stmtPosts->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($namaLomba); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
            --accent: #f093fb;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .navbar-custom .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
        }
        .navbar-custom .navbar-brand i {
            margin-right: 8px;
        }
        .btn-nav-login {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid rgba(255,255,255,0.5);
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-nav-login:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-2px);
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 80px 0 100px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .hero-subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        .btn-hero {
            background: white;
            color: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 14px 40px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            color: var(--primary-dark);
        }

        /* ===== INFO CARDS ===== */
        .info-section {
            margin-top: -50px;
            position: relative;
            z-index: 10;
            padding-bottom: 3rem;
        }
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.04);
            height: 100%;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.12);
        }
        .info-card .info-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }
        .info-icon-date { background: rgba(102, 126, 234, 0.1); color: var(--primary); }
        .info-icon-fee { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
        .info-icon-contact { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
        .info-icon-visitor { background: rgba(155, 89, 182, 0.1); color: #9b59b6; }
        .info-card h6 {
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        .info-card .info-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
        }

        /* ===== POSTS SECTION ===== */
        .posts-section {
            padding: 3rem 0 4rem;
            background: #f8f9ff;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .section-subtitle {
            color: #888;
            margin-bottom: 2.5rem;
        }
        .post-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0,0,0,0.1);
        }
        .post-card-header {
            padding: 1.5rem 1.5rem 0;
        }
        .post-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-pengumuman { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
        .badge-informasi { background: rgba(52, 152, 219, 0.1); color: #3498db; }
        .badge-persyaratan { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
        .badge-lainnya { background: rgba(149, 165, 166, 0.1); color: #95a5a6; }
        .post-card-body {
            padding: 1rem 1.5rem;
            flex-grow: 1;
        }
        .post-card-body h5 {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .post-card-body h5 a {
            color: #333;
            text-decoration: none;
            transition: color 0.2s;
        }
        .post-card-body h5 a:hover {
            color: var(--primary);
        }
        .post-excerpt {
            color: #777;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        .post-card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f0f0f0;
            font-size: 0.8rem;
            color: #aaa;
        }
        .btn-read-more {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-read-more:hover {
            color: var(--primary-dark);
            transform: translateX(3px);
        }
        .no-posts {
            text-align: center;
            padding: 3rem;
            color: #aaa;
        }
        .no-posts i {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: linear-gradient(135deg, #2d3436 0%, #000000 100%);
            color: rgba(255,255,255,0.7);
            padding: 2.5rem 0;
        }
        .footer a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }
        .footer a:hover {
            color: white;
        }
        .footer-brand {
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-title { font-size: 1.8rem; }
            .hero-subtitle { font-size: 1rem; }
            .hero-section { padding: 50px 0 80px; }
            .section-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-quran"></i><?php echo htmlspecialchars($namaLomba); ?>
            </a>
            <a href="auth/login.php" class="btn btn-nav-login d-none d-md-inline-block">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container text-center">
            <div class="hero-content">
                <div class="hero-icon">
                    <?php if ($logoSekolah && file_exists($logoSekolah)): ?>
                        <img src="<?php echo htmlspecialchars($logoSekolah); ?>" alt="Logo" style="max-height: 120px; border-radius: 15px; background: rgba(255,255,255,0.15); padding: 10px;">
                    <?php else: ?>
                        🕌
                    <?php endif; ?>
                </div>
                <h1 class="hero-title"><?php echo htmlspecialchars($namaLomba); ?></h1>
                <p class="hero-subtitle">Sistem Pendaftaran & Penilaian Online</p>
                <a href="auth/login.php" class="btn btn-hero">
                    <i class="fas fa-sign-in-alt me-2"></i>Login ke Sistem
                </a>
            </div>
        </div>
    </section>

    <!-- INFO CARDS -->
    <section class="info-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon info-icon-date">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h6>Batas Pendaftaran</h6>
                        <div class="info-value"><?php echo $tanggalFormatted ?: '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon info-icon-fee">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h6>Biaya Pendaftaran</h6>
                        <div class="info-value">Rp <?php echo number_format((int)$biayaPendaftaran, 0, ',', '.'); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon info-icon-contact">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <h6>Kontak Panitia</h6>
                        <div class="info-value"><?php echo htmlspecialchars($kontakPanitia); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-card">
                        <div class="info-icon info-icon-visitor">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6>Jumlah Kunjungan</h6>
                        <div class="info-value"><?php echo number_format($jumlahKunjungan, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- POSTS / PENGUMUMAN SECTION -->
    <section class="posts-section">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title"><i class="fas fa-bullhorn me-2" style="color: var(--primary);"></i>Pengumuman & Informasi</h2>
                <p class="section-subtitle">Informasi terbaru seputar kegiatan lomba</p>
            </div>

            <?php if (count($posts) > 0): ?>
                <div class="row g-4">
                    <?php foreach ($posts as $post): ?>
                        <?php
                        // Badge class berdasarkan jenis post
                        $badgeClass = 'badge-informasi';
                        switch ($post['jenis_post']) {
                            case 'Pengumuman': $badgeClass = 'badge-pengumuman'; break;
                            case 'Persyaratan': $badgeClass = 'badge-persyaratan'; break;
                            case 'Lainnya': $badgeClass = 'badge-lainnya'; break;
                        }
                        // Format tanggal posting
                        $tglPost = new DateTime($post['created_at']);
                        $tglFormatted = $tglPost->format('d') . ' ' . $bulan[(int)$tglPost->format('m')] . ' ' . $tglPost->format('Y');
                        // Potong konten untuk excerpt
                        $raw_text = strip_tags(html_entity_decode($post['konten'], ENT_QUOTES, 'UTF-8'));
                        // Hilangkan extra whitespace dan non-breaking space yang mungkin masih ada
                        $raw_text = preg_replace('/\s+/', ' ', str_replace('&nbsp;', ' ', $raw_text));
                        $excerpt = mb_strlen($raw_text) > 150 ? mb_substr($raw_text, 0, 150) . '...' : $raw_text;
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="post-card">
                                <div class="post-card-header">
                                    <span class="post-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($post['jenis_post']); ?></span>
                                </div>
                                <div class="post-card-body">
                                    <h5><a href="posting.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['judul']); ?></a></h5>
                                    <p class="post-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                                </div>
                                <div class="post-card-footer d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-clock me-1"></i> <?php echo $tglFormatted; ?></span>
                                    <a href="posting.php?id=<?php echo $post['id']; ?>" class="btn-read-more">Baca Selengkapnya <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-inbox"></i>
                    <h5>Belum ada pengumuman</h5>
                    <p>Pengumuman dan informasi terbaru akan ditampilkan di sini.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-brand"><i class="fas fa-quran me-2"></i><?php echo htmlspecialchars($namaLomba); ?></div>
                    <small><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($alamatSekretariat); ?></small>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <small>
                        <i class="fas fa-phone-alt me-1"></i> <?php echo htmlspecialchars($kontakPanitia); ?>
                    </small>
                    <br>
                    <small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($namaLomba); ?>. All rights reserved.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
