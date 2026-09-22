<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../auth/login_admin.php');
    exit();
}

/**
 * Import Sekolah from uploaded file (.xls, .xlsx, .csv)
 * 
 * Expected columns (case-insensitive):
 * Nama Sekolah, NPSN, Alamat, No HP, Email, Nama Kepala Sekolah, Username
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file_import'])) {
    $_SESSION['import_error'] = 'Tidak ada file yang diunggah.';
    header('Location: sekolah.php');
    exit();
}

$file = $_FILES['file_import'];

// Validate upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['import_error'] = 'Gagal mengunggah file. Silakan coba lagi.';
    header('Location: sekolah.php');
    exit();
}

// Validate file extension
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowedExtensions = ['xls', 'xlsx', 'csv'];

if (!in_array($extension, $allowedExtensions)) {
    $_SESSION['import_error'] = 'Format file tidak didukung. Gunakan file .xls, .xlsx, atau .csv';
    header('Location: sekolah.php');
    exit();
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['import_error'] = 'Ukuran file terlalu besar. Maksimal 5MB.';
    header('Location: sekolah.php');
    exit();
}

$filePath = $file['tmp_name'];
$rows = [];

try {
    // ==========================================
    // Parse file based on extension
    // ==========================================

    if ($extension === 'csv') {
        // ---- CSV Parsing ----
        $rows = parseCSV($filePath);

    } elseif ($extension === 'xlsx') {
        // ---- XLSX Parsing (try PhpSpreadsheet) ----
        if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            $rows = parseWithPhpSpreadsheet($filePath);
        } else {
            $_SESSION['import_error'] = 'Untuk mengimpor file .xlsx, diperlukan library PhpSpreadsheet. Silakan gunakan format .xls atau .csv.';
            header('Location: sekolah.php');
            exit();
        }

    } elseif ($extension === 'xls') {
        // ---- XLS Parsing ----
        // Try PhpSpreadsheet first, then fall back to HTML table parsing
        // (since exports from this system produce HTML tables saved as .xls)
        if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $rows = parseWithPhpSpreadsheet($filePath);
            } catch (Exception $e) {
                // PhpSpreadsheet failed, try HTML parsing
                $rows = parseHTMLTable($filePath);
            }
        } else {
            // No PhpSpreadsheet, try HTML parsing
            $rows = parseHTMLTable($filePath);
        }
    }

    if (empty($rows)) {
        $_SESSION['import_error'] = 'File kosong atau format tidak dapat dibaca.';
        header('Location: sekolah.php');
        exit();
    }

    // ==========================================
    // Map columns by header name (case-insensitive)
    // ==========================================
    $headerRow = array_shift($rows);
    $headerRow = array_map(function ($h) {
        return strtolower(trim($h));
    }, $headerRow);

    // Define column mappings: db_column => possible header names
    $columnMappings = [
        'nama_sekolah'        => ['nama sekolah', 'nama_sekolah'],
        'npsn'                => ['npsn'],
        'alamat_sekolah'      => ['alamat', 'alamat sekolah', 'alamat_sekolah'],
        'no_hp_sekolah'       => ['no hp', 'no_hp', 'no hp sekolah', 'no_hp_sekolah', 'telepon', 'hp'],
        'email_sekolah'       => ['email', 'email sekolah', 'email_sekolah'],
        'nama_kepala_sekolah' => ['nama kepala sekolah', 'nama_kepala_sekolah', 'kepala sekolah'],
        'username'            => ['username', 'user'],
    ];

    // Resolve column indices
    $columnIndices = [];
    foreach ($columnMappings as $dbCol => $possibleHeaders) {
        foreach ($possibleHeaders as $header) {
            $index = array_search($header, $headerRow);
            if ($index !== false) {
                $columnIndices[$dbCol] = $index;
                break;
            }
        }
    }

    // Validate required columns
    if (!isset($columnIndices['nama_sekolah']) || !isset($columnIndices['username'])) {
        $_SESSION['import_error'] = 'Kolom "Nama Sekolah" dan "Username" wajib ada di file. Pastikan header kolom sesuai.';
        header('Location: sekolah.php');
        exit();
    }

    // ==========================================
    // Process rows and insert into database
    // ==========================================
    $successCount = 0;
    $skipCount = 0;
    $failCount = 0;
    $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

    $insertStmt = $pdo->prepare("
        INSERT INTO sekolah (nama_sekolah, npsn, alamat_sekolah, no_hp_sekolah, email_sekolah, nama_kepala_sekolah, username, password, status, created_at)
        VALUES (:nama_sekolah, :npsn, :alamat_sekolah, :no_hp_sekolah, :email_sekolah, :nama_kepala_sekolah, :username, :password, :status, :created_at)
    ");

    $checkUsernameStmt = $pdo->prepare("SELECT COUNT(*) FROM sekolah WHERE username = :username");

    foreach ($rows as $row) {
        // Get values from mapped columns
        $namaSekolah      = getColumnValue($row, $columnIndices, 'nama_sekolah');
        $npsn             = getColumnValue($row, $columnIndices, 'npsn');
        $alamat           = getColumnValue($row, $columnIndices, 'alamat_sekolah');
        $noHp             = getColumnValue($row, $columnIndices, 'no_hp_sekolah');
        $email            = getColumnValue($row, $columnIndices, 'email_sekolah');
        $namaKepala       = getColumnValue($row, $columnIndices, 'nama_kepala_sekolah');
        $username         = getColumnValue($row, $columnIndices, 'username');

        // Skip if nama_sekolah or username is empty
        if (empty($namaSekolah) || empty($username)) {
            $skipCount++;
            continue;
        }

        // Check if username already exists
        $checkUsernameStmt->execute([':username' => $username]);
        if ($checkUsernameStmt->fetchColumn() > 0) {
            $skipCount++;
            continue;
        }

        // Insert into database
        try {
            $insertStmt->execute([
                ':nama_sekolah'        => $namaSekolah,
                ':npsn'                => $npsn,
                ':alamat_sekolah'      => $alamat,
                ':no_hp_sekolah'       => $noHp,
                ':email_sekolah'       => $email,
                ':nama_kepala_sekolah' => $namaKepala,
                ':username'            => $username,
                ':password'            => $defaultPassword,
                ':status'              => 'Aktif',
                ':created_at'          => date('Y-m-d H:i:s'),
            ]);
            $successCount++;
        } catch (PDOException $e) {
            $failCount++;
        }
    }

    // Build result message
    $message = "Import selesai: {$successCount} data berhasil diimpor.";
    if ($skipCount > 0) {
        $message .= " {$skipCount} data dilewati (kosong/duplikat).";
    }
    if ($failCount > 0) {
        $message .= " {$failCount} data gagal diimpor.";
    }

    if ($successCount > 0) {
        $_SESSION['import_success'] = $message;
    } else {
        $_SESSION['import_error'] = $message;
    }

} catch (Exception $e) {
    $_SESSION['import_error'] = 'Terjadi kesalahan saat memproses file: ' . $e->getMessage();
}

header('Location: sekolah.php');
exit();


// ==========================================
// Helper Functions
// ==========================================

/**
 * Get column value from a row using mapped column indices
 */
