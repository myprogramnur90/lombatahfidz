<?php
require 'config/database.php';
$pdo->query("UPDATE musabaqoh_aktif SET status = 'Aktif' WHERE id = 1");
$stmt = $pdo->query("SELECT * FROM musabaqoh_aktif WHERE status = 'Aktif'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
