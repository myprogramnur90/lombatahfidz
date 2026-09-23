<?php
require 'config/database.php';
try {
    $query = "
        SELECT p.*, s.nama_sekolah,
               pb.status_pembayaran, pb.nominal as nominal_pembayaran, pb.bukti_pembayaran,
               db.status_dokumen, db.jenis_dokumen, db.file_dokumen
        FROM peserta p 
        JOIN sekolah s ON p.sekolah_id = s.id 
        LEFT JOIN (
            SELECT p1.sekolah_id, p1.status_pembayaran, p1.nominal, p1.bukti_pembayaran
            FROM pembayaran p1
            INNER JOIN (
                SELECT sekolah_id, MAX(created_at) as max_created
                FROM pembayaran
                GROUP BY sekolah_id
            ) p2 ON p1.sekolah_id = p2.sekolah_id AND p1.created_at = p2.max_created
        ) pb ON p.sekolah_id = pb.sekolah_id
        LEFT JOIN (
            SELECT d1.sekolah_id, d1.status_dokumen, d1.jenis_dokumen, d1.file_dokumen
            FROM dokumen_berka d1
            INNER JOIN (
                SELECT sekolah_id, MAX(created_at) as max_created
                FROM dokumen_berka
                GROUP BY sekolah_id
            ) d2 ON d1.sekolah_id = d2.sekolah_id AND d1.created_at = d2.max_created
        ) db ON p.sekolah_id = db.sekolah_id
        ORDER BY p.created_at DESC
        LIMIT 10 OFFSET 0
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Count: " . count($res) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
