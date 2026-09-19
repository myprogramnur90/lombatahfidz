-- Script untuk membuat struktur soal baru yang lebih baik
USE lom_tahfidz;

-- Hapus tabel lama jika ada
DROP TABLE IF EXISTS soal_terpakai_final;
DROP TABLE IF EXISTS soal_musabaqoh_final;
DROP TABLE IF EXISTS soal_musabaqoh;

-- Tabel kategori soal
CREATE TABLE IF NOT EXISTS kategori_soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel tingkat kesulitan
CREATE TABLE IF NOT EXISTS tingkat_kesulitan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tingkat VARCHAR(50) NOT NULL,
    skor_min INT DEFAULT 0,
    skor_max INT DEFAULT 100,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel soal musabaqoh baru dengan struktur yang lebih baik
CREATE TABLE IF NOT EXISTS soal_musabaqoh_new (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_soal INT NOT NULL,
    kategori_id INT NOT NULL,
    tingkat_id INT NOT NULL,
    jenis_soal ENUM('Penyisihan', 'Final') NOT NULL,
    judul_soal VARCHAR(255) NOT NULL,
    soal1 TEXT NOT NULL,
    soal2 TEXT NOT NULL,
    soal3 TEXT NOT NULL,
    kunci_jawaban1 TEXT,
    kunci_jawaban2 TEXT,
    kunci_jawaban3 TEXT,
    waktu_pengerjaan INT DEFAULT 300, -- dalam detik
    skor_maksimal INT DEFAULT 100,
    status ENUM('Aktif', 'Nonaktif', 'Draft') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    updated_by INT,
    UNIQUE KEY unique_no_soal_jenis (no_soal, jenis_soal),
    FOREIGN KEY (kategori_id) REFERENCES kategori_soal(id) ON DELETE RESTRICT,
    FOREIGN KEY (tingkat_id) REFERENCES tingkat_kesulitan(id) ON DELETE RESTRICT
);

-- Tabel riwayat penggunaan soal
CREATE TABLE IF NOT EXISTS riwayat_soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    soal_id INT NOT NULL,
    peserta_id INT,
    tanggal_pakai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Aktif', 'Selesai', 'Reset') DEFAULT 'Aktif',
    skor_diperoleh INT DEFAULT 0,
    waktu_pengerjaan INT DEFAULT 0,
    jawaban_peserta TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (soal_id) REFERENCES soal_musabaqoh_new(id) ON DELETE CASCADE
);

-- Insert data kategori soal
INSERT INTO kategori_soal (nama_kategori, deskripsi) VALUES
('Sambung Ayat', 'Soal untuk melanjutkan ayat Al-Quran'),
('Tebak Surah', 'Soal untuk menebak nama surah berdasarkan petunjuk'),
('Sambung Surah', 'Soal untuk melanjutkan dari satu surah ke surah berikutnya'),
('Hafalan Juz 30', 'Soal khusus untuk hafalan Juz Amma'),
('Hafalan Juz 1', 'Soal khusus untuk hafalan Juz Al-Fatihah'),
('Hafalan Pilihan', 'Soal hafalan surah pilihan');

-- Insert data tingkat kesulitan
INSERT INTO tingkat_kesulitan (nama_tingkat, skor_min, skor_max) VALUES
('Mudah', 0, 40),
('Sedang', 41, 70),
('Sulit', 71, 85),
('Sangat Sulit', 86, 100);

-- Insert data soal penyisihan baru
INSERT INTO soal_musabaqoh_new (no_soal, kategori_id, tingkat_id, jenis_soal, judul_soal, soal1, soal2, soal3, waktu_pengerjaan, skor_maksimal, status) VALUES
(1, 1, 1, 'Penyisihan', 'Sambung Ayat Juz 30 - Mudah', 
 'Sambung ayat: "Maa yatasaalun" (An-Naba: 1)',
 'Sambung ayat: "Am anta qoola lil muslimin" (An-Naba: 2)', 
 'Sambung ayat: "Innaa anzalnaahu fii lailatil qadr" (Al-Qadr: 1)',
 300, 100, 'Aktif'),