function getColumnValue(array $row, array $columnIndices, string $column): string
{
    if (!isset($columnIndices[$column])) {
        return '';
    }
    $index = $columnIndices[$column];
    return isset($row[$index]) ? trim($row[$index]) : '';
}

/**
 * Parse CSV file
 */
function parseCSV(string $filePath): array
{
    $rows = [];
    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        throw new Exception('Gagal membuka file CSV.');
    }

    // Detect delimiter
    $firstLine = fgets($handle);
    rewind($handle);

    $delimiter = ',';
    if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
        $delimiter = ';';
    } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
        $delimiter = "\t";
    }

    while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
        // Skip completely empty rows
        $filtered = array_filter($data, function ($val) {
            return trim($val) !== '';
        });
        if (!empty($filtered)) {
            $rows[] = $data;
        }
    }

    fclose($handle);
    return $rows;
}

/**
 * Parse HTML table from .xls file (HTML-based Excel export)
 */
function parseHTMLTable(string $filePath): array
{
    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new Exception('Gagal membaca file.');
    }

    // Check if content looks like HTML
    if (stripos($content, '<table') === false) {
        throw new Exception('File bukan format HTML table yang valid.');
    }

    $rows = [];

    // Suppress HTML parsing warnings
    libxml_use_internal_errors(true);

    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_NOERROR | LIBXML_NOWARNING);

    libxml_clear_errors();

    $tables = $dom->getElementsByTagName('table');
    if ($tables->length === 0) {
        throw new Exception('Tidak ditemukan tabel dalam file.');
    }

    // Use the first (or last, in case there's a title row) table found
    $table = $tables->item($tables->length - 1);
    $trs = $table->getElementsByTagName('tr');

    $headerFound = false;

    foreach ($trs as $tr) {
        $cells = [];
        $ths = $tr->getElementsByTagName('th');
        $tds = $tr->getElementsByTagName('td');

        if ($ths->length > 0) {
            // Header row
            foreach ($ths as $th) {
                $cells[] = trim($th->textContent);
            }
            $headerFound = true;
        } elseif ($tds->length > 0) {
            foreach ($tds as $td) {
                $cells[] = trim($td->textContent);
            }
        }

        if (!empty($cells)) {
            // Skip the "No" column if present (first column in exports is row number)
            // We detect this by checking if the first header is "No"
            $rows[] = $cells;
        }
    }

    // Remove the "No" column if the first header is "No"
    if (!empty($rows) && strtolower(trim($rows[0][0])) === 'no') {
        foreach ($rows as &$row) {
            array_shift($row);
        }
        unset($row);
    }

    return $rows;
}

/**
 * Parse file using PhpSpreadsheet library
 */
function parseWithPhpSpreadsheet(string $filePath): array
{
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = [];

    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $cells = [];
        foreach ($cellIterator as $cell) {
            $cells[] = trim((string) $cell->getValue());
        }

        // Skip completely empty rows
        $filtered = array_filter($cells, function ($val) {
            return $val !== '';
        });
        if (!empty($filtered)) {
            $rows[] = $cells;
        }
    }

    // Remove the "No" column if the first header is "No"
    if (!empty($rows) && strtolower(trim($rows[0][0])) === 'no') {
        foreach ($rows as &$row) {
            array_shift($row);
        }
        unset($row);
    }

    return $rows;
}
