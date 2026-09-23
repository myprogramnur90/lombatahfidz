<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ../auth/login_admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Menu - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .test-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .test-result {
            background: #f3e5f5;
            border: 1px solid #9c27b0;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="p-4">
                    <h2><i class="fas fa-bug me-2"></i>Test Menu Admin</h2>
                    
                    <div class="test-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Informasi Test</h5>
                        <p>Halaman ini digunakan untuk menguji apakah menu "Kelola Posting" dapat diklik dengan baik.</p>
                        <p><strong>Langkah test:</strong></p>
                        <ol>
                            <li>Klik menu "Kelola Posting" di sidebar kiri</li>
                            <li>Jika berhasil, Anda akan diarahkan ke halaman posting.php</li>
                            <li>Jika tidak berhasil, periksa console browser (F12) untuk error</li>
                        </ol>
                    </div>
                    
                    <div class="test-result">
                        <h5><i class="fas fa-check-circle me-2"></i>Status Test</h5>
                        <div id="test-status">
                            <p>Menunggu test menu...</p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Debug Information</h5>
                        </div>
                        <div class="card-body">
                            <div id="debug-info">
                                <p>Loading debug information...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="posting.php" class="btn btn-primary">
                            <i class="fas fa-newspaper me-2"></i>Test Direct Link ke Posting
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const debugInfo = document.getElementById('debug-info');
            const testStatus = document.getElementById('test-status');
            
            // Test menu links
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            let postingLink = null;
            
            navLinks.forEach(link => {
                if (link.href && link.href.includes('posting.php')) {
                    postingLink = link;
                }
            });
            
            // Debug information
            let debugHtml = '<h6>Menu Links Found:</h6><ul>';
            navLinks.forEach((link, index) => {
                debugHtml += `<li>Link ${index + 1}: ${link.href || 'No href'} - Text: "${link.textContent.trim()}"</li>`;
            });
            debugHtml += '</ul>';
            
            if (postingLink) {
                debugHtml += `<h6>Posting Link Status:</h6>`;
                debugHtml += `<ul>`;
                debugHtml += `<li>Href: ${postingLink.href}</li>`;
                debugHtml += `<li>Clickable: ${postingLink.style.pointerEvents !== 'none'}</li>`;
                debugHtml += `<li>Z-index: ${postingLink.style.zIndex || 'auto'}</li>`;
                debugHtml += `<li>Position: ${postingLink.style.position || 'static'}</li>`;
                debugHtml += `</ul>`;
                
                testStatus.innerHTML = '<p class="text-success"><i class="fas fa-check me-2"></i>Menu "Kelola Posting" ditemukan dan siap untuk test!</p>';
            } else {
                debugHtml += `<h6 class="text-danger">Posting Link Status:</h6>`;
                debugHtml += `<p class="text-danger">Menu "Kelola Posting" tidak ditemukan!</p>`;
                
                testStatus.innerHTML = '<p class="text-danger"><i class="fas fa-times me-2"></i>Menu "Kelola Posting" tidak ditemukan!</p>';
            }
            
            debugInfo.innerHTML = debugHtml;
            
            // Test click functionality
            if (postingLink) {
                postingLink.addEventListener('click', function(e) {
                    testStatus.innerHTML = '<p class="text-success"><i class="fas fa-check me-2"></i>Menu "Kelola Posting" berhasil diklik!</p>';
                });
            }
        });
    </script>
</body>
</html>
