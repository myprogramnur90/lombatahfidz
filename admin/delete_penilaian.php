<?php
session_start();
require_once '../config/database.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

if (!isset($_GET['id'])) {
    die('Missing participant ID');
}

$peserta_id = (int)$_GET['id'];

try {
    // Delete all penilaian rows for this participant
    $stmt = $pdo->prepare('DELETE FROM penilaian WHERE peserta_id = :peserta_id');
    $stmt->execute(['peserta_id' => $peserta_id]);
    // Optionally, you could also delete the participant record
    // $stmt2 = $pdo->prepare('DELETE FROM peserta WHERE id = :peserta_id');
    // $stmt2->execute(['peserta_id' => $peserta_id]);
    header('Location: laporan_penilaian.php?msg=deleted');
    exit();
} catch (Exception $e) {
    die('Error deleting data: ' . $e->getMessage());
}
?>