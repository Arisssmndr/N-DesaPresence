<div align="center">

# 🏛️ N-DESAPRESENCE
### **Sistem Absensi Desa Integratif & Ekosistem Smart Governance (SADI v2.0)**
**Pemerintah Desa Nangtang, Kecamatan Cigalontang, Kabupaten Tasikmalaya**

[![Laravel 12.x](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire 3.x](https://img.shields.io/badge/Livewire-3.x-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black)](https://alpinejs.dev)
[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL 8.0](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Fonnte API](https://img.shields.io/badge/Fonnte-WA_Gateway-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://fonnte.com)
[![ZKTeco Biometric](https://img.shields.io/badge/Hardware-ZKTeco_SSR_Serial-064E3B?style=for-the-badge&logo=microchip&logoColor=F3E5AB)](https://zkteco.com)

<p align="center">
  <b>Solusi Enterprise-Grade Presensi Aparatur Desa Modern:</b><br>
  Integrasi Mesin Fingerprint Hardware Serial, Portal Mandiri Mobile PWA, Otomasi Piket Malam & Lepas Piket, serta Siaran Notifikasi WhatsApp Fonnte Terenkripsi.
</p>

---

</div>

## 💎 Highlight Utama Sistem

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  🏛️ N-DesaPresence v2.0 — Architecture & Core Engine Highlights                       │
├────────────────────────────────────────────────────────────────────────────────────────┤
│  ⚡ Fonnte WA Gateway       : Remote Multi-Device, QR Polling Web-Style, Encrypted Token│
│  🔌 Biometric USB Serial    : Real-Time Hardware Ingestion (ZKTeco SSR & MAGIC Series) │
│  🌙 Smart Night Patrol      : Presensi Piket Malam & Auto-Kompensasi "Lepas Piket"    │
│  📑 Legal SPJ & Inspektorat : Ekspor PDF A4 Landscape Standar Permendagri & Tanda Tangan│
│  📱 Mobile Portal PWA       : Navigasi Bottom Dock Modern, Clock In GPS & TTD Digital   │
│  💰 Kalkulator SILTAP       : Potongan Otomatis Kedisiplinan Keterlambatan & Alpa     │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🌟 Modul & Fitur Unggulan

### 1. 📲 WhatsApp Gateway & Remote Device Manager (Fonnte API)
* **Remote Multi-Device Management**: Kelola perangkat WhatsApp pengirim langsung dari panel admin tanpa perlu membuka dashboard pihak ketiga.
* **Auto-Polling QR Code (*WA Web Style*)**: Tampilan QR Code interaktif dengan interval polling 2.5 detik yang otomatis terhubung dan mendeteksi status tautan perangkat.
* **Dual-Layer Persistent Cache**: Menyimpan status dan daftar perangkat di memori & database lokal sehingga data tidak pernah hilang saat refresh.
* **Real-time Synchronous Broadcast**: Pengiriman pengumuman dinas ke seluruh staf desa atau per divisi dalam hitungan detik.
* **Security Key Vault**: Penyimpanan Master Account Token dan Device Token dengan enkripsi AES-256 (`Crypt::encryptString`).

### 2. 🔌 Biometric Hardware Ingestion (`php artisan fingerprint:listen`)
* **Live USB Serial Listener**: Menangkap log punch absensi dari mesin fingerprint ZKTeco SSR & MAGIC series via port Serial COM (RS232/USB).
* **Multi-Format Parser**: Mendukung parser payload string ZKTeco, format CSV, JSON, maupun direct hexadecimal stream.
* **Auto Sync ke Database**: Pencocokan otomatis PIN mesin ke NIPD pegawai secara *real-time* dengan penandaan keterlambatan instan.

### 3. 🌙 Jadwal Piket Malam & Otomasi Kompensasi "Lepas Piket"
* **Presensi Lintas Hari (Overnight Shifts)**: Mendukung jam piket malam (misal 19:00 – 06:00 WIB) dengan validasi kunci jam buka absen.
* **Dual Digital Signature**: Rekam bukti tanda tangan digital saat mulai bertugas (Masuk) dan saat serah terima tugas (Pulang).
* **Otomatis Hadir Lepas Piket**: Sistem secara otomatis mencatatkan kehadiran **100% HADIR (Lepas Piket)** untuk hari esoknya, sehingga petugas berhak istirahat tanpa mengurangi hak SILTAP.

### 4. 📝 Surat Perintah Tugas (SPT) & Izin Digital
* **Workflow SPT Digital**: Admin menerbitkan SPT dinas luar → staf menerima notifikasi → staf konfirmasi kehadiran luar lokasi.
* **7 Kategori Izin/Sakit**: Cuti tahunan, sakit dengan unggah surat dokter, urusan dinas luar, hingga izin darurat keluarga dengan approval berjenjang.

### 5. 📊 Buku Matriks Presensi & Kalkulator SILTAP
* **Matriks 31 Hari Berwarna**: Visualisasi grid status kehadiran (Hadir, Terlambat, Alpa, Izin, Dinas Luar, Libur Nasional).
* **Formula Pemotongan Penghasilan Tetap**: Perhitungan persentase potongan otomatis berdasarkan keterlambatan menit dan akumulasi alpa bulanan.

### 6. 📄 Ekspor PDF Standar Resmi Pemdes & Inspektorat
* **Format SPJ A4 Landscape**: Tata letak resmi berkas pertanggungjawaban lengkap dengan Kop Surat Pemdes Nangtang, logo daerah beresolusi tinggi, dan lembar legalitas tanda tangan Kepala Desa & Sekretaris Desa.

---

## 🎨 Luxury Design System (Filosofi 60-30-10)

Antarmuka dirancang dengan standar estetika tinggi (*Ultra-Premium Luxury Aesthetic*):

```
  ┌────────────────────────┬────────────────────────┬────────────────────────┐
  │      60% DOMINANT      │     30% STRUCTURAL     │       10% ACCENT       │
  │   Alabaster Warm Base  │   Deep Emerald Green   │      Imperial Gold     │
  │        #FAF6F0         │        #064E3B         │        #C9A84C         │
  └────────────────────────┴────────────────────────┴────────────────────────┘
```
* **Tipografi**: Menggunakan Google Font **`Outfit`** untuk display/heading yang berwibawa dan **`Inter`** / **`JetBrains Mono`** untuk keterbacaan data numerik.
* **Micro-Interactions**: Transisi halus, glassmorphism modal blur, dan indikator status bernafas (*pulsing live badges*).

---

## 🚀 Panduan Instalasi Cepat (Quick Start)

### 1. Kloning Repositori
```bash
git clone https://github.com/Arisssmndr/N-DesaPresence.git n-desapresence
cd n-desapresence
```

### 2. Pasang Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin berkas contoh konfigurasi:
```bash
cp .env.example .env
```
Sesuaikan parameter database Anda:
```env
APP_NAME="N-DesaPresence"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_kknpresencedesa
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Inisialisasi Database & Seeder
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 5. Kompilasi Aset & Jalankan Server
Buka 2 tab terminal terpisah:
```bash
# Terminal 1 (Frontend Vite)
npm run dev

# Terminal 2 (Laravel Server)
php artisan serve
```
Akses aplikasi melalui peramban: **`http://127.0.0.1:8000`**

---

## 🔐 Akun Pengguna Bawaan (Default Seeder)

| Role / Jabatan | Username | Password | Akses & Wewenang |
|---|---|---|---|
| **Sekretaris Desa (Super Admin)** | `admin` | `admin123` | Akses penuh master data, approval, konfigurasi WA & serial |
| **Kepala Desa (Pimpinan)** | `kades` | `kades123` | Monitoring analitik, approval SPT dinas luar, tanda tangan SPJ |
| **Auditor Inspektorat** | `auditor` | `auditor123` | Akses baca laporan, riwayat audit trail, matriks disiplin |
| **Staf / Perangkat Desa** | `daday` *(atau NIK)* | `password` | Portal presensi mandiri, pengajuan izin, jadwal piket |

---

## 🔌 Menjalankan Listener Hardware Serial

Hubungkan kabel USB mesin absensi ZKTeco / MAGIC ke komputer server, lalu jalankan:

```bash
# Format: php artisan fingerprint:listen <PORT_COM> <BAUDRATE>
php artisan fingerprint:listen COM3 9600
```

---

## 🛡️ Standar Keamanan & Integritas Data

* **Enkripsi Kredensial**: Seluruh token WhatsApp Gateway dienkripsi menggunakan standar AES-256-CBC bawaan Laravel Security Suite.
* **Audit Trail Permanen**: Setiap aksi mutasi data (edit absensi, persetujuan izin, perubahan jadwal piket) tercatat di tabel `audit_logs`.
* **Proteksi Akses IP & Geolocation**: Validasi IP address internal kantor desa untuk mencegah manipulasi presensi jarak jauh.

---

## 👥 Tim Pengembang & Kolaborasi

* **Program KKN Universitas LP3I Tasikmalaya 2026**
* **Mitra Kerjasama:** Pemerintah Desa Nangtang, Kec. Cigalontang, Kab. Tasikmalaya
* **Lead Developer / Author:** [Arisssmndr](https://github.com/Arisssmndr)

<div align="center">

---
<sub>© 2026 Pemerintah Desa Nangtang & Tim KKN LP3I Tasikmalaya. Hak Cipta Dilindungi Undang-Undang.</sub>

</div>
