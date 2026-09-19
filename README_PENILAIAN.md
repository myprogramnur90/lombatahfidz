# Fitur Penilaian Lomba Tahfidz

## Deskripsi

Fitur penilaian memungkinkan 3 juri untuk menilai peserta lomba tahfidz dengan sistem **assignment 1:1** (setiap juri hanya menilai 1 peserta):

- **Tajwid** (0-100): Penilaian ketepatan hukum tajwid
- **Fluency** (0-100): Penilaian kelancaran membaca
- **Makhraj** (0-100): Penilaian ketepatan makhraj huruf
- **Keseluruhan** (Otomatis): Dihitung otomatis dari rata-rata 3 aspek penilaian

### Sistem Assignment:

- ✅ **1 Juri = 1 Peserta**: Setiap juri hanya menilai 1 peserta tertentu
- ✅ **1 Peserta = 1 Juri**: Setiap peserta hanya dinilai oleh 1 juri
- ✅ **Assignment Management**: Admin mengatur assignment juri ke peserta
- ✅ **Access Control**: Juri hanya bisa mengakses peserta yang di-assign

## Instalasi

### 1. Update Database

Jalankan file `update_database_penilaian.php` untuk menambahkan tabel dan data yang diperlukan:

```
http://localhost/lombatahfidz/update_database_penilaian.php
```

### 2. Setup Assignment (Wajib)

Jalankan file `update_database_assignment.php` untuk membuat sistem assignment:

```
http://localhost/lombatahfidz/update_database_assignment.php
```

### 3. Struktur Database Baru

#### Tabel `juri`

- `id`: Primary key
- `nama_lengkap`: Nama lengkap juri
- `username`: Username untuk login
- `password`: Password (MD5 encrypted)
- `email`: Email juri
- `no_hp`: Nomor HP juri
- `spesialisasi`: Spesialisasi juri
- `status`: Status aktif/nonaktif
- `created_at`, `updated_at`: Timestamp

#### Tabel `penilaian`

- `id`: Primary key
- `peserta_id`: ID peserta yang dinilai
- `juri_id`: ID juri yang menilai
- `skor_tajwid`: Skor tajwid (0-100)
- `skor_fluency`: Skor fluency (0-100)
- `skor_makhraj`: Skor makhraj (0-100)
- `skor_keseluruhan`: Skor keseluruhan (0-100)
- `catatan`: Catatan penilaian
- `tanggal_penilaian`: Tanggal penilaian
- `created_at`, `updated_at`: Timestamp

#### Tabel `assignment_juri` (Baru)

- `id`: Primary key
- `juri_id`: ID juri (foreign key, unique)
- `peserta_id`: ID peserta (foreign key, unique)
- `status`: Status assignment (Assigned/Completed)
- `created_at`, `updated_at`: Timestamp

## Kredensial Login Default

### Juri 1

- **Username**: juri1
- **Password**: juri123
- **Nama**: Dr. Ahmad Al-Hafizh
- **Spesialisasi**: Tajwid dan Makhraj

### Juri 2

- **Username**: juri2
- **Password**: juri123
- **Nama**: Ust. Muhammad Qari
- **Spesialisasi**: Fluency dan Tartil

### Juri 3

- **Username**: juri3
- **Password**: juri123
- **Nama**: Ust. Abdullah Hafizh
- **Spesialisasi**: Keseluruhan dan Hafalan

## Halaman dan Fitur

### 1. Login Juri

- **URL**: `auth/login_juri.php`
- **Fitur**: Login untuk juri dengan validasi kredensial

### 2. Dashboard Juri

- **URL**: `juri/dashboard.php`
- **Fitur**:
  - Statistik penilaian (total peserta, sudah dinilai, belum dinilai)
  - **Hanya menampilkan peserta yang di-assign ke juri tersebut**
  - Daftar peserta yang belum dinilai
  - Daftar peserta yang sudah dinilai
  - Quick access ke form penilaian
  - **Peringatan jika belum ada assignment**

### 3. Form Penilaian

- **URL**: `juri/penilaian.php?id={peserta_id}`
- **Fitur**:
  - Form input skor untuk 3 aspek penilaian (Tajwid, Fluency, Makhraj)
  - **Skor keseluruhan dihitung otomatis** dari rata-rata 3 aspek
  - Panduan penilaian yang detail
  - Validasi skor (0-100)
  - Update penilaian yang sudah ada
  - Real-time calculation dengan JavaScript
  - **Hanya bisa diakses untuk peserta yang di-assign ke juri tersebut**

### 4. Daftar Peserta

- **URL**: `juri/daftar_peserta.php`
- **Fitur**:
  - Daftar semua peserta yang diterima
  - Status penilaian per peserta
  - Quick access ke form penilaian

### 5. Riwayat Penilaian

- **URL**: `juri/riwayat_penilaian.php`
- **Fitur**:
  - Riwayat penilaian yang telah dilakukan
  - Statistik penilaian juri
  - Edit penilaian yang sudah ada

### 6. Profil Juri

- **URL**: `juri/profil.php`
- **Fitur**:
  - Edit profil juri
  - Ubah password
  - Informasi akun

### 7. Manajemen Juri (Admin)

- **URL**: `admin/juri.php`
- **Fitur**:
  - Tambah juri baru
  - Edit data juri
  - Reset password juri
  - Hapus juri (jika belum ada penilaian)
  - Status aktif/nonaktif

### 8. Assignment Juri (Admin)

- **URL**: `admin/assignment_juri.php`
- **Fitur**:
  - **Tambah Assignment**: Assign juri ke peserta (1:1)
  - **Lihat Assignment**: Daftar semua assignment aktif
  - **Hapus Assignment**: Hapus assignment jika diperlukan
  - **Statistik**: Juri belum di-assign, peserta belum di-assign, assignment aktif
  - **Validasi**: Mencegah double assignment (juri/peserta sudah di-assign)

