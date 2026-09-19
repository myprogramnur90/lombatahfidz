<?php
require_once 'config/database.php';

echo "<h2>Update Database - Assignment Juri</h2>";

try {
    // Buat tabel assignment_juri
    $sql_assignment = "
    CREATE TABLE IF NOT EXISTS assignment_juri (
        id INT AUTO_INCREMENT PRIMARY KEY,
        juri_id INT NOT NULL,
        peserta_id INT NOT NULL,
        status ENUM('Assigned', 'Completed') DEFAULT 'Assigned',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
        FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE CASCADE,
        UNIQUE KEY unique_juri_assignment (juri_id),
        UNIQUE KEY unique_peserta_assignment (peserta_id)
    )";
    
    if ($pdo->exec($sql_assignment) !== false) {
        echo "<p style='color: green;'>✓ Tabel 'assignment_juri' berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>✗ Error membuat tabel assignment_juri</p>";
    }
    
    // Cek apakah ada data juri dan peserta
    $query_juri = "SELECT COUNT(*) as total FROM juri";
    $result_juri = $pdo->query($query_juri);
    $total_juri = $result_juri->fetch()['total'];
    
    $query_peserta = "SELECT COUNT(*) as total FROM peserta WHERE status = 'Diterima'";
    $result_peserta = $pdo->query($query_peserta);
    $total_peserta = $result_peserta->fetch()['total'];
    
    echo "<hr>";
    echo "<h3>Status Data:</h3>";
    echo "<p>✓ Total Juri: <strong>{$total_juri}</strong></p>";
    echo "<p>✓ Total Peserta (Diterima): <strong>{$total_peserta}</strong></p>";
    
    if ($total_juri > 0 && $total_peserta > 0) {
        echo "<hr>";
        echo "<h3>Assignment Otomatis (Demo):</h3>";
        
        // Ambil 3 juri pertama
        $query_juri_list = "SELECT * FROM juri ORDER BY id LIMIT 3";
        $result_juri_list = $pdo->query($query_juri_list);
        $juri_list = $result_juri_list->fetchAll();
        
        // Ambil 3 peserta pertama yang diterima
        $query_peserta_list = "SELECT * FROM peserta WHERE status = 'Diterima' ORDER BY id LIMIT 3";
        $result_peserta_list = $pdo->query($query_peserta_list);
        $peserta_list = $result_peserta_list->fetchAll();
        
        $min_count = min(count($juri_list), count($peserta_list));
        
        if ($min_count > 0) {
            echo "<p>Membuat assignment demo untuk {$min_count} pasangan juri-peserta:</p>";
            
            for ($i = 0; $i < $min_count; $i++) {
                $juri = $juri_list[$i];
                $peserta = $peserta_list[$i];
                
                // Cek apakah assignment sudah ada
                $query_cek = "SELECT id FROM assignment_juri WHERE juri_id = ? OR peserta_id = ?";
                $stmt_cek = $pdo->prepare($query_cek);
                $stmt_cek->execute([$juri['id'], $peserta['id']]);
                
                if (!$stmt_cek->fetch()) {
                    $query_insert = "INSERT INTO assignment_juri (juri_id, peserta_id) VALUES (?, ?)";
                    $stmt_insert = $pdo->prepare($query_insert);
                    
                    if ($stmt_insert->execute([$juri['id'], $peserta['id']])) {
                        echo "<p style='color: green;'>✓ {$juri['nama_lengkap']} → {$peserta['nama_lengkap']}</p>";
                    } else {
                        echo "<p style='color: red;'>✗ Gagal assign {$juri['nama_lengkap']} → {$peserta['nama_lengkap']}</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>⚠ Assignment sudah ada untuk {$juri['nama_lengkap']} atau {$peserta['nama_lengkap']}</p>";
                }
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Update Database Selesai!</h3>";
    echo "<p><strong>Fitur yang telah ditambahkan:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Tabel assignment_juri untuk mengatur juri ke peserta</li>";
    echo "<li>✓ Setiap juri hanya bisa menilai 1 peserta</li>";
    echo "<li>✓ Setiap peserta hanya dinilai oleh 1 juri</li>";
    echo "<li>✓ Dashboard juri menampilkan hanya peserta yang di-assign</li>";
    echo "<li>✓ Form penilaian hanya bisa diakses untuk peserta yang di-assign</li>";
    echo "</ul>";
    
    echo "<p><strong>Halaman yang dapat diakses:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Assignment Juri (Admin): <a href='admin/assignment_juri.php'>admin/assignment_juri.php</a></li>";
    echo "<li>✓ Dashboard Juri: <a href='juri/dashboard.php'>juri/dashboard.php</a></li>";
    echo "<li>✓ Form Penilaian: <a href='juri/penilaian.php'>juri/penilaian.php</a></li>";
    echo "</ul>";
    
    echo "<p><strong>Cara Kerja:</strong></p>";
    echo "<ol>";
    echo "<li>Admin membuat assignment juri ke peserta melalui halaman Assignment Juri</li>";
    echo "<li>Juri login dan hanya melihat peserta yang di-assign ke mereka</li>";
    echo "<li>Juri hanya bisa mengisi penilaian untuk peserta yang di-assign</li>";
    echo "<li>Sistem memastikan setiap juri hanya menilai 1 peserta</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f5f5f5;
}
h2, h3 {
    color: #333;
}
p {
    margin: 10px 0;
}
ul, ol {
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
a {
    color: #007bff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
