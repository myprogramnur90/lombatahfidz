-- Database untuk Sistem Manajemen Pendaftaran Lomba Tahfidz
CREATE DATABASE IF NOT EXISTS lombatahfid;
USE lombatahfid;

-- Tabel untuk data sekolah
CREATE TABLE sekolah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sekolah VARCHAR(200) NOT NULL,
    npsn VARCHAR(20) UNIQUE,
    alamat_sekolah TEXT NOT NULL,
    no_hp_sekolah VARCHAR(15),
    email_sekolah VARCHAR(100),
    nama_kepala_sekolah VARCHAR(100),
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk data peserta
CREATE TABLE peserta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sekolah_id INT NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    nisn VARCHAR(20) NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    no_hp VARCHAR(15) NOT NULL,
    kelas VARCHAR(20),
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Diterima', 'Ditolak') DEFAULT 'Pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sekolah_id) REFERENCES sekolah(id) ON DELETE CASCADE
);

-- Tabel untuk data pembayaran
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sekolah_id INT NOT NULL,
    nominal DECIMAL(10,2) NOT NULL,
    bukti_pembayaran VARCHAR(255),
    status_pembayaran ENUM('Pending', 'Lunas', 'Ditolak') DEFAULT 'Pending',
    tanggal_bayar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sekolah_id) REFERENCES sekolah(id) ON DELETE CASCADE
);

-- Tabel untuk dokumen berka (surat keterangan)
CREATE TABLE dokumen_berka (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sekolah_id INT NOT NULL,
    jenis_dokumen ENUM('Surat Keterangan Aktif', 'KTS') NOT NULL,
    file_dokumen VARCHAR(255) NOT NULL,
    status_dokumen ENUM('Pending', 'Diterima', 'Ditolak') DEFAULT 'Pending',
    tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sekolah_id) REFERENCES sekolah(id) ON DELETE CASCADE
);

-- Tabel untuk admin
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin default
INSERT INTO admin (username, password, nama_lengkap, email) VALUES 
('admin', '$2y$10$192l632c5qituuf0.67G5OlIjGXkJLDqVW3defAwYPW4XqXAG795a', 'Administrator', 'admin@lombatahfidz.com');

-- Tabel untuk pengaturan sistem
CREATE TABLE pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengaturan VARCHAR(100) NOT NULL UNIQUE,
    nilai TEXT,
    keterangan TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk posting/pengumuman sekolah
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sekolah_id INT,
    judul VARCHAR(200) NOT NULL,
    konten TEXT NOT NULL,
    jenis_post ENUM('Persyaratan', 'Pengumuman', 'Informasi', 'Lainnya') DEFAULT 'Informasi',
    target_audience ENUM('Sekolah', 'Juri', 'Umum') DEFAULT 'Umum',
    status ENUM('Draft', 'Published') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sekolah_id) REFERENCES sekolah(id) ON DELETE SET NULL
);

-- Tabel untuk data juri
CREATE TABLE juri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    no_hp VARCHAR(15),
    spesialisasi VARCHAR(100),
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk penilaian peserta oleh juri (berdasarkan spesialisasi)
CREATE TABLE penilaian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    peserta_id INT NOT NULL,
    juri_id INT NOT NULL,
    skor_spesialisasi DECIMAL(5,2) NOT NULL DEFAULT 0,
    catatan TEXT,
    tanggal_penilaian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE CASCADE,
    FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
    UNIQUE KEY unique_penilaian (peserta_id, juri_id)
);

-- Tabel untuk assignment juri ke peserta (setiap juri hanya menilai 1 peserta)
CREATE TABLE assignment_juri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    juri_id INT NOT NULL,
    peserta_id INT NOT NULL,
    status ENUM('Assigned', 'Completed') DEFAULT 'Assigned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (juri_id) REFERENCES juri(id) ON DELETE CASCADE,
    FOREIGN KEY (peserta_id) REFERENCES peserta(id) ON DELETE CASCADE,
    UNIQUE KEY unique_juri_assignment (juri_id),
    UNIQUE KEY unique_peserta_assignment (peserta_id)
);

