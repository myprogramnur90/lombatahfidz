<?php
require 'config/database.php';
$pdo->exec("ALTER TABLE assignment_juri_final DROP FOREIGN KEY assignment_juri_final_ibfk_1");
$pdo->exec("ALTER TABLE assignment_juri_final DROP FOREIGN KEY assignment_juri_final_ibfk_2");

$pdo->exec("ALTER TABLE assignment_juri_final DROP INDEX unique_juri_final_assignment");
$pdo->exec("ALTER TABLE assignment_juri_final DROP INDEX unique_peserta_final_assignment");

$pdo->exec("ALTER TABLE assignment_juri_final ADD UNIQUE KEY unique_juri_peserta_final (juri_id, peserta_final_id)");

$pdo->exec("ALTER TABLE assignment_juri_final ADD CONSTRAINT assignment_juri_final_ibfk_1 FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE");
$pdo->exec("ALTER TABLE assignment_juri_final ADD CONSTRAINT assignment_juri_final_ibfk_2 FOREIGN KEY (peserta_final_id) REFERENCES peserta_final(id) ON DELETE CASCADE");

echo "Done";
