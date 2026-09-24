<?php
// File menu admin yang dapat digunakan di semua halaman
function getCurrentPage() {
    $current_file = basename($_SERVER['PHP_SELF']);
    return $current_file;
}

function isActive($page) {
    return getCurrentPage() === $page ? 'active' : '';
}
?>

<!-- Sidebar Menu -->
<div class="col-md-3 col-lg-2 px-0">
    <div class="sidebar">
        <div class="p-3">
            <h4><i class="fas fa-user-shield me-2"></i>Admin</h4>
            <hr>
        </div>
        <nav class="nav flex-column px-3">
            <a class="nav-link <?php echo isActive('dashboard.php'); ?>" href="dashboard.php" 
               data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard Admin">
                <i class="fas fa-tachometer-alt me-2"></i>
                <span class="menu-text">Dashboard</span>
            </a>
            <a class="nav-link <?php echo isActive('sekolah.php'); ?>" href="sekolah.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Data Sekolah">
                <i class="fas fa-school me-2"></i>
                <span class="menu-text">Kelola Sekolah</span>
            </a>
            <a class="nav-link <?php echo isActive('peserta.php'); ?>" href="peserta.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Data Peserta Lomba">
                <i class="fas fa-users me-2"></i>
                <span class="menu-text">Data Peserta</span>
            </a>
            <a class="nav-link <?php echo isActive('pembayaran.php'); ?>" href="pembayaran.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Pembayaran">
                <i class="fas fa-credit-card me-2"></i>
                <span class="menu-text">Pembayaran</span>
            </a>
            <a class="nav-link <?php echo isActive('dokumen.php'); ?>" href="dokumen.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Dokumen Berka">
                <i class="fas fa-file-alt me-2"></i>
                <span class="menu-text">Dokumen Berka</span>
            </a>
            <a class="nav-link <?php echo isActive('posting.php'); ?>" href="posting.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Posting Sekolah">
                <i class="fas fa-newspaper me-2"></i>
                <span class="menu-text">Kelola Posting</span>
            </a>
            <a class="nav-link <?php echo isActive('soal.php'); ?>" href="soal.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Soal Musabaqoh">
                <i class="fas fa-book-open me-2"></i>
                <span class="menu-text">Soal</span>
            </a>
            <a class="nav-link <?php echo isActive('juri.php'); ?>" href="juri.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Data Juri">
                <i class="fas fa-gavel me-2"></i>
                <span class="menu-text">Kelola Juri</span>
            </a>
            
            
            <a class="nav-link <?php echo isActive('laporan_penilaian.php'); ?>" href="laporan_penilaian.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Hasil Penilaian">
                <i class="fas fa-chart-bar me-2"></i>
                <span class="menu-text">Laporan Penilaian</span>
            </a>
            
            <!-- Menu Sistem Final -->
            <div class="px-3 pt-3 pb-1">
                <small class="text-uppercase fw-bold" style="color: rgba(255,255,255,0.6); font-size: 0.75rem;">Babak Final</small>
            </div>
            
            <a class="nav-link <?php echo isActive('peserta_final.php'); ?>" href="peserta_final.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Peserta Final">
                <i class="fas fa-users-cog me-2"></i>
                <span class="menu-text">Peserta Final</span>
            </a>
            <a class="nav-link <?php echo isActive('assignment_juri_final.php'); ?>" href="assignment_juri_final.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Assignment Juri Final">
                <i class="fas fa-user-tie me-2"></i>
                <span class="menu-text">Assignment Juri</span>
            </a>
            <a class="nav-link <?php echo isActive('laporan_penilaian_final.php'); ?>" href="laporan_penilaian_final.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Penilaian Final">
                <i class="fas fa-chart-line me-2"></i>
                <span class="menu-text">Laporan Final</span>
            </a>
            
            <div class="px-3 pt-3 pb-1">
                <small class="text-uppercase fw-bold" style="color: rgba(255,255,255,0.6); font-size: 0.75rem;">Sistem</small>
            </div>
            <a class="nav-link <?php echo isActive('database.php'); ?>" href="database.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Database">
                <i class="fas fa-database me-2"></i>
                <span class="menu-text">Kelola Database</span>
            </a>
            <a class="nav-link <?php echo isActive('pengaturan.php'); ?>" href="pengaturan.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Pengaturan Sistem">
                <i class="fas fa-cog me-2"></i>
                <span class="menu-text">Pengaturan</span>
            </a>
            <a class="nav-link <?php echo isActive('profil.php'); ?>" href="profil.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Profil Admin">
                <i class="fas fa-user-edit me-2"></i>
                <span class="menu-text">Profil</span>
            </a>
            <hr>
            <a class="nav-link" href="../auth/logout.php"
               data-bs-toggle="tooltip" data-bs-placement="right" title="Keluar dari Sistem">
                <i class="fas fa-sign-out-alt me-2"></i>
                <span class="menu-text">Logout</span>
            </a>
        </nav>
    </div>
