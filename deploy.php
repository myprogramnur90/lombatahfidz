<?php
/**
 * Auto-Deploy Webhook Handler
 * Dipanggil otomatis oleh GitHub setiap kali ada git push
 */

// ⚠️ JANGAN ubah secret ini — harus sama dengan yang di GitHub Webhook Settings
define('WEBHOOK_SECRET', 'mhqs$2026#smpn1sumenep!deploy');

// Validasi request harus dari GitHub
$signature  = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload    = file_get_contents('php://input');

if (empty($signature) || empty($payload)) {
    http_response_code(400);
    die('Bad Request');
}

$expected = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);

if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    die('Unauthorized: Invalid signature');
}

// Jalankan git pull
$repo_path = '/home/u930333972/public_html';
$output = shell_exec("cd $repo_path && git pull origin main 2>&1");

// Log hasil deploy
$log = date('Y-m-d H:i:s') . " | DEPLOY OK\n" . $output . "\n---\n";
file_put_contents(__DIR__ . '/deploy.log', $log, FILE_APPEND);

http_response_code(200);
echo "Deploy selesai:\n";
echo "<pre>" . htmlspecialchars($output) . "</pre>";
?>
