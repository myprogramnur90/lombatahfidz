<?php
// API endpoint untuk get/set peserta dan soal yang sedang aktif
session_start();
require_once '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';
$jenis = $_GET['jenis'] ?? $_POST['jenis'] ?? 'penyisihan';

// GET: Ambil data aktif saat ini (bisa diakses juri tanpa login musabaqoh)
if ($action === 'get') {
    try {
        $stmt = $pdo->prepare("
            SELECT ma.*, p.nama_lengkap, p.nisn, s.nama_sekolah,
                   sm.soal1, sm.soal2, sm.soal3
            FROM musabaqoh_aktif ma
            LEFT JOIN peserta p ON ma.peserta_id = p.id
            LEFT JOIN sekolah s ON p.sekolah_id = s.id
            LEFT JOIN soal_musabaqoh sm ON ma.no_soal = sm.no_soal AND ma.jenis = 'penyisihan'
            WHERE ma.jenis = ? AND ma.status = 'Aktif'
            ORDER BY ma.updated_at DESC
            LIMIT 1
        ");
        $stmt->execute([$jenis]);
        $aktif = $stmt->fetch();

        // Jika jenis final, ambil soal dari tabel final
        if ($aktif && $jenis === 'final') {
            $stmtSoal = $pdo->prepare("SELECT soal1, soal2, soal3 FROM soal_musabaqoh_final WHERE no_soal = ?");
            $stmtSoal->execute([$aktif['no_soal']]);
            $soalData = $stmtSoal->fetch();
            if ($soalData) {
                $aktif['soal1'] = $soalData['soal1'];
                $aktif['soal2'] = $soalData['soal2'];
                $aktif['soal3'] = $soalData['soal3'];
            }
        }

        if ($aktif) {
            echo json_encode([
                'status' => 'ok',
                'data' => [
                    'peserta_id' => $aktif['peserta_id'],
                    'nama_lengkap' => $aktif['nama_lengkap'],
                    'nisn' => $aktif['nisn'],
                    'nama_sekolah' => $aktif['nama_sekolah'],
                    'no_soal' => $aktif['no_soal'],
                    'soal1' => $aktif['soal1'],
                    'soal2' => $aktif['soal2'],
                    'soal3' => $aktif['soal3'],
                    'jenis' => $aktif['jenis'],
                    'updated_at' => $aktif['updated_at']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'empty', 'data' => null]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

// SET: Hanya bisa diakses oleh user musabaqoh yang login
if (!isset($_SESSION['musabaqoh_logged_in']) || !$_SESSION['musabaqoh_logged_in']) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($action === 'set') {
    $peserta_id = isset($_POST['peserta_id']) ? (int)$_POST['peserta_id'] : null;
    $no_soal = isset($_POST['no_soal']) ? (int)$_POST['no_soal'] : null;

    if (!$peserta_id) {
        echo json_encode(['status' => 'error', 'message' => 'Peserta harus dipilih']);
        exit();
    }

    try {
        // Nonaktifkan semua yang aktif untuk jenis ini
        $pdo->prepare("UPDATE musabaqoh_aktif SET status = 'Selesai' WHERE jenis = ? AND status = 'Aktif'")
            ->execute([$jenis]);

        // Insert baru
        $stmt = $pdo->prepare("INSERT INTO musabaqoh_aktif (jenis, peserta_id, no_soal, status) VALUES (?, ?, ?, 'Aktif')");
        $stmt->execute([$jenis, $peserta_id, $no_soal]);

        echo json_encode(['status' => 'ok', 'message' => 'Peserta dan soal aktif berhasil diset']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

if ($action === 'clear') {
    try {
        $pdo->prepare("UPDATE musabaqoh_aktif SET status = 'Selesai' WHERE jenis = ? AND status = 'Aktif'")
            ->execute([$jenis]);
        echo json_encode(['status' => 'ok', 'message' => 'Berhasil dikosongkan']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

// GET peserta list untuk dropdown
if ($action === 'peserta_list') {
    try {
        $stmt = $pdo->query("
            SELECT p.id, p.nama_lengkap, p.nisn, s.nama_sekolah
            FROM peserta p
            JOIN sekolah s ON p.sekolah_id = s.id
            WHERE p.status = 'Diterima'
            ORDER BY p.nama_lengkap
        ");
        $peserta = $stmt->fetchAll();
        echo json_encode(['status' => 'ok', 'data' => $peserta]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
