<?php
// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Mobile Menu Toggle Button -->
<button class="btn btn-primary d-md-none mobile-menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
    <i class="fas fa-bars"></i>
</button>

<!-- Desktop Sidebar -->
<div class="col-md-3 col-lg-2 px-0 d-none d-md-block">
    <div class="sidebar">
        <div class="p-3">
            <h4><i class="fas fa-school me-2"></i><?php echo htmlspecialchars($_SESSION['nama_sekolah'] ?? 'Panel Sekolah'); ?></h4>
            <hr>
        </div>
        <nav class="nav flex-column px-3">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                <i class="fas fa-tachometer-alt me-2"></i><span class="menu-text">Dashboard</span>
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'daftar.php' ? 'active' : ''; ?>" href="daftar.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Daftar Peserta">
                <i class="fas fa-user-plus me-2"></i><span class="menu-text">Daftar Peserta</span>
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bayar.php' ? 'active' : ''; ?>" href="bayar.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Pembayaran">
                <i class="fas fa-credit-card me-2"></i><span class="menu-text">Pembayaran</span>
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'berka.php' ? 'active' : ''; ?>" href="berka.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Dokumen Berka">
                <i class="fas fa-file-alt me-2"></i><span class="menu-text">Dokumen Berka</span>
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'peserta.php' ? 'active' : ''; ?>" href="peserta.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Data Peserta">
                <i class="fas fa-users me-2"></i><span class="menu-text">Data Peserta</span>
            </a>
            <hr>
            <a class="nav-link" href="../auth/logout.php" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                <i class="fas fa-sign-out-alt me-2"></i><span class="menu-text">Logout</span>
            </a>
        </nav>
    </div>
</div>

<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">
            <i class="fas fa-school me-2"></i><?php echo htmlspecialchars($_SESSION['nama_sekolah'] ?? 'Panel Sekolah'); ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-mobile">
            <nav class="nav flex-column">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'daftar.php' ? 'active' : ''; ?>" href="daftar.php">
                    <i class="fas fa-user-plus me-2"></i>Daftar Peserta
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bayar.php' ? 'active' : ''; ?>" href="bayar.php">
                    <i class="fas fa-credit-card me-2"></i>Pembayaran
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'berka.php' ? 'active' : ''; ?>" href="berka.php">
                    <i class="fas fa-file-alt me-2"></i>Dokumen Berka
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'peserta.php' ? 'active' : ''; ?>" href="peserta.php">
                    <i class="fas fa-users me-2"></i>Data Peserta
                </a>
                <hr>
                <a class="nav-link" href="../auth/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>
    </div>
</div>
