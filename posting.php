<?php
require_once 'config/security.php';
initSecureSession();
setSecurityHeaders();
require_once 'config/database.php';

// Ambil ID dari parameter URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    header('Location: index.php');
    exit();
}

// Ambil data posting yang Published dan target Umum
$stmt = $pdo->prepare("SELECT p.*, s.nama_sekolah 
                       FROM posts p 
                       LEFT JOIN sekolah s ON p.sekolah_id = s.id 
                       WHERE p.id = ? 
                       AND p.status = 'Published' 
                       AND p.target_audience = 'Umum'");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: index.php');
    exit();
}

$namaLomba = getPengaturan('nama_lomba');

// Format tanggal
$bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tglPost = new DateTime($post['created_at']);
$tglFormatted = $tglPost->format('d') . ' ' . $bulan[(int)$tglPost->format('m')] . ' ' . $tglPost->format('Y, H:i');

// Badge class
$badgeClass = 'badge-informasi';
switch ($post['jenis_post']) {
    case 'Pengumuman': $badgeClass = 'badge-pengumuman'; break;
    case 'Persyaratan': $badgeClass = 'badge-persyaratan'; break;
    case 'Lainnya': $badgeClass = 'badge-lainnya'; break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=1.1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['judul']); ?> - <?php echo htmlspecialchars($namaLomba); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --primary-dark: #764ba2;
        }
        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f8f9ff;
        }
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
        }
        .post-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem 0;
        }
        .post-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-pengumuman { background: rgba(231, 76, 60, 0.2); color: #ff6b6b; }
        .badge-informasi { background: rgba(52, 152, 219, 0.2); color: #74b9ff; }
        .badge-persyaratan { background: rgba(46, 204, 113, 0.2); color: #55efc4; }
        .badge-lainnya { background: rgba(149, 165, 166, 0.2); color: #dfe6e9; }
        .post-content-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-top: -2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.04);
            line-height: 1.8;
            font-size: 1rem;
        }
        .post-content-card p {
            margin-bottom: 1rem;
        }
        .btn-back {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .btn-back:hover {
            color: white;
        }
        .post-meta {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .footer {
            background: linear-gradient(135deg, #2d3436 0%, #000000 100%);
            color: rgba(255,255,255,0.7);
            padding: 2rem 0;
            margin-top: 4rem;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-quran me-2"></i><?php echo htmlspecialchars($namaLomba); ?>
            </a>
            <a href="auth/login.php" class="btn btn-nav-login">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </a>
        </div>
    </nav>

    <!-- POST HEADER -->
    <section class="post-header">
        <div class="container">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
            <div class="mt-3">
                <span class="post-badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($post['jenis_post']); ?></span>
            </div>
            <h1 class="mt-3 fw-bold"><?php echo htmlspecialchars($post['judul']); ?></h1>
            <div class="post-meta mt-2">
                <i class="fas fa-clock me-1"></i> <?php echo $tglFormatted; ?> WIB
            </div>
        </div>
    </section>

    <!-- POST CONTENT -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="post-content-card">
                    <?php echo $post['konten']; ?>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline-primary btn-lg" style="border-radius: 50px;">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container text-center">
            <small>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($namaLomba); ?>. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

