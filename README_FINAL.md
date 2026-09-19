# Sistem Penilaian Final - Lomba Tahfidz

## Deskripsi

Sistem penilaian final memungkinkan admin untuk memilih 6 peserta dengan nilai tertinggi dari babak penyisihan untuk dinilai kembali dalam babak final. Sistem ini menggunakan penilaian yang lebih detail dengan 3 aspek: Tajwid, Fluency, dan Makhraj.

## Fitur Utama

### 1. **Pemilihan Peserta Final**

- Admin dapat memilih 6 peserta terbaik dari penyisihan
- Sistem otomatis menghitung ranking berdasarkan skor terbobot
- Preview ranking sebelum memilih peserta final

### 2. **Assignment Juri Final**

- Assignment 1:1 (1 juri = 1 peserta final)
- Sistem otomatis mengatur assignment berdasarkan peringkat
- Status tracking untuk assignment

### 3. **Penilaian Final**

- 3 aspek penilaian: Tajwid, Fluency, Makhraj (0-100)
- Skor keseluruhan dihitung otomatis (rata-rata 3 aspek)
- Validasi skor dan input yang aman
- Catatan penilaian untuk setiap peserta

### 4. **Laporan dan Export**

- Laporan penilaian final dengan ranking
- Export ke Excel dengan format yang rapi
- Statistik lengkap penilaian final

## Instalasi

### 1. Update Database

Jalankan file `update_database_final.php` untuk menambahkan tabel dan data yang diperlukan:

```
http://localhost/lombatahfidz/update_database_final.php
```

### 2. Struktur Database Baru

#### Tabel `peserta_final`

- `id`: Primary key
- `peserta_id`: ID peserta yang masuk final
- `skor_penyisihan`: Skor yang diperoleh di penyisihan
- `peringkat_penyisihan`: Peringkat di babak penyisihan
- `status`: Status peserta final (Aktif/Nonaktif)
- `created_at`, `updated_at`: Timestamp

#### Tabel `penilaian_final`

- `id`: Primary key
- `peserta_final_id`: ID peserta final
- `juri_id`: ID juri yang menilai
- `skor_tajwid`: Skor tajwid (0-100)
- `skor_fluency`: Skor fluency (0-100)
- `skor_makhraj`: Skor makhraj (0-100)
- `skor_keseluruhan`: Skor keseluruhan (otomatis)
- `catatan`: Catatan penilaian
- `tanggal_penilaian`: Tanggal penilaian
- `created_at`, `updated_at`: Timestamp

#### Tabel `assignment_juri_final`

- `id`: Primary key
- `juri_id`: ID juri
- `peserta_final_id`: ID peserta final
- `status`: Status assignment (Assigned/Completed)
- `created_at`, `updated_at`: Timestamp

## Cara Penggunaan

### 1. **Admin - Pilih Peserta Final**

1. Login sebagai admin
2. Akses menu **Sistem Final > Peserta Final**
3. Klik **"Pilih 6 Peserta Final"**
4. Sistem akan otomatis memilih 6 peserta terbaik
5. Status sistem akan berubah ke "Final Aktif"

### 2. **Admin - Setup Assignment Juri**

1. Akses menu **Sistem Final > Assignment Juri**
2. Klik **"Buat Assignment Juri"**
3. Sistem akan otomatis mengassign juri ke peserta final
4. Assignment 1:1 (1 juri = 1 peserta final)

### 3. **Juri - Penilaian Final**

1. Login sebagai juri
2. Di dashboard akan muncul statistik penilaian final
3. Klik **"Penilaian Final"** atau akses dari assignment
4. Isi 3 aspek penilaian:
   - **Tajwid** (0-100): Ketepatan hukum tajwid
   - **Fluency** (0-100): Kelancaran membaca
   - **Makhraj** (0-100): Ketepatan makhraj huruf
5. Skor keseluruhan akan dihitung otomatis
6. Tambahkan catatan penilaian
7. Klik **"Simpan Penilaian"**

### 4. **Admin - Lihat Laporan**

