# Setup Musabaqoh Hifdzul Qur'an

## Langkah-langkah Setup

### 1. Setup Database

Pastikan tabel musabaqoh sudah dibuat di database `lom_tahfidz`:

- `user_musabaqoh` - Data user login
- `soal_musabaqoh` - Data soal penyisihan Juz 30
- `soal_terpakai` - Tracking soal penyisihan yang sudah digunakan
- `soal_musabaqoh_final` - Data soal final Juz 30 (tingkat kesulitan tinggi)
- `soal_terpakai_final` - Tracking soal final yang sudah digunakan

**Setup Tabel Soal Final:**

```
http://localhost/lombatahfidz/setup_soal_final.php
```

### 2. Akses Login

- **URL Login**: `http://localhost/lombatahfidz/index.php`
- **Tab Musabaqoh**: Klik tab "Musabaqoh" di halaman utama

### 3. Kredensial Login

- **Admin**: `admin` / `admin123`
- **Juri**: `juri` / `juri123`
- **Peserta**: `peserta` / `peserta123`

### 4. Struktur Folder

```
musabaqoh/
├── index.php          (Dashboard utama)
├── pilih_jenis.php    (Pilih jenis soal)
├── home.php           (Pilih soal)
├── soal.php           (Tampil soal)
└── reset.php          (Reset soal)
```

### 5. Jenis Soal

- **Soal Penyisihan**: 2 Sambung Ayat + 1 Sambung Surat (Tingkat kesulitan menengah)
- **Soal Final**: 1 Sambung Ayat + 1 Tebak Surah + 1 Sambung Surah (Tingkat kesulitan tinggi)

### 6. Fitur

- **Dashboard Soal**: Pilih nomor soal untuk menampilkan 3 soal
- **Reset**: Reset semua soal yang sudah digunakan
- **Tracking**: Sistem tracking soal yang sudah dipilih

### 7. Database Tables

- `user_musabaqoh`: Data user login
- `soal_musabaqoh`: Data soal penyisihan Juz 30
- `soal_terpakai`: Tracking soal penyisihan yang sudah digunakan
- `soal_musabaqoh_final`: Data soal final Juz 30 (tingkat kesulitan tinggi)
- `soal_terpakai_final`: Tracking soal final yang sudah digunakan

## Troubleshooting

### Login Gagal

1. Pastikan MySQL/XAMPP sudah berjalan
2. Pastikan tabel musabaqoh sudah dibuat di database
3. Cek koneksi database di `config/database.php`

### Error Database

1. Pastikan database `lom_tahfidz` sudah dibuat
2. Pastikan port MySQL sesuai (default: 3307)
3. Cek username/password database
