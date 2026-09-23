<?php
session_start();
require_once '../config/database.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: ../auth/login_admin.php');
    exit();
}

// Fungsi untuk mendapatkan data posting
function getPosts($pdo) {
    $sql = "SELECT p.*, s.nama_sekolah 
            FROM posts p 
            LEFT JOIN sekolah s ON p.sekolah_id = s.id 
            ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Fungsi untuk mendapatkan data sekolah
function getSekolah($pdo) {
    $sql = "SELECT id, nama_sekolah FROM sekolah WHERE status = 'Aktif' ORDER BY nama_sekolah";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// Proses form tambah/edit posting
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    
    if ($action == 'add') {
        $sekolah_id = !empty($_POST['sekolah_id']) ? $_POST['sekolah_id'] : null;
        $judul = $_POST['judul'];
        $konten = $_POST['konten'];
        $jenis_post = $_POST['jenis_post'];
        $target_audience = $_POST['target_audience'];
        $status = $_POST['status'];
        
        try {
            $sql = "INSERT INTO posts (sekolah_id, judul, konten, jenis_post, target_audience, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sekolah_id, $judul, $konten, $jenis_post, $target_audience, $status]);
            $_SESSION['success'] = "Posting berhasil ditambahkan!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menambahkan posting!";
        }
    }
    
    if ($action == 'edit') {
        $id = $_POST['id'];
        $sekolah_id = !empty($_POST['sekolah_id']) ? $_POST['sekolah_id'] : null;
        $judul = $_POST['judul'];
        $konten = $_POST['konten'];
        $jenis_post = $_POST['jenis_post'];
        $target_audience = $_POST['target_audience'];
        $status = $_POST['status'];
        
        try {
            $sql = "UPDATE posts SET sekolah_id = ?, judul = ?, konten = ?, jenis_post = ?, target_audience = ?, status = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$sekolah_id, $judul, $konten, $jenis_post, $target_audience, $status, $id]);
            $_SESSION['success'] = "Posting berhasil diperbarui!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui posting!";
        }
    }
    
    if ($action == 'delete') {
        $id = $_POST['id'];
        
        try {
            $sql = "DELETE FROM posts WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $_SESSION['success'] = "Posting berhasil dihapus!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menghapus posting!";
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$posts = getPosts($pdo);
$sekolah_list = getSekolah($pdo);

// Handle session messages
$success = '';
$error = '';

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Posting - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .main-content {
            padding: 20px;
        }
        
        /* Ensure menu links are clickable */
        .nav-link {
            pointer-events: auto !important;
            cursor: pointer !important;
            z-index: 10 !important;
            position: relative !important;
        }
        
        /* Fix any potential overlay issues */
        .sidebar {
            z-index: 1000 !important;
        }
        
        .sidebar .nav-link {
            z-index: 1001 !important;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
        
        .badge {
            font-size: 0.8em;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'menu.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-newspaper me-2"></i>Kelola Posting Sekolah</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPostModal">
                        <i class="fas fa-plus me-2"></i>Tambah Posting
                    </button>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Posting</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Target</th>
                                        <th>Sekolah</th>
                                        <th>Judul</th>
                                        <th>Jenis</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    foreach ($posts as $post): 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php 
                                            $target_badge = '';
                                            switch($post['target_audience']) {
                                                case 'Sekolah':
                                                    $target_badge = 'bg-primary';
                                                    break;
                                                case 'Juri':
                                                    $target_badge = 'bg-warning';
                                                    break;
                                                case 'Umum':
                                                    $target_badge = 'bg-success';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $target_badge; ?>"><?php echo $post['target_audience']; ?></span>
                                        </td>
                                        <td><?php echo $post['nama_sekolah'] ? htmlspecialchars($post['nama_sekolah']) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($post['judul']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $post['jenis_post']; ?></span>
                                        </td>
                                        <td>
                                            <?php if ($post['status'] == 'Published'): ?>
                                                <span class="badge bg-success">Published</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editPost(<?php echo htmlspecialchars(json_encode($post)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deletePost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['judul']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Tambah Posting -->
    <div class="modal fade" id="addPostModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Posting Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="target_audience" class="form-label">Target Audience</label>
                            <select class="form-select" id="target_audience" name="target_audience" required onchange="toggleSekolahField()">
                                <option value="Umum">Umum</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Juri">Juri</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="sekolah_field" style="display: none;">
                            <label for="sekolah_id" class="form-label">Sekolah</label>
                            <select class="form-select" id="sekolah_id" name="sekolah_id">
                                <option value="">Pilih Sekolah</option>
                                <?php 
                                foreach ($sekolah_list as $sekolah): 
                                ?>
                                <option value="<?php echo $sekolah['id']; ?>"><?php echo htmlspecialchars($sekolah['nama_sekolah']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Posting</label>
                            <input type="text" class="form-control" id="judul" name="judul" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="jenis_post" class="form-label">Jenis Posting</label>
                            <select class="form-select" id="jenis_post" name="jenis_post" required>
                                <option value="Informasi">Informasi</option>
                                <option value="Persyaratan">Persyaratan</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="konten" class="form-label">Konten</label>
                            <textarea class="form-control" id="konten" name="konten" rows="6"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Posting -->
    <div class="modal fade" id="editPostModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Posting</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="edit_target_audience" class="form-label">Target Audience</label>
                            <select class="form-select" id="edit_target_audience" name="target_audience" required onchange="toggleEditSekolahField()">
                                <option value="Umum">Umum</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Juri">Juri</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="edit_sekolah_field" style="display: none;">
                            <label for="edit_sekolah_id" class="form-label">Sekolah</label>
                            <select class="form-select" id="edit_sekolah_id" name="sekolah_id">
                                <option value="">Pilih Sekolah</option>
                                <?php 
                                foreach ($sekolah_list as $sekolah): 
                                ?>
                                <option value="<?php echo $sekolah['id']; ?>"><?php echo htmlspecialchars($sekolah['nama_sekolah']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_judul" class="form-label">Judul Posting</label>
                            <input type="text" class="form-control" id="edit_judul" name="judul" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_jenis_post" class="form-label">Jenis Posting</label>
                            <select class="form-select" id="edit_jenis_post" name="jenis_post" required>
                                <option value="Informasi">Informasi</option>
                                <option value="Persyaratan">Persyaratan</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_konten" class="form-label">Konten</label>
                            <textarea class="form-control" id="edit_konten" name="konten" rows="6"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus posting <strong id="delete_title"></strong>?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_id">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        .ck-editor__editable_inline {
            min-height: 200px;
        }
    </style>
    <script>
        let addEditor;
        let editEditor;

        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create( document.querySelector( '#konten' ) )
                .then( editor => { addEditor = editor; } )
                .catch( error => { console.error( error ); } );

            ClassicEditor
                .create( document.querySelector( '#edit_konten' ) )
                .then( editor => { editEditor = editor; } )
                .catch( error => { console.error( error ); } );

            // Fix Bootstrap modal focus trap for CKEditor link dialog
            document.addEventListener('focusin', (e) => {
                if (e.target.closest('.ck-body-wrapper')) {
                    e.stopImmediatePropagation();
                }
            });
        });
        
        // Ensure menu links work properly
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure all nav links are clickable
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(function(link) {
                link.style.pointerEvents = 'auto';
                link.style.cursor = 'pointer';
                link.style.zIndex = '1001';
                link.style.position = 'relative';
                
                // Remove any event listeners that might be blocking clicks
                link.onclick = null;
                
                // Ensure href is working
                if (link.href && link.href !== 'javascript:void(0)') {
                    link.addEventListener('click', function(e) {
                        // Allow normal navigation
                        return true;
                    });
                }
            });
            
            // Debug: Log any issues with menu links
            console.log('Menu links initialized:', navLinks.length);
        });
        function toggleSekolahField() {
            const targetAudience = document.getElementById('target_audience').value;
            const sekolahField = document.getElementById('sekolah_field');
            const sekolahSelect = document.getElementById('sekolah_id');
            
            if (targetAudience === 'Sekolah') {
                sekolahField.style.display = 'block';
                sekolahSelect.required = true;
            } else {
                sekolahField.style.display = 'none';
                sekolahSelect.required = false;
                sekolahSelect.value = '';
            }
        }
        
        function toggleEditSekolahField() {
            const targetAudience = document.getElementById('edit_target_audience').value;
            const sekolahField = document.getElementById('edit_sekolah_field');
            const sekolahSelect = document.getElementById('edit_sekolah_id');
            
            if (targetAudience === 'Sekolah') {
                sekolahField.style.display = 'block';
                sekolahSelect.required = true;
            } else {
                sekolahField.style.display = 'none';
                sekolahSelect.required = false;
                sekolahSelect.value = '';
            }
        }
        
        function editPost(post) {
            document.getElementById('edit_id').value = post.id;
            document.getElementById('edit_target_audience').value = post.target_audience;
            document.getElementById('edit_sekolah_id').value = post.sekolah_id || '';
            document.getElementById('edit_judul').value = post.judul;
            document.getElementById('edit_jenis_post').value = post.jenis_post;
            if (editEditor) {
                editEditor.setData(post.konten);
            } else {
                document.getElementById('edit_konten').value = post.konten;
            }
            document.getElementById('edit_status').value = post.status;
            
            // Toggle sekolah field based on target audience
            toggleEditSekolahField();
            
            new bootstrap.Modal(document.getElementById('editPostModal')).show();
        }
        
        function deletePost(id, title) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_title').textContent = title;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
            </main>
        </div>
    </div>
</body>
</html>
