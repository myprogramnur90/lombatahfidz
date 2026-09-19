# Sistem Soal Musabaqoh Baru

## Overview

Sistem soal musabaqoh baru telah dibuat dengan struktur yang lebih baik dan fitur-fitur yang lebih lengkap dibandingkan sistem lama.

## Fitur Baru

### 1. Kategori Soal

- **Sambung Ayat**: Soal untuk melanjutkan ayat Al-Quran
- **Tebak Surah**: Soal untuk menebak nama surah berdasarkan petunjuk
- **Sambung Surah**: Soal untuk melanjutkan dari satu surah ke surah berikutnya
- **Hafalan Juz 30**: Soal khusus untuk hafalan Juz Amma
- **Hafalan Juz 1**: Soal khusus untuk hafalan Juz Al-Fatihah
- **Hafalan Pilihan**: Soal hafalan surah pilihan

### 2. Tingkat Kesulitan

- **Mudah**: Skor 0-40
- **Sedang**: Skor 41-70
- **Sulit**: Skor 71-85
- **Sangat Sulit**: Skor 86-100

### 3. Fitur Tambahan

- **Kunci Jawaban**: Penyimpanan kunci jawaban untuk setiap soal
- **Waktu Pengerjaan**: Pengaturan waktu pengerjaan per soal (dalam detik)
- **Skor Maksimal**: Pengaturan skor maksimal per soal
- **Status Soal**: Draft, Aktif, Nonaktif
- **Riwayat Soal**: Tracking penggunaan soal dan skor peserta

## File yang Dibuat

### 1. Database

- `create_soal_new_structure.sql` - Script SQL untuk membuat struktur database baru
- `setup_soal_new.php` - Script PHP untuk setup struktur baru

### 2. Admin Panel

- `admin/reset_soal.php` - Halaman untuk menghapus semua data soal lama
- `admin/soal_musabaqoh_new.php` - Halaman kelola soal baru
- `admin/kelola_kategori_soal.php` - Halaman kelola kategori dan tingkat kesulitan

### 3. Menu Admin

- Menu admin telah diupdate untuk menambahkan link ke halaman-halaman baru

## Cara Penggunaan

### 1. Reset Data Soal Lama

1. Buka `admin/reset_soal.php`
2. Konfirmasi untuk menghapus semua data soal lama
3. Data akan dihapus secara permanen

### 2. Setup Struktur Baru

1. Buka `setup_soal_new.php`
2. Klik "Setup Struktur Baru"
3. Struktur database baru akan dibuat

### 3. Kelola Kategori & Tingkat

1. Buka `admin/kelola_kategori_soal.php`
2. Tambah/edit/hapus kategori soal
3. Tambah/edit/hapus tingkat kesulitan

### 4. Kelola Soal

1. Buka `admin/soal_musabaqoh_new.php`
2. Tambah soal baru dengan kategori dan tingkat kesulitan
3. Edit atau hapus soal yang sudah ada

## Struktur Database

### Tabel Baru

1. **kategori_soal** - Menyimpan kategori soal
2. **tingkat_kesulitan** - Menyimpan tingkat kesulitan
3. **soal_musabaqoh_new** - Menyimpan soal dengan struktur baru
4. **riwayat_soal** - Menyimpan riwayat penggunaan soal

### Tabel Lama (Akan Dihapus)

1. **soal_musabaqoh** - Tabel soal penyisihan lama
2. **soal_musabaqoh_final** - Tabel soal final lama
3. **soal_terpakai_final** - Tabel tracking soal lama

## Keunggulan Sistem Baru

1. **Organisasi Lebih Baik**: Soal dikelompokkan berdasarkan kategori dan tingkat kesulitan
2. **Fleksibilitas**: Mudah menambah kategori dan tingkat kesulitan baru
3. **Tracking**: Riwayat penggunaan soal dan skor peserta
4. **Kunci Jawaban**: Penyimpanan kunci jawaban untuk evaluasi otomatis
5. **Waktu & Skor**: Pengaturan waktu pengerjaan dan skor maksimal per soal
6. **Status Management**: Pengelolaan status soal (Draft, Aktif, Nonaktif)

## Langkah Selanjutnya

1. **Migrasi Data**: Jika ada data soal lama yang ingin dipindahkan, buat script migrasi
2. **Integrasi Frontend**: Update halaman musabaqoh untuk menggunakan struktur baru
3. **Testing**: Test sistem baru untuk memastikan berfungsi dengan baik
4. **Training**: Berikan training kepada admin untuk menggunakan sistem baru

## Catatan Penting

- **Backup Database**: Selalu backup database sebelum melakukan perubahan
- **Testing**: Test semua fitur sebelum digunakan di production
- **Dokumentasi**: Update dokumentasi jika ada perubahan struktur
- **Monitoring**: Monitor penggunaan sistem baru untuk memastikan berjalan dengan baik