(2, 1, 2, 'Penyisihan', 'Sambung Ayat Juz 30 - Sedang',
 'Sambung ayat: "Wa maa adraaka maa lailatul qadr" (Al-Qadr: 2)',
 'Sambung ayat: "Lailatul qadri khairun min alfi syahr" (Al-Qadr: 3)',
 'Sambung ayat: "Tanazzalul malaaikatu war ruuhu fiihaa" (Al-Qadr: 4)',
 300, 100, 'Aktif'),

(3, 2, 2, 'Penyisihan', 'Tebak Surah Juz 30',
 'Surah yang dimulai dengan "Qul a\'uudzu bi rabbin naas" adalah...',
 'Surah yang dimulai dengan "Qul a\'uudzu bi rabbil falaq" adalah...',
 'Surah yang dimulai dengan "Izaa jaa-a nashrullaahi wal fath" adalah...',
 300, 100, 'Aktif'),

(4, 3, 3, 'Penyisihan', 'Sambung Surah Juz 30 - Sulit',
 'Sambung dari surah An-Naba ke surah berikutnya',
 'Sambung dari surah An-Nazi\'at ke surah berikutnya',
 'Sambung dari surah \'Abasa ke surah berikutnya',
 300, 100, 'Aktif'),

(5, 4, 1, 'Penyisihan', 'Hafalan Juz 30 - Mudah',
 'Hafalkan surah Al-Fatihah ayat 1-3',
 'Hafalkan surah Al-Ikhlas lengkap',
 'Hafalkan surah Al-Falaq lengkap',
 300, 100, 'Aktif');

-- Insert data soal final baru
INSERT INTO soal_musabaqoh_new (no_soal, kategori_id, tingkat_id, jenis_soal, judul_soal, soal1, soal2, soal3, waktu_pengerjaan, skor_maksimal, status) VALUES
(1, 1, 3, 'Final', 'Sambung Ayat Final - Sulit',
 'Sambung ayat: "Wa maa adraaka maa lailatul qadr" (Al-Qadr: 2)',
 'Sambung ayat: "Lailatul qadri khairun min alfi syahr" (Al-Qadr: 3)',
 'Sambung ayat: "Tanazzalul malaaikatu war ruuhu fiihaa" (Al-Qadr: 4)',
 300, 100, 'Aktif'),

(2, 2, 4, 'Final', 'Tebak Surah Final - Sangat Sulit',
 'Surah yang memiliki 3 ayat dan dimulai dengan "Qul huwallaahu ahad" adalah...',
 'Surah yang memiliki 5 ayat dan dimulai dengan "Qul a\'uudzu bi rabbil falaq" adalah...',
 'Surah yang memiliki 6 ayat dan dimulai dengan "Qul a\'uudzu bi rabbin naas" adalah...',
 300, 100, 'Aktif'),

(3, 3, 4, 'Final', 'Sambung Surah Final - Sangat Sulit',
 'Sambung dari surah At-Takwir ke surah berikutnya',
 'Sambung dari surah Al-Infitar ke surah berikutnya',
 'Sambung dari surah Al-Mutaffifin ke surah berikutnya',
 300, 100, 'Aktif'),

(4, 4, 3, 'Final', 'Hafalan Final - Sulit',
 'Hafalkan surah Al-Fatihah lengkap dengan tajwid yang benar',
 'Hafalkan surah Al-Baqarah ayat 1-5',
 'Hafalkan surah Al-Baqarah ayat 255 (Ayat Kursi)',
 300, 100, 'Aktif'),

(5, 5, 4, 'Final', 'Hafalan Juz 1 Final - Sangat Sulit',
 'Hafalkan surah Al-Baqarah ayat 1-10',
 'Hafalkan surah Al-Baqarah ayat 11-20',
 'Hafalkan surah Al-Baqarah ayat 21-30',
 300, 100, 'Aktif');

