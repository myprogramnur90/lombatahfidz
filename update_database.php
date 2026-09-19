<?php
require_once 'config/database.php';

try {
    // Tambah kolom target_audience
    $sql1 = "ALTER TABLE posts ADD COLUMN target_audience ENUM('Sekolah', 'Juri', 'Umum') DEFAULT 'Umum' AFTER jenis_post";
    $pdo->exec($sql1);
    echo "✅ Kolom target_audience berhasil ditambahkan<br>";
    
    // Update sekolah_id agar bisa NULL
    $sql2 = "ALTER TABLE posts MODIFY COLUMN sekolah_id INT NULL";
    $pdo->exec($sql2);
    echo "✅ Kolom sekolah_id berhasil diubah menjadi nullable<br>";
    
    // Update foreign key constraint
    try {
        $sql3 = "ALTER TABLE posts DROP FOREIGN KEY posts_ibfk_1";
        $pdo->exec($sql3);
        echo "✅ Foreign key constraint lama berhasil dihapus<br>";
    } catch (PDOException $e) {
        echo "⚠️ Foreign key constraint lama tidak ditemukan (sudah dihapus atau belum ada)<br>";
    }
    
    $sql4 = "ALTER TABLE posts ADD CONSTRAINT posts_ibfk_1 FOREIGN KEY (sekolah_id) REFERENCES sekolah(id) ON DELETE SET NULL";
    $pdo->exec($sql4);
    echo "✅ Foreign key constraint baru berhasil ditambahkan<br>";
    
    // Update data yang sudah ada
    $sql5 = "UPDATE posts SET target_audience = 'Sekolah' WHERE sekolah_id IS NOT NULL";
    $pdo->exec($sql5);
    echo "✅ Data posting sekolah berhasil diupdate<br>";
    
    $sql6 = "UPDATE posts SET target_audience = 'Umum' WHERE sekolah_id IS NULL";
    $pdo->exec($sql6);
    echo "✅ Data posting umum berhasil diupdate<br>";
    
    echo "<br><strong>🎉 Database berhasil diupdate! Sekarang sistem posting sudah mendukung target audience.</strong><br>";
    echo "<a href='admin/posting.php'>Klik di sini untuk mengakses halaman posting</a>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
