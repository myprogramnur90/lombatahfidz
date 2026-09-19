<?php
require 'config/database.php';
$stmt = $pdo->query('SHOW CREATE TABLE penilaian_final');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
