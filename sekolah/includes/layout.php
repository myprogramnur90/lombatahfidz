<?php
// Cek apakah user sudah login sebagai sekolah
if (!isset($_SESSION['sekolah_id'])) {
    header('Location: ../index.php');
    exit();
}

// Set default page title jika belum diset
if (!isset($page_title)) {
    $page_title = 'Dashboard';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo getPengaturan('nama_lomba'); ?></title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico?v=1.1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        
        /* Desktop Sidebar */
        .sidebar { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            color: white; 
        }
        .sidebar .nav-link { 
            color: rgba(255, 255, 255, 0.8); 
            padding: 15px 20px; 
            border-radius: 10px; 
            margin: 5px 0; 
            transition: all 0.3s ease; 
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            background: rgba(255, 255, 255, 0.2); 
            color: white; 
        }
        
        /* Mobile Sidebar */
        .sidebar-mobile {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar-mobile .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }
        .sidebar-mobile .nav-link:hover, .sidebar-mobile .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1050;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .col-md-9.col-lg-10 {
                padding-left: 80px !important;
            }
            .container-fluid .row {
                margin-left: 0;
            }
            
            /* Mobile modal adjustments */
            .modal-fullscreen-sm-down {
                margin: 0;
                max-width: 100%;
            }
            
            /* Mobile form adjustments */
            .form-control {
                font-size: 16px; /* Prevents zoom on iOS */
            }
            
            /* Mobile card adjustments */
            .card-body {
                padding: 1rem;
            }
            
            /* Mobile button adjustments */
            .btn {
                padding: 10px 15px;
                font-size: 14px;
            }
        }
        
        /* Tooltip customization */
        .tooltip {
            font-size: 14px;
        }
        .tooltip-inner {
            background-color: #333;
            color: white;
            border-radius: 8px;
            padding: 8px 12px;
        }
        
        /* Card and form styles */
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); 
            transition: transform 0.3s ease; 
        }
        .card:hover { 
            transform: translateY(-5px); 
        }
        .form-control { 
            border-radius: 10px; 
            border: 2px solid #e9ecef; 
            padding: 12px 15px; 
            transition: all 0.3s ease; 
        }
        .form-control:focus { 
            border-color: #667eea; 
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); 
        }
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border: none; 
            border-radius: 10px; 
            padding: 12px 30px; 
            font-weight: 600; 
        }
        .stat-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        .stat-card .card-body { 
            padding: 2rem; 
        }
        .stat-number { 
            font-size: 2.5rem; 
            font-weight: bold; 
        }
        
        /* Form validation styles */
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-control.is-valid {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        /* Mobile modal improvements */
        @media (max-width: 575.98px) {
            .modal-dialog {
                margin: 0;
                max-width: 100%;
            }
            
            .modal-content {
                border-radius: 0;
                min-height: 100vh;
            }
            
            .modal-header {
                border-bottom: 1px solid #dee2e6;
                padding: 1rem;
            }
            
            .modal-body {
                padding: 1rem;
                flex: 1;
            }
            
            .modal-footer {
                border-top: 1px solid #dee2e6;
                padding: 1rem;
                background-color: #f8f9fa;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($content)): ?>
                        <?php echo $content; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
        
        // Auto-close mobile menu when clicking on a link
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuLinks = document.querySelectorAll('.sidebar-mobile .nav-link');
            const offcanvasElement = document.getElementById('sidebarMenu');
            
            mobileMenuLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                });
            });
        });
        
        // Fix modal issues on mobile
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure modals work properly on mobile
            const modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    // Focus on first input when modal opens
                    const firstInput = modal.querySelector('input, select, textarea');
                    if (firstInput) {
                        setTimeout(function() {
                            firstInput.focus();
                        }, 100);
                    }
                });
                
                modal.addEventListener('hidden.bs.modal', function() {
                    // Clear any validation states
                    const inputs = modal.querySelectorAll('.form-control');
                    inputs.forEach(function(input) {
                        input.classList.remove('is-invalid', 'is-valid');
                    });
                });
            });
            
            // Fix form submission issues on mobile
            const forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    // Ensure form is valid before submission
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(function(field) {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                            field.classList.add('is-valid');
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        // Show error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-danger mt-2';
                        errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Mohon lengkapi semua field yang wajib diisi!';
                        
                        const existingError = form.querySelector('.alert-danger');
                        if (existingError) {
                            existingError.remove();
                        }
                        
                        form.appendChild(errorDiv);
                        
                        // Scroll to first invalid field
                        const firstInvalid = form.querySelector('.is-invalid');
                        if (firstInvalid) {
                            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

