<?php
require 'config/database.php';
try {
    $query = "SELECT p.* FROM peserta p LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([10, 0]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Count: " . count($res) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