-- Insert pengaturan default
INSERT INTO pengaturan (nama_pengaturan, nilai, keterangan) VALUES 
('nama_lomba', 'Lomba Tahfidz Juz 30 Al-Quran 2024', 'Nama lomba yang sedang berlangsung'),
('tanggal_penutupan', '2024-12-31', 'Tanggal penutupan pendaftaran'),
('biaya_pendaftaran', '50000', 'Biaya pendaftaran dalam rupiah'),
('kontak_panitia', '081234567890', 'Nomor kontak panitia'),
('alamat_sekretariat', 'Jl. Contoh No. 123, Kota Contoh', 'Alamat sekretariat lomba'),
('maksimal_skor', '100', 'Skor maksimal untuk setiap aspek penilaian'),
('minimal_skor', '0', 'Skor minimal untuk setiap aspek penilaian');

-- Insert juri default (3 juri)
INSERT INTO juri (nama_lengkap, username, password, email, no_hp, spesialisasi) VALUES 
('Dr. Ahmad Al-Hafizh', 'juri1', '$2y$10$RvIqqihKT1t/iiJRiLmYru4Ml8uVA5YAm84Q9fOXZgwjZIx1zB/8W', 'juri1@lombatahfidz.com', '081111111111', 'Makhraj'),
('Ust. Muhammad Qari', 'juri2', '$2y$10$RvIqqihKT1t/iiJRiLmYru4Ml8uVA5YAm84Q9fOXZgwjZIx1zB/8W', 'juri2@lombatahfidz.com', '081222222222', 'Kelancaran Hafalan'),
('Ust. Abdullah Hafizh', 'juri3', '$2y$10$RvIqqihKT1t/iiJRiLmYru4Ml8uVA5YAm84Q9fOXZgwjZIx1zB/8W', 'juri3@lombatahfidz.com', '081333333333', 'Tajwid dan Adab');

CREATE TABLE soal_musabaqoh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_soal INT NOT NULL,
    soal1 TEXT NOT NULL,
    soal2 TEXT NOT NULL,
    soal3 TEXT NOT NULL,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert data soal Juz 30
INSERT INTO soal_musabaqoh (no_soal, soal1, soal2, soal3) VALUES
(1, 'Sambung Ayat - An-Naba ayat 1-5', 'Sambung Ayat - An-Naba ayat 6-10', 'Sambung Surat - An-Naba ke An-Nazi\'at'),
(2, 'Sambung Ayat - An-Nazi\'at ayat 1-5', 'Sambung Ayat - An-Nazi\'at ayat 6-10', 'Sambung Surat - An-Nazi\'at ke \'Abasa'),
(3, 'Sambung Ayat - \'Abasa ayat 1-5', 'Sambung Ayat - \'Abasa ayat 6-10', 'Sambung Surat - \'Abasa ke At-Takwir'),
(4, 'Sambung Ayat - At-Takwir ayat 1-5', 'Sambung Ayat - At-Takwir ayat 6-10', 'Sambung Surat - At-Takwir ke Al-Infitar'),
(5, 'Sambung Ayat - Al-Infitar ayat 1-5', 'Sambung Ayat - Al-Infitar ayat 6-10', 'Sambung Surat - Al-Infitar ke Al-Mutaffifin'),
(6, 'Sambung Ayat - Al-Mutaffifin ayat 1-5', 'Sambung Ayat - Al-Mutaffifin ayat 6-10', 'Sambung Surat - Al-Mutaffifin ke Al-Inshiqaq'),
(7, 'Sambung Ayat - Al-Inshiqaq ayat 1-5', 'Sambung Ayat - Al-Inshiqaq ayat 6-10', 'Sambung Surat - Al-Inshiqaq ke Al-Buruj'),
(8, 'Sambung Ayat - Al-Buruj ayat 1-5', 'Sambung Ayat - Al-Buruj ayat 6-10', 'Sambung Surat - Al-Buruj ke At-Tariq'),
(9, 'Sambung Ayat - At-Tariq ayat 1-5', 'Sambung Ayat - At-Tariq ayat 6-10', 'Sambung Surat - At-Tariq ke Al-A\'la'),
(10, 'Sambung Ayat - Al-A\'la ayat 1-5', 'Sambung Ayat - Al-A\'la ayat 6-10', 'Sambung Surat - Al-A\'la ke Al-Ghashiyah');