</div>

<style>


/* Tooltip customization */
.tooltip {
    font-size: 0.875rem;
}

.tooltip-inner {
    background-color: #333;
    color: white;
    border-radius: 6px;
    padding: 8px 12px;
}

/* Sidebar styles */
.sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 100vh;
    color: white;
    position: sticky;
    top: 0;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Custom Scrollbar for Sidebar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

.sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 15px 20px;
    border-radius: 10px;
    margin: 5px 0;
    transition: all 0.3s ease;
    text-decoration: none;
    pointer-events: auto !important;
    cursor: pointer !important;
    z-index: 1001 !important;
    position: relative !important;
}

.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

.sidebar .nav-link i {
    width: 20px;
    text-align: center;
}
/* Responsive Menu Styles */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
        height: 100vh;
        z-index: 1000;
        transition: left 0.3s ease;
    }
    
    .sidebar.show {
        left: 0;
    }
    
    .menu-text {
        display: inline !important;
        margin-left: 8px;
    }
    
    .nav-link {
        justify-content: flex-start;
        padding: 15px 20px !important;
    }
    
    .nav-link i {
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }
    
    /* Overlay untuk mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
    
    /* Mobile menu toggle button */
    .mobile-menu-toggle {
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1001;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    /* Adjust main content for mobile */
    .main-content {
        margin-left: 0;
        padding-top: 60px;
        padding-left: 15px; /* using default bootstrap padding */
        padding-right: 15px;
    }
}

@media (min-width: 769px) {
    .mobile-menu-toggle {
        display: none;
    }
    
    .sidebar-overlay {
        display: none !important;
    }
}
</style>

<script>
// Mobile menu functionality
document.addEventListener('DOMContentLoaded', function() {
    // Create mobile menu toggle button
    const mobileToggle = document.createElement('button');
    mobileToggle.className = 'mobile-menu-toggle';
    mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
    document.body.appendChild(mobileToggle);
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Add main content class
    const mainContent = document.querySelector('.col-md-9, .col-lg-10');
    if (mainContent) {
        mainContent.classList.add('main-content');
    }
    
    // Toggle sidebar
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    }
    
    // Event listeners
    mobileToggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Close sidebar when clicking on menu items (mobile)
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(link => {
        // Ensure links are clickable
        link.style.pointerEvents = 'auto';
        link.style.cursor = 'pointer';
        link.style.zIndex = '1001';
        link.style.position = 'relative';
        
        link.addEventListener('click', function(e) {
            // Allow normal navigation for all links
            if (this.href && this.href !== 'javascript:void(0)') {
                // For mobile, close sidebar after a short delay
                if (window.innerWidth <= 768) {
                    setTimeout(() => {
                        toggleSidebar();
                    }, 100);
                }
                // Allow normal navigation
                return true;
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        if (window.innerWidth > 768) {
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        }
    });
});
</script>

