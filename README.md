# 🏛️ PRESENCE DESA — Sistem Absensi Desa Integratif (SADI v2.0)
### Desa Nangtang, Kecamatan Cigalontang, Kabupaten Tasikmalaya
**Pengembangan Web Standar Nasional — Program KKN Universitas 2025**

---

![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![Livewire Version](https://img.shields.io/badge/Livewire-4.x-4E5BA6?style=for-the-badge&logo=livewire)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php)

---

## 🌟 Fitur Utama Sistem

1. **Real-Time Hardware Serial Listener (`php artisan fingerprint:listen`):** Ingestion data transaksi sidik jari langsung via USB Serial Mini-USB (ZKTeco SSR & MAGIC Series) di Windows PC Server.
2. **Real-Time Livewire Dashboard (10s Polling):** Feed absensi live, clock digital real-time, KPI card indikator kehadiran, dan widget pengumuman.
3. **Master Pegawai & PIN Mapping:** Pengelolaan identitas Perangkat Desa (NIK 16 digit, NIPD, Jabatan, Shift Kerja, & Upload Foto).
4. **Surat Perintah Tugas (SPT) Digital:** Workflow pembuatan SPT resmi dengan approval Kepala Desa & otomatisasi status "Dinas Luar".
5. **Izin & Sakit Digital:** 7 jenis kategori izin perangkat desa dengan unggah surat dokter dan approval admin.
6. **Buku Matriks Presensi Bulanan (Tanggal 1–31):** Visual matriks indikator warna (Hadir, Terlambat, Alpa, Izin, Dinas Luar, Libur).
7. **Kalkulator Siltap & Potongan Kedisiplinan:** Otomatisasi perhitungan Penghasilan Tetap Neto berdasarkan potongan terlambat dan alpa.
8. **Cetak SPJ PDF Resmi Pemdes & Inspektorat:** Ekspor PDF laporan rekapitulasi SPJ A4 Landscape via DomPDF lengkap dengan Kop Surat Pemdes Nangtang dan Lembar Tanda Tangan Kades/Sekdes.
9. **Scheduled Background Jobs:** Penandaan alpa otomatis (23:59), update jam pulang (tiap 5 menit), dan backup database `.sql` otomatis (22:00).
10. **Permanent Audit Trail Log:** Pencatatan seluruh aktivitas sistem untuk akuntabilitas inspektorat.

---

## 🎨 Palet Warna UI (Aturan 60-30-10)

- **60% Cream (`#F5F0E8`):** Background utama, workspace area, card base.
- **30% Dark Emerald Green (`#064E3B`):** Sidebar navigasi, header panel, primary button.
- **10% Gold Accent (`#C9A84C`):** Circular ring monogram logo "N", active nav highlight, status borders.

---

## ⚡ Panduan Instalasi Cepat

### 1. Clone & Core Setup
```bash
git clone <repository_url> desa-presence
cd desa-presence
composer install
```

### 2. Konfigurasi Environment & Database
Edit berkas `.env` dan sesuaikan koneksi database MySQL:
```env
APP_NAME="Presence Desa"
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kknpresencedesa
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi Database & Seeding Data Awal
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 4. Menjalankan Server Aplikasi
```bash
php artisan serve
```
Buka browser di `http://localhost:8000`.

---

## 🔐 Kredensial Akses Bawaan (Seeder)

- **Admin Desa (Sekdes):** `admin` / `admin123`
- **Kepala Desa:** `kades` / `kades123`
- **Auditor Inspektorat:** `auditor` / `auditor123`

---

## 🔌 Jalankan Listener Serial Hardware

```bash
php artisan fingerprint:listen COM3
```

---

*Dikembangkan oleh Tim KKN Universitas — Untuk Pemerintah Desa Nangtang 2025.*