-- Tabel untuk tracking soal yang sudah digunakan
CREATE TABLE soal_terpakai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_soal INT NOT NULL,
    tanggal_pakai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Aktif', 'Reset') DEFAULT 'Aktif',
    FOREIGN KEY (no_soal) REFERENCES soal_musabaqoh(no_soal) ON DELETE CASCADE
);

-- Tabel untuk user musabaqoh (akses soal)
CREATE TABLE user_musabaqoh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert user default untuk musabaqoh
INSERT INTO user_musabaqoh (username, password, nama_lengkap) VALUES 
('admin', '$2y$10$192l632c5qituuf0.67G5OlIjGXkJLDqVW3defAwYPW4XqXAG795a', 'Admin Musabaqoh'),
('juri', '$2y$10$RvIqqihKT1t/iiJRiLmYru4Ml8uVA5YAm84Q9fOXZgwjZIx1zB/8W', 'Juri Musabaqoh'),
('peserta', '$2y$10$O.tERJleO6PTT/fRb.VqjOS49Tw.uRn60RtxMk1eF0h5NuFccK04a', 'Peserta Musabaqoh');

-- Tabel untuk soal musabaqoh final
CREATE TABLE soal_musabaqoh_final (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_soal INT NOT NULL,
    soal1 TEXT NOT NULL,
    soal2 TEXT NOT NULL,
    soal3 TEXT NOT NULL,
    status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_no_soal_final (no_soal)
);

-- Insert data soal Final Juz 30 (3 jenis soal: Sambung Ayat, Tebak Surah, Sambung Surah)
INSERT INTO soal_musabaqoh_final (no_soal, soal1, soal2, soal3) VALUES
(1, 'Sambung Ayat - An-Naba ayat 11-15', 'Tebak Surah - An-Nazi\'at', 'Sambung Surah - An-Naba ke An-Nazi\'at'),
(2, 'Sambung Ayat - An-Nazi\'at ayat 11-15', 'Tebak Surah - \'Abasa', 'Sambung Surah - An-Nazi\'at ke \'Abasa'),
(3, 'Sambung Ayat - \'Abasa ayat 11-15', 'Tebak Surah - At-Takwir', 'Sambung Surah - \'Abasa ke At-Takwir'),
(4, 'Sambung Ayat - At-Takwir ayat 11-15', 'Tebak Surah - Al-Infitar', 'Sambung Surah - At-Takwir ke Al-Infitar'),
(5, 'Sambung Ayat - Al-Infitar ayat 11-15', 'Tebak Surah - Al-Mutaffifin', 'Sambung Surah - Al-Infitar ke Al-Mutaffifin'),
(6, 'Sambung Ayat - Al-Mutaffifin ayat 11-15', 'Tebak Surah - Al-Inshiqaq', 'Sambung Surah - Al-Mutaffifin ke Al-Inshiqaq'),
(7, 'Sambung Ayat - Al-Inshiqaq ayat 11-15', 'Tebak Surah - Al-Buruj', 'Sambung Surah - Al-Inshiqaq ke Al-Buruj'),
(8, 'Sambung Ayat - Al-Buruj ayat 11-15', 'Tebak Surah - At-Tariq', 'Sambung Surah - Al-Buruj ke At-Tariq'),
(9, 'Sambung Ayat - At-Tariq ayat 11-15', 'Tebak Surah - Al-A\'la', 'Sambung Surah - At-Tariq ke Al-A\'la'),
(10, 'Sambung Ayat - Al-A\'la ayat 11-15', 'Tebak Surah - Al-Ghashiyah', 'Sambung Surah - Al-A\'la ke Al-Ghashiyah');

-- Tabel untuk tracking soal final yang sudah digunakan
CREATE TABLE soal_terpakai_final (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_soal INT NOT NULL,
    tanggal_pakai TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Aktif', 'Reset') DEFAULT 'Aktif',
    FOREIGN KEY (no_soal) REFERENCES soal_musabaqoh_final(no_soal) ON DELETE CASCADE
);

