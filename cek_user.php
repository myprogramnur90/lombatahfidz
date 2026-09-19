<?php
$hash_db = '17e8a079455e4fd957b9af0d4a0f0d3b';

$candidates = [
    'admin123', 'admin', 'soal', 'soal123', 'password', '123456',
    'juri123', 'peserta123', 'musabaqoh', 'musabaqoh123',
    'tahfidz', 'tahfidz123', 'lomba123', 'lomba',
    '12345678', '1234567890', 'qwerty', 'abc123',
    'bismillah', 'bismillah123', 'panitia', 'panitia123',
    'soal1234', 'soal12345', 'Soal123', 'SOAL123'
];

$found = false;
foreach ($candidates as $pass) {
    if (md5($pass) === $hash_db) {
        echo "COCOK! Password: $pass\n";
        $found = true;
        break;
    }
}

if (!$found) {
    echo "Tidak ditemukan dari daftar password umum.\n";
    echo "Hash MD5: $hash_db\n";
}