1. Akses menu **Sistem Final > Laporan Final**
2. Lihat ranking peserta final
3. Export ke Excel jika diperlukan
4. Lihat detail penilaian setiap peserta

## Alur Kerja Sistem

```
1. PENYISIHAN SELESAI
   ↓
2. ADMIN PILIH 6 PESERTA TERBAIK
   ↓
3. ADMIN SETUP ASSIGNMENT JURI
   ↓
4. JURI NILAI PESERTA FINAL
   ↓
5. ADMIN LIHAT LAPORAN FINAL
```

## Keuntungan Sistem Final

### Untuk Admin:

- ✅ **Kontrol Penuh**: Admin mengatur semua aspek sistem final
- ✅ **Otomatis**: Pemilihan peserta dan assignment otomatis
- ✅ **Transparan**: Laporan lengkap dan detail
- ✅ **Fleksibel**: Bisa reset dan ulang jika diperlukan

### Untuk Juri:

- ✅ **Fokus**: 1 juri = 1 peserta final
- ✅ **Detail**: Penilaian 3 aspek yang komprehensif
- ✅ **Mudah**: Interface yang user-friendly
- ✅ **Real-time**: Perhitungan skor otomatis

### Untuk Sistem:

- ✅ **Konsisten**: Menggunakan sistem yang sama dengan penyisihan
- ✅ **Aman**: Validasi dan keamanan data
- ✅ **Scalable**: Bisa dikembangkan untuk lomba yang lebih besar
- ✅ **Audit**: Tracking lengkap semua aktivitas

## Validasi dan Keamanan

- **Validasi Skor**: Semua skor harus dalam rentang 0-100
- **Assignment Validation**: Juri hanya bisa mengakses peserta yang di-assign
- **Unique Constraint**: Mencegah duplikasi penilaian
- **Session Management**: Login/logout yang aman
- **Input Sanitization**: Semua input divalidasi dan disanitasi

## Panduan Penilaian Final

### Tajwid (0-100)

- 90-100: Sangat baik, tidak ada kesalahan tajwid
- 80-89: Baik, ada 1-2 kesalahan minor
- 70-79: Cukup, ada beberapa kesalahan
- 60-69: Kurang, banyak kesalahan tajwid
- 0-59: Tidak memenuhi standar

### Fluency (0-100)

- 90-100: Sangat lancar, tidak ada jeda
- 80-89: Lancar, ada sedikit jeda
- 70-79: Cukup lancar, ada beberapa jeda
- 60-69: Kurang lancar, sering jeda
- 0-59: Tidak lancar, banyak jeda

### Makhraj (0-100)

- 90-100: Sangat tepat, tidak ada kesalahan
- 80-89: Tepat, ada 1-2 kesalahan minor
- 70-79: Cukup tepat, ada beberapa kesalahan
- 60-69: Kurang tepat, banyak kesalahan
- 0-59: Tidak tepat, banyak kesalahan

## File yang Dibuat

1. `update_database_final.php` - Script update database
2. `admin/peserta_final.php` - Kelola peserta final
3. `admin/assignment_juri_final.php` - Assignment juri final
4. `admin/laporan_penilaian_final.php` - Laporan penilaian final
5. `admin/export_penilaian_final.php` - Export ke Excel
6. `juri/penilaian_final.php` - Form penilaian final
7. `README_FINAL.md` - Dokumentasi sistem

## Troubleshooting

### Q: Peserta final tidak muncul di dashboard juri

A: Pastikan:

1. Database sudah diupdate dengan `update_database_final.php`
2. Admin sudah memilih 6 peserta final
3. Assignment juri sudah dibuat
4. Juri sudah di-assign ke peserta final

### Q: Skor keseluruhan tidak terhitung

A: Pastikan:

1. Semua 3 aspek penilaian sudah diisi
2. Skor dalam rentang 0-100
3. Browser mendukung JavaScript

### Q: Tidak bisa export Excel

A: Pastikan:

1. Ada data penilaian final
2. Browser tidak memblokir download
3. File permission sudah benar

## Support

Jika mengalami masalah atau butuh bantuan, silakan hubungi administrator sistem.