### 9. Laporan Penilaian (Admin)

- **URL**: `admin/laporan_penilaian.php`
- **Fitur**:
  - Ranking peserta berdasarkan skor rata-rata
  - Statistik penilaian
  - Progress penilaian
  - Export ke Excel
  - Detail penilaian per peserta

### 9. Detail Penilaian (Admin)

- **URL**: `admin/detail_penilaian.php?id={peserta_id}`
- **Fitur**:
  - Detail penilaian dari semua juri
  - Rekapitulasi skor rata-rata
  - Data lengkap peserta

## Cara Penggunaan

### Untuk Juri:

1. Login menggunakan kredensial yang diberikan
2. Lihat dashboard untuk melihat peserta yang perlu dinilai
3. Klik "Nilai" pada peserta yang belum dinilai
4. Isi form penilaian dengan skor 0-100 untuk setiap aspek
5. Berikan catatan jika diperlukan
6. Simpan penilaian

### Untuk Admin:

1. Login sebagai admin
2. **Assignment Juri**: Akses menu "Assignment Juri" untuk mengatur assignment (1:1)
3. **Kelola Juri**: Akses menu "Kelola Juri" untuk mengelola data juri
4. **Laporan**: Akses menu "Laporan Penilaian" untuk melihat hasil penilaian
5. Export laporan ke Excel jika diperlukan

## Perhitungan Skor Otomatis

### Cara Kerja:

1. **Juri mengisi 3 aspek penilaian**: Tajwid, Fluency, Makhraj (0-100)
2. **Sistem menghitung otomatis**: Skor Keseluruhan = (Tajwid + Fluency + Makhraj) ÷ 3
3. **Pembulatan**: Hasil dibulatkan ke 2 desimal
4. **Real-time**: Perhitungan muncul langsung saat juri mengisi skor

### Contoh:

- Tajwid: 85
- Fluency: 90
- Makhraj: 88
- **Keseluruhan**: (85 + 90 + 88) ÷ 3 = 87.67

### Keuntungan:

- ✅ Konsistensi penilaian
- ✅ Mengurangi kesalahan manual
- ✅ Transparansi perhitungan
- ✅ Efisiensi waktu juri

## Keuntungan Sistem Assignment (1:1)

### Untuk Juri:

- ✅ **Fokus**: Juri fokus menilai 1 peserta dengan detail dan teliti
- ✅ **Konsistensi**: Penilaian lebih konsisten karena fokus pada 1 peserta
- ✅ **Efisiensi**: Tidak bingung memilih peserta mana yang harus dinilai
- ✅ **Kualitas**: Penilaian lebih berkualitas karena waktu yang cukup

### Untuk Admin:

- ✅ **Kontrol**: Admin memiliki kontrol penuh atas assignment
- ✅ **Organisasi**: Sistem lebih terorganisir dan terstruktur
- ✅ **Monitoring**: Mudah memantau progress penilaian
- ✅ **Fleksibilitas**: Bisa mengubah assignment jika diperlukan

### Untuk Sistem:

- ✅ **Keamanan**: Mencegah akses tidak sah ke data peserta
- ✅ **Integritas**: Memastikan setiap peserta dinilai oleh 1 juri
- ✅ **Audit**: Mudah melacak siapa yang menilai siapa
- ✅ **Scalability**: Sistem bisa dikembangkan untuk lomba yang lebih besar

## Validasi dan Keamanan

- **Validasi Skor**: Semua skor harus dalam rentang 0-100
- **Unique Constraint**: Setiap juri hanya bisa menilai peserta sekali
- **Assignment Validation**: Juri hanya bisa mengakses peserta yang di-assign
- **Double Assignment Prevention**: Mencegah juri/peserta di-assign ke multiple assignment
- **Session Management**: Login/logout yang aman
- **Input Sanitization**: Semua input divalidasi dan disanitasi
- **Password Security**: Password dienkripsi menggunakan MD5

## Panduan Penilaian

### Tajwid (0-100)

- 90-100: Sangat baik, tidak ada kesalahan tajwid
- 80-89: Baik, ada 1-2 kesalahan minor
- 70-79: Cukup, ada beberapa kesalahan
- 60-69: Kurang, banyak kesalahan tajwid
- 0-59: Sangat kurang, kesalahan tajwid fatal

### Fluency (0-100)

- 90-100: Sangat lancar, tempo konsisten
- 80-89: Lancar, sedikit terputus
- 70-79: Cukup lancar, ada jeda
- 60-69: Kurang lancar, sering terputus
- 0-59: Tidak lancar, banyak terputus

### Makhraj (0-100)

- 90-100: Makhraj sangat tepat
- 80-89: Makhraj baik, sedikit kurang
- 70-79: Makhraj cukup
- 60-69: Makhraj kurang tepat
- 0-59: Makhraj salah

### Keseluruhan (0-100)

- 90-100: Performa sangat baik
- 80-89: Performa baik
- 70-79: Performa cukup
- 60-69: Performa kurang
- 0-59: Performa sangat kurang

## Troubleshooting

### Masalah Umum:

1. **Tidak bisa login juri**: Pastikan username/password benar dan status juri aktif
2. **Form penilaian tidak tersimpan**: Cek validasi skor (harus 0-100)
3. **Laporan tidak muncul**: Pastikan ada data penilaian di database
4. **Error database**: Jalankan ulang `update_database_penilaian.php`

### Support:

Untuk bantuan teknis, hubungi administrator sistem.
