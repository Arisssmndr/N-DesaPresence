# SISTEM ABSENSI DESA INTEGRATIF (SADI)
## Product Requirement Document (PRD) — Versi 2.0.0 Final

**Desa Nangtang | Program KKN Universitas [Nama Universitas]**

---

```
╔══════════════════════════════════════════════════════════════════╗
║          PEMERINTAH DESA NANGTANG — KECAMATAN [XXX]             ║
║                  KABUPATEN / KOTA [XXX]                          ║
║                                                                  ║
║   SISTEM ABSENSI DESA INTEGRATIF (SADI)                         ║
║   Dokumen Persyaratan Produk & Spesifikasi Sistem               ║
║                                                                  ║
║   Versi  : 2.0.0 — Government-Grade Release                     ║
║   Status : Approved for Development                              ║
╚══════════════════════════════════════════════════════════════════╝
```

---

## 📋 INFORMASI DOKUMEN

| Atribut | Detail |
| :--- | :--- |
| **Nama Sistem** | Sistem Absensi Desa Integratif (SADI) |
| **Kode Proyek** | KKN-SADI-NANGTANG-2025 |
| **Versi Dokumen** | 2.0.0 — Government-Grade Release |
| **Status Dokumen** | ✅ Approved for Development |
| **Tanggal Pengesahan** | Agustus 2025 |
| **Penyusun** | Tim KKN — Universitas [Nama Universitas] |
| **Pembimbing Lapangan** | Sekretaris Desa Nangtang |
| **Klien / Pengguna** | Pemerintah Desa Nangtang |
| **Teknologi Utama** | Laravel 11, Livewire 3, Alpine.js, Tailwind CSS, MySQL 8.0 |
| **Perangkat Keras** | Mesin Fingerprint ZKTeco / MAGIC Series (USB Serial COM) |
| **Model Deployment** | Offline-First Local Server (LAN/Wi-Fi Intranet) |
| **Lisensi** | Open Source — Diserahkan kepada Pemerintah Desa |

---

## 📑 DAFTAR ISI

1. Pendahuluan & Latar Belakang
2. Visi, Misi & Tujuan Sistem
3. Ruang Lingkup Proyek
4. Analisis Pemangku Kepentingan
5. Arsitektur Sistem & Komunikasi Hardware
6. Hak Akses & Manajemen Pengguna
7. Spesifikasi Modul & Fitur Lengkap
8. Desain Antarmuka (UI/UX Guidelines)
9. Rancangan Skema Database
10. Implementasi Kode Program Utama
11. Spesifikasi Keamanan Sistem
12. Rencana Pengujian (Testing Plan)
13. Rencana Implementasi & Timeline
14. Pemeliharaan & Dukungan Pasca-Launch
15. Analisis Risiko & Mitigasi
16. Glosarium Istilah
17. Lampiran Teknis

---

## 1. PENDAHULUAN & LATAR BELAKANG

### 1.1 Latar Belakang Kebijakan

Berdasarkan **Permendagri No. 20 Tahun 2018** tentang Pengelolaan Keuangan Desa dan **Permendesa PDTT No. 8 Tahun 2022** tentang Prioritas Penggunaan Dana Desa, setiap Pemerintah Desa wajib menyelenggarakan tata kelola administrasi pegawai yang transparan, terukur, dan akuntabel. Rekap presensi yang valid merupakan **syarat mutlak** pencairan Penghasilan Tetap (Siltap) dan Tunjangan Perangkat Desa dari Dana Desa yang bersumber dari APBN.

Kondisi eksisting di Desa Nangtang masih menggunakan sistem presensi manual berupa buku absen fisik yang rentan terhadap:
- **Manipulasi data** (titip absen, tanda tangan palsu)
- **Ketidakakuratan waktu** (jam masuk/pulang tidak tercatat presisi)
- **Kehilangan data fisik** (buku basah, hilang, rusak)
- **Proses rekapitulasi manual** yang memakan waktu dan rawan human error
- **Tidak adanya audit trail** yang bisa dipertanggungjawabkan kepada inspektorat

### 1.2 Justifikasi Teknologi

Tim KKN Universitas [Nama Universitas] menawarkan solusi **Sistem Absensi Desa Integratif (SADI)** yang memanfaatkan mesin fingerprint ZKTeco/MAGIC yang telah tersedia di Kantor Desa Nangtang. Sistem ini dibangun dengan prinsip:

- **Offline-First Architecture** — Beroperasi 100% tanpa internet publik
- **Open Source Stack** — Tidak ada biaya lisensi perangkat lunak
- **Government-Grade Security** — Standar keamanan data pemerintah
- **Handover-Ready** — Siap diserahterimakan dan dikelola mandiri oleh desa

### 1.3 Referensi Regulasi

| No. | Regulasi | Relevansi |
| :--- | :--- | :--- |
| 1 | UU No. 6 Tahun 2014 tentang Desa | Dasar Hukum Tata Kelola Desa |
| 2 | PP No. 43 Tahun 2014 | Administrasi Perangkat Desa |
| 3 | Permendagri No. 20 Tahun 2018 | Pengelolaan Keuangan Desa |
| 4 | Permendesa PDTT No. 8 Tahun 2022 | Prioritas Dana Desa |
| 5 | UU No. 27 Tahun 2022 tentang PDP | Perlindungan Data Pribadi |
| 6 | Peraturan Desa tentang Siltap | Pedoman Pembayaran Penghasilan Tetap |

---

## 2. VISI, MISI & TUJUAN SISTEM

### 2.1 Visi

> *"Mewujudkan tata kelola presensi Perangkat Desa Nangtang yang digital, transparan, dan akuntabel sebagai fondasi birokrasi desa berkelas nasional."*

### 2.2 Misi

1. Mengotomatisasi seluruh proses pencatatan dan rekapitulasi presensi dari manual ke digital
2. Mengintegrasikan perangkat keras fingerprint ke sistem informasi berbasis web secara real-time
3. Menyediakan laporan siap pakai sesuai format standar inspektorat kabupaten
4. Membangun fondasi sistem informasi desa yang berkelanjutan dan dapat dikembangkan

### 2.3 Tujuan Terukur (SMART Goals)

| # | Tujuan | Indikator Keberhasilan |
| :--- | :--- | :--- |
| T-01 | Otomatisasi pencatatan absensi | 0% input manual, 100% via fingerprint |
| T-02 | Akurasi data presensi | Tingkat duplikasi data = 0% |
| T-03 | Efisiensi rekapitulasi SPJ | Waktu pembuatan laporan < 5 menit |
| T-04 | Real-time monitoring | Pembaruan data di dashboard ≤ 30 detik |
| T-05 | Ketersediaan sistem | Uptime ≥ 99% pada jam kerja |
| T-06 | Pengurangan manipulasi data | Audit trail 100% tercatat & tidak dapat dihapus |

---

## 3. RUANG LINGKUP PROYEK

### 3.1 Dalam Ruang Lingkup (In Scope)

- ✅ Presensi berbasis fingerprint dengan integrasi real-time via serial COM
- ✅ Dashboard monitoring real-time Livewire (tanpa page refresh)
- ✅ Manajemen master data pegawai dan jabatan
- ✅ Konfigurasi jam kerja, shift, dan hari libur nasional
- ✅ Modul Surat Perintah Tugas (SPT) digital
- ✅ Modul pengajuan izin & sakit dengan upload lampiran
- ✅ Engine deduplication & validasi transaksi
- ✅ Buku Matriks Presensi visual (rekap harian 1-31)
- ✅ Cetak laporan SPJ bulanan (PDF & Excel)
- ✅ Audit trail lengkap seluruh aktivitas sistem
- ✅ Backup & restore database otomatis
- ✅ **[BARU]** Dashboard analitik tren kehadiran (grafik bulanan)
- ✅ **[BARU]** Modul Rekapitulasi Siltap (kalkulasi otomatis potongan)
- ✅ **[BARU]** Manajemen hari libur nasional & cuti bersama
- ✅ **[BARU]** Fitur pengumuman internal desa
- ✅ **[BARU]** Sistem notifikasi in-app (WhatsApp Gateway opsional)
- ✅ **[BARU]** Multi-device fingerprint support (lebih dari 1 mesin)

### 3.2 Di Luar Ruang Lingkup (Out of Scope)

- ❌ Integrasi dengan SIPD/SISKEUDES (bisa dikembangkan fase berikutnya)
- ❌ Aplikasi mobile native Android/iOS
- ❌ Koneksi ke internet publik / cloud server
- ❌ Face recognition / RFID card
- ❌ Pengelolaan penggajian lengkap (payroll system)

---

## 4. ANALISIS PEMANGKU KEPENTINGAN

### 4.1 Peta Pemangku Kepentingan

```
                    ┌─────────────────────────────────────┐
                    │        PEMANGKU KEPENTINGAN         │
                    └─────────────────────────────────────┘
                                      │
          ┌───────────────────────────┼───────────────────────────┐
          ▼                           ▼                           ▼
   ┌─────────────┐           ┌─────────────────┐         ┌──────────────┐
   │  PENGGUNA   │           │  PENENTU KEBIJAK│         │  PENGAWAS    │
   │  LANGSUNG   │           │       AN        │         │  EKSTERNAL   │
   └─────────────┘           └─────────────────┘         └──────────────┘
   • Admin Desa              • Kepala Desa                • Inspektorat
   • Perangkat Desa          • BPD                        • Camat
   • Tim KKN                 • Pemkab                     • Auditor BPK
```

### 4.2 Profil Pengguna Detail

| Peran | Profil | Frekuensi | Keahlian Teknis |
| :--- | :--- | :--- | :--- |
| **Admin Desa** (Sekdes/Kaur Umum) | 1-2 orang | Harian | Menengah |
| **Kepala Desa** | 1 orang | Mingguan/bulanan | Rendah-Menengah |
| **Perangkat Desa** | 8-20 orang | Harian (via fingerprint) | Rendah |
| **Auditor/Inspektorat** | 2-5 orang | Tahunan saat audit | Menengah |
| **Tim KKN** | 5-10 mahasiswa | Selama masa KKN | Tinggi |

---

## 5. ARSITEKTUR SISTEM & KOMUNIKASI HARDWARE

### 5.1 Diagram Arsitektur Lengkap

```
╔══════════════════════════════════════════════════════════════════════════╗
║                         INFRASTRUKTUR HARDWARE                          ║
║  ┌──────────────────────────────────────────────────────────────────┐   ║
║  │              MESIN FINGERPRINT ZKTeco / MAGIC SERIES             │   ║
║  └──────────────────────────────────────────────────────────────────┘   ║
║            │ Mini-USB/USB-A                     │ USB Flashdisk          ║
╚════════════│═══════════════════════════════════════════════════════════╝ ║
             ▼                                         ▼
╔════════════════════════╗                 ╔═══════════════════════╗
║  ENGINE INGESTION #1   ║                 ║  ENGINE INGESTION #2  ║
║  PHP Artisan Daemon    ║                 ║  Livewire File        ║
║  fingerprint:listen    ║                 ║  Importer Component   ║
║  (Serial COM Port)     ║                 ║  (.dat / .txt parser) ║
║  [REAL-TIME PRIMARY]   ║                 ║  [BACKUP / RECOVERY]  ║
╚════════════════════════╝                 ╚═══════════════════════╝
             │                                         │
             └───────────────────┬─────────────────────┘
                                 ▼
╔══════════════════════════════════════════════════════════╗
║              LARAVEL 11 APPLICATION LAYER                ║
║   ┌─────────────┐  ┌──────────────┐  ┌───────────────┐  ║
║   │  Validation │→ │ Deduplication│→ │ Business Logic│  ║
║   └─────────────┘  └──────────────┘  └───────────────┘  ║
║   ┌──────────────────────────────────────────────────┐   ║
║   │              MySQL 8.0 Database                  │   ║
║   └──────────────────────────────────────────────────┘   ║
╚══════════════════════════════════════════════════════════╝
                         ▼
╔══════════════════════════════════════════════════════════╗
║           PRESENTATION LAYER (Livewire 3)                ║
║   Dashboard Real-Time | Laporan SPJ | Admin Panel        ║
║   Browser Pengguna — LAN/Wi-Fi Intranet Kantor Desa      ║
╚══════════════════════════════════════════════════════════╝
```

### 5.2 Spesifikasi Server Minimum (PC Server Desa)

| Komponen | Minimum | Rekomendasi |
| :--- | :--- | :--- |
| **CPU** | Intel Core i3 Gen 6 | Intel Core i5 |
| **RAM** | 4 GB DDR4 | 8 GB DDR4 |
| **Storage** | 120 GB SSD | 256 GB SSD |
| **OS** | Windows 10 64-bit | Windows 11 Pro |
| **PHP** | PHP 8.2 | PHP 8.3 |
| **Database** | MySQL 8.0 | MySQL 8.0 / MariaDB 10.6 |
| **Web Server** | Laragon (local dev) | Nginx + PHP-FPM |

### 5.3 Jalur Ingestion Data

#### Jalur A: Real-Time Serial COM (Primer)
- Mesin fingerprint → kabel USB Serial → COM Port Windows
- `php artisan fingerprint:listen {port=COM3}` berjalan 24/7
- **Latency target:** < 3 detik dari tap → tampil di dashboard

#### Jalur B: File Import (Backup/Recovery)
- Admin download file `.dat` / `.txt` dari Flashdisk
- Upload via Livewire `AttendanceImporter` dengan anti-duplikasi

#### [BARU] Jalur C: Manual Admin Override
- Admin masukkan absensi manual untuk kondisi darurat
- Wajib disertai alasan dan terdokumentasi di audit trail
- Data override diberi tag khusus untuk pembedaan saat audit

---

## 6. HAK AKSES & MANAJEMEN PENGGUNA

### 6.1 Matriks Hak Akses Lengkap

| Fitur | Admin Desa | Kepala Desa | Perangkat Desa | Auditor |
| :--- | :---: | :---: | :---: | :---: |
| **Dashboard Real-Time** | ✅ Full | ✅ View | ✅ View Sendiri | ✅ View |
| **Master Pegawai (CRUD)** | ✅ | ❌ | ❌ | 👁️ |
| **Konfigurasi Jam Kerja** | ✅ | ❌ | ❌ | ❌ |
| **Override Absensi Manual** | ✅ | ❌ | ❌ | ❌ |
| **Approve SPT** | ✅ Draft | ✅ Final | ❌ | 👁️ |
| **Pengajuan Izin/Sakit** | ✅ | ✅ | ✅ Sendiri | ❌ |
| **Laporan SPJ** | ✅ Cetak | ✅ Approve | ❌ | 👁️ |
| **Audit Trail Logs** | ✅ | 👁️ | ❌ | ✅ Full |
| **Rekap Siltap** | ✅ | ✅ Approve | ❌ | 👁️ |
| **[BARU] Pengumuman** | ✅ Kelola | ✅ View | ✅ View | ❌ |
| **[BARU] Analitik Tren** | ✅ | ✅ | ❌ | 👁️ |

> **Keterangan:** ✅ Akses Penuh | 👁️ Hanya Lihat | ❌ Tidak Ada Akses

### 6.2 Kebijakan Autentikasi

- **Login:** Username + Password dengan enkripsi bcrypt (cost factor 12)
- **Session:** Batas waktu sesi 8 jam (menyesuaikan jam kerja)
- **Password Policy:** Minimal 8 karakter, kombinasi huruf & angka
- **Login Log:** Setiap percobaan login (berhasil/gagal) dicatat di audit trail

---

## 7. SPESIFIKASI MODUL & FITUR LENGKAP

### 📌 Modul 1: Dashboard Monitoring Real-Time

**Prioritas:** P0 (Must Have) | **Livewire:** `wire:poll.30s`

#### 1.1 Kartu Statistik Harian (Top KPI Cards)

| Kartu | Data | Warna |
| :--- | :--- | :--- |
| 🟢 Hadir Tepat Waktu | Jumlah pegawai hadir ontime | Emerald Green |
| 🟡 Terlambat | Jumlah pegawai terlambat | Amber/Gold |
| 🔴 Alpa | Absen tanpa keterangan | Red |
| 🔵 Dinas Luar | Pegawai sedang SPT aktif | Blue |
| 🟣 Izin/Sakit | Pegawai dengan izin disetujui | Purple |
| ⬜ Belum Masuk | Pegawai yang belum tap | Gray |

#### 1.2 Tabel Absensi Hari Ini (Live Feed)
- Kolom: No, Foto, Nama, Jabatan, Jam Masuk, Jam Pulang, Keterlambatan, Status Badge
- Auto-update tiap 30 detik tanpa reload halaman
- Indikator "LIVE" berkedip hijau di pojok kanan atas

#### 1.3 Widget Tambahan
- **Clock Widget:** Jam digital real-time kantor desa
- **Kalender Mini:** Hari ini + tanggal aktif
- **Audit Trail Widget:** 5 aktivitas terakhir sistem
- **[BARU] Progress Ring:** % kehadiran vs total pegawai hari ini
- **[BARU] Grafik Mini:** Trend kehadiran 7 hari terakhir (sparkline)
- **[BARU] Pengumuman Pinned:** Pengumuman aktif dari admin/kades

#### 1.4 Alert Dashboard
- Banner kuning jika ada pegawai terlambat > 30 menit
- Banner merah jika ada pegawai alpa sudah jam 10:00
- **[BARU]** Pop-up toast saat tap sidik jari baru masuk

---

### 📌 Modul 2: Manajemen Master Pegawai

**Prioritas:** P0 (Must Have)

#### 2.1 Data Identitas Pegawai

| Field | Tipe | Validasi |
| :--- | :--- | :--- |
| NIPD | VARCHAR(30) | Unik, nullable |
| NIK | VARCHAR(16) | Unik, 16 digit, required |
| Nama Lengkap | VARCHAR(100) | Required |
| Tempat / Tanggal Lahir | DATE | Required |
| Jenis Kelamin | ENUM | L/P |
| Jabatan | ENUM | Kades, Sekdes, Kaur, Kasi, Kadus, Staf |
| Kategori | ENUM | perangkat_tetap, staf, bpd, kemasyarakatan |
| No. HP | VARCHAR(15) | Format Indonesia |
| Foto Profil | FILE | JPG/PNG, max 2MB |
| PIN Fingerprint | VARCHAR(20) | Unik, required |
| Status Aktif | BOOLEAN | Default: true |

#### 2.2 Fitur Tambahan
- **[BARU] Riwayat Jabatan:** Catatan perubahan jabatan/periode + nomor SK
- **[BARU] Struktur Organisasi Visual:** Bagan organisasi desa otomatis
- **[BARU] QR Code Profil:** Generate QR code per pegawai
- **[BARU] Kartu ID Digital:** Cetak kartu identitas pegawai

---

### 📌 Modul 3: Konfigurasi Jam Kerja, Shift & Kalender

**Prioritas:** P0 (Must Have)

#### 3.1 Konfigurasi Jam Kerja

| Parameter | Default |
| :--- | :--- |
| Jam Masuk | 08:00 WIB |
| Batas Toleransi Keterlambatan | 15 menit |
| Jam Pulang Minimum | 15:30 WIB |
| Hari Kerja | Senin – Jumat |

#### 3.2 [BARU] Manajemen Hari Libur
- Kalender hari libur nasional (input manual atau import)
- Cuti bersama khusus desa
- Hari kerja pengganti

#### 3.3 [BARU] Multi-Shift Support
- Shift Pagi: 08:00 – 15:30 (Perangkat Desa)
- Shift Malam: 20:00 – 06:00 (Linmas/Satpam Desa)

---

### 📌 Modul 4: Surat Perintah Tugas (SPT) Digital

**Prioritas:** P1 (Should Have)

#### 4.1 Alur SPT
```
[Pegawai] → Ajukan SPT → [Admin] → Draft → [Kepala Desa] 
→ Setujui → [Sistem] → Status Otomatis "Dinas Luar" → [Rekap SPJ]
```

#### 4.2 Data SPT

| Field | Keterangan |
| :--- | :--- |
| Nomor SPT | Auto-generate: SPT/[BULAN]/[TAHUN]/[URUTAN] |
| Tanggal Dinas | Rentang tanggal mulai – selesai |
| Tujuan / Keperluan | Deskripsi kegiatan |
| File Undangan | Upload PDF/foto |
| Status | draft → diajukan → disetujui → selesai |
| Bukti Kegiatan | Upload foto setelah selesai |

#### 4.3 [BARU] QR Code Verifikasi SPT
- QR code pada SPT tercetak yang bisa discan untuk verifikasi keaslian

---

### 📌 Modul 5: Manajemen Izin & Sakit Digital

**Prioritas:** P0 (Must Have)

#### 5.1 Jenis Pengajuan

| Jenis | Maks. Hari/Tahun |
| :--- | :--- |
| Izin Keperluan Pribadi | 6 hari |
| Sakit dengan Surat Dokter | 14 hari |
| Cuti Tahunan | 12 hari |
| **[BARU] Duka Cita** | 3 hari |
| **[BARU] Melahirkan** | 90 hari |

#### 5.2 [BARU] Fitur Kuota Izin
- Tampilan sisa kuota izin per jenis per pegawai
- Peringatan otomatis jika kuota hampir habis

---

### 📌 Modul 6: Engine Absensi — Validasi & Deduplikasi

**Prioritas:** P0 (Must Have)

#### 6.1 Aturan Validasi

| Aturan | Logika |
| :--- | :--- |
| Anti-Duplikasi | `UNIQUE(pin_fingerprint, waktu_scan)` |
| Anti-Scan Cepat | Selang minimal 5 menit antar scan |
| Jam Kerja Range | Hanya proses scan 05:00 – 22:00 |
| Unknown PIN | Catat di tabel `unknown_scans` + alert admin |
| Hari Libur | Scan tercatat tapi tidak dihitung kehadiran |

#### 6.2 Logika Status Otomatis

```
IF scan 05:00–08:00 → status = "Tepat Waktu"
IF scan 08:01–12:00 → status = "Terlambat"
IF scan terakhir AFTER 15:30 → update jam_pulang
IF tidak ada scan → status = "Alpa" (oleh scheduled job 23:59)
IF ada SPT aktif → override ke "Dinas Luar"
IF ada Izin approved → override ke "Izin" / "Sakit"
```

#### 6.3 [BARU] Scheduled Jobs (Laravel Scheduler)

| Job | Jadwal | Fungsi |
| :--- | :--- | :--- |
| `MarkAbsent` | 23:59 harian | Tandai pegawai tanpa scan sebagai "Alpa" |
| `UpdateJamPulang` | Tiap 5 menit | Update jam pulang dari scan terakhir |
| `DatabaseBackup` | 22:00 harian | Backup database ke storage lokal |
| `GenerateMonthlyReport` | 01-tiap-bulan | Pre-generate draft laporan SPJ |

---

### 📌 Modul 7: Laporan & Rekap SPJ

**Prioritas:** P0 (Must Have)

#### 7.1 Buku Matriks Presensi Visual

```
┌──────────────────┬────────────────────────────────────┐
│   Nama Pegawai   │  1  2  3  4  5  6  7 ... 31 │ Rekap │
├──────────────────┼────────────────────────────────────┤
│ Ahmad Sopian     │  🟢 🟢 🟡 🟢 🟢 ⬜ ⬜ ... 🟢 │ 20H/1T/1A│
│ Siti Nurhaliza   │  🟢 🔵 🔵 🟢 🟢 ⬜ ⬜ ... 🟢 │ 18H/2D/2I│
└──────────────────┴────────────────────────────────────┘
Keterangan: 🟢Tepat Waktu | 🟡Terlambat | 🔴Alpa | 🔵Dinas Luar | 🟣Izin/Sakit | ⬜Libur
```

#### 7.2 Laporan yang Tersedia

| Laporan | Format | Keterangan |
| :--- | :--- | :--- |
| Rekap Kehadiran Harian | PDF/Excel | Per hari, seluruh pegawai |
| Buku Matriks Bulanan | PDF/Excel | Rekap visual 1-31 |
| Laporan SPJ Bulanan | PDF | Format standar Inspektorat |
| **[BARU] Rekapitulasi Siltap** | PDF/Excel | Kalkulasi potongan alpa |
| **[BARU] Laporan Keterlambatan** | PDF/Excel | Rekap menit terlambat |
| **[BARU] Laporan Izin & SPT** | PDF/Excel | Rekap dokumen kedinasan |
| **[BARU] Audit Trail Report** | PDF | Log aktivitas admin |
| **[BARU] Statistik Tahunan** | PDF/Excel | Tren kehadiran setahun penuh |

#### 7.3 [BARU] Modul Kalkulasi Siltap

```
Siltap Bruto = [sesuai Perdes tentang Siltap]
Potongan Alpa = Siltap Bruto / 26 hari kerja × jumlah_hari_alpa
Potongan Terlambat = [nominal per kejadian sesuai kebijakan desa]
Siltap Neto = Siltap Bruto − Potongan Alpa − Potongan Terlambat
```

> ⚠️ Angka Siltap dan rumus potongan dikonfigurasi Admin sesuai Peraturan Desa berlaku.

#### 7.4 Template Laporan SPJ
1. Header kop surat Pemerintah Desa Nangtang
2. Judul: "REKAPITULASI PRESENSI PERANGKAT DESA [BULAN] [TAHUN]"
3. Tabel buku matriks lengkap
4. Kolom: Hadir, Terlambat, Izin, Sakit, Alpa, Dinas Luar, % Kehadiran
5. **[BARU]** Kolom Siltap Neto per pegawai
6. Tanda tangan: Kepala Desa & Sekretaris Desa + stempel

---

### 📌 [BARU] Modul 8: Analitik & Business Intelligence

**Prioritas:** P2 (Nice to Have)

- **Grafik Kehadiran Bulanan:** Line chart trend 12 bulan
- **Pie Chart Status:** Proporsi Tepat Waktu vs Terlambat vs Alpa
- **Bar Chart per Pegawai:** Perbandingan kehadiran
- **Heatmap Keterlambatan:** Hari & jam paling sering terlambat
- **Ranking Kedisiplinan:** Top 5 pegawai paling & kurang disiplin
- **Insight Otomatis:** Notifikasi tren negatif/positif kehadiran

---

### 📌 [BARU] Modul 9: Sistem Notifikasi Internal

**Prioritas:** P2 (Nice to Have)

- Toast notification saat tap sidik jari baru masuk
- Bell icon dengan badge di navbar
- Notifikasi pengajuan izin baru menunggu approval
- **[Opsional] WhatsApp Gateway:** Notifikasi alpa ke HP Admin, pengiriman slip Siltap

---

### 📌 [BARU] Modul 10: Pengumuman & Informasi Desa

**Prioritas:** P2 (Nice to Have)

- Admin/Kades membuat pengumuman resmi yang tampil di dashboard
- Kategori: Rapat, Kegiatan, Informasi, Penting
- Pengumuman dapat di-pin dan kadaluarsa otomatis

---

### 📌 [BARU] Modul 11: Backup, Restore & Ketersediaan Data

**Prioritas:** P0 (Must Have)

- Backup otomatis harian ke `storage/backups/` format `.sql.gz`
- Retensi 30 backup terakhir, hapus yang lebih lama
- Restore via antarmuka web dengan konfirmasi ganda
- Export data ke CSV/JSON untuk open standard interoperability

---

## 8. DESAIN ANTARMUKA (UI/UX GUIDELINES)

### 8.1 Sistem Warna (Design Token)

| Token | Hex Code | Penggunaan |
| :--- | :--- | :--- |
| `--color-primary` | `#059669` | Emerald Green — Warna utama Pemdes |
| `--color-primary-dark` | `#047857` | Hover state |
| `--color-accent` | `#D97706` | Gold/Amber — Aksen khas desa |
| `--color-surface` | `#F0FDF4` | Background halaman |
| `--color-sidebar` | `#064E3B` | Sidebar navigasi gelap |
| `--color-danger` | `#DC2626` | Error, alpa |
| `--color-info` | `#2563EB` | Informasi, dinas luar |
| `--color-warning` | `#F59E0B` | Peringatan, terlambat |

### 8.2 Tipografi

| Elemen | Font | Weight | Size |
| :--- | :--- | :--- | :--- |
| Heading H1 | Inter | 700 Bold | 2rem |
| Heading H2 | Inter | 600 SemiBold | 1.5rem |
| Body Text | Inter | 400 Regular | 0.875rem |
| Monospace (data) | JetBrains Mono | 400 | 0.875rem |

### 8.3 Responsivitas

| Breakpoint | Layout |
| :--- | :--- |
| Mobile (< 640px) | Stack layout, hamburger menu |
| Tablet (640–1024px) | 2-column grid, sidebar collapsed |
| Desktop (> 1024px) | Full sidebar, 4-column KPI cards |

### 8.4 Aksesibilitas (WCAG 2.1 AA)
- Contrast ratio minimum 4.5:1 untuk teks normal
- Semua elemen interaktif dapat diakses via keyboard
- Alt text untuk semua gambar

---

## 9. RANCANGAN SKEMA DATABASE

### 9.1 Entity Relationship Diagram (ERD)

```
jabatans ──< pegawais ──< kehadirans
                    ├──< surat_perintah_tugas
                    ├──< izin_sakits
                    ├──< riwayat_jabatans
                    └──< rekap_siltaps

log_absensis ──> pegawais (via pin_fingerprint)
users ──> pegawais
shift_kerjas ──< pegawais
audit_logs >── users
hari_liburs (independent)
konfigurasi_siltaps ──> jabatans
pengumuman >── users
unknown_scans (independent log)
```

### 9.2 DDL Lengkap (15 Tabel)

```sql
-- ============================================================
-- SADI DATABASE SCHEMA v2.0
-- Sistem Absensi Desa Integratif — Desa Nangtang
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabel Jabatan
CREATE TABLE jabatans (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nama_jabatan VARCHAR(100) NOT NULL,
    kode_jabatan VARCHAR(20) UNIQUE NOT NULL,
    level_jabatan TINYINT DEFAULT 1,
    deskripsi TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 2. Tabel Shift Kerja
CREATE TABLE shift_kerjas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nama_shift VARCHAR(50) NOT NULL,
    jam_masuk TIME NOT NULL DEFAULT '08:00:00',
    jam_pulang TIME NOT NULL DEFAULT '15:30:00',
    toleransi_menit INT DEFAULT 15,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- 3. Tabel Master Pegawai
CREATE TABLE pegawais (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pin_fingerprint VARCHAR(20) UNIQUE NOT NULL,
    nipd VARCHAR(30) UNIQUE NULL,
    nik VARCHAR(16) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(50) NULL,
    tanggal_lahir DATE NULL,
    jenis_kelamin ENUM('L', 'P') NULL,
    jabatan_id BIGINT NOT NULL,
    kategori_pegawai ENUM('perangkat_tetap', 'staf', 'bpd', 'kemasyarakatan') DEFAULT 'perangkat_tetap',
    shift_id BIGINT NULL DEFAULT 1,
    no_hp VARCHAR(15) NULL,
    alamat TEXT NULL,
    foto_profil VARCHAR(255) NULL,
    periode_mulai DATE NULL,
    periode_akhir DATE NULL,
    siltap_bruto DECIMAL(15,2) DEFAULT 0,
    status_aktif BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (jabatan_id) REFERENCES jabatans(id) ON DELETE RESTRICT,
    FOREIGN KEY (shift_id) REFERENCES shift_kerjas(id) ON DELETE SET NULL
);

-- 4. Tabel Users (Login Web)
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id BIGINT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kepala_desa', 'perangkat', 'auditor') DEFAULT 'perangkat',
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pegawai_id) REFERENCES pegawais(id) ON DELETE SET NULL
);

-- 5. Tabel Hari Libur
CREATE TABLE hari_liburs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    nama_hari_libur VARCHAR(100) NOT NULL,
    jenis ENUM('nasional', 'cuti_bersama', 'lokal') DEFAULT 'nasional',
    created_at TIMESTAMP NULL,
    UNIQUE KEY unique_libur_tanggal (tanggal)
);

-- 6. Tabel Raw Log Transaksi Fingerprint
CREATE TABLE log_absensis (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pin_fingerprint VARCHAR(20) NOT NULL,
    waktu_scan DATETIME NOT NULL,
    metode_ingest ENUM('serial_realtime', 'import_file', 'manual_admin') DEFAULT 'serial_realtime',
    raw_data TEXT NULL,
    is_processed BOOLEAN DEFAULT false,
    created_at TIMESTAMP NULL,
    CONSTRAINT unique_raw_scan UNIQUE (pin_fingerprint, waktu_scan)
);

-- 7. Tabel Scan PIN Tidak Dikenal
CREATE TABLE unknown_scans (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pin_fingerprint VARCHAR(20) NOT NULL,
    waktu_scan DATETIME NOT NULL,
    keterangan VARCHAR(255) NULL,
    created_at TIMESTAMP NULL
);

-- 8. Tabel Olahan Kehadiran Harian
CREATE TABLE kehadirans (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id BIGINT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME NULL,
    jam_pulang TIME NULL,
    durasi_kerja_menit INT DEFAULT 0,
    terlambat_menit INT DEFAULT 0,
    status ENUM('Tepat Waktu', 'Terlambat', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa', 'Libur') DEFAULT 'Alpa',
    sumber_data ENUM('fingerprint', 'manual_admin', 'import_file') DEFAULT 'fingerprint',
    keterangan TEXT NULL,
    diverifikasi_oleh BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pegawai_id) REFERENCES pegawais(id) ON DELETE CASCADE,
    FOREIGN KEY (diverifikasi_oleh) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT unique_daily_attendance UNIQUE (pegawai_id, tanggal)
);

-- 9. Tabel Surat Perintah Tugas
CREATE TABLE surat_perintah_tugas (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nomor_spt VARCHAR(50) UNIQUE NOT NULL,
    pegawai_id BIGINT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    keperluan TEXT NOT NULL,
    file_undangan VARCHAR(255) NULL,
    file_bukti_kegiatan VARCHAR(255) NULL,
    anggaran DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft', 'diajukan', 'disetujui', 'ditolak', 'selesai') DEFAULT 'draft',
    disetujui_oleh BIGINT NULL,
    tanggal_persetujuan TIMESTAMP NULL,
    catatan_penolakan TEXT NULL,
    created_by BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pegawai_id) REFERENCES pegawais(id) ON DELETE CASCADE,
    FOREIGN KEY (disetujui_oleh) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 10. Tabel Izin & Sakit
CREATE TABLE izin_sakits (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id BIGINT NOT NULL,
    jenis ENUM('izin_pribadi', 'izin_kedinasan', 'sakit_dengan_surat', 'sakit_tanpa_surat', 'cuti_tahunan', 'duka_cita', 'melahirkan') NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    jumlah_hari INT NOT NULL,
    keterangan TEXT NULL,
    file_lampiran VARCHAR(255) NULL,
    status ENUM('menunggu', 'disetujui', 'ditolak') DEFAULT 'menunggu',
    diproses_oleh BIGINT NULL,
    catatan_admin TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pegawai_id) REFERENCES pegawais(id) ON DELETE CASCADE,
    FOREIGN KEY (diproses_oleh) REFERENCES users(id) ON DELETE SET NULL
);

-- 11. Tabel Rekap Siltap Bulanan
CREATE TABLE rekap_siltaps (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id BIGINT NOT NULL,
    bulan TINYINT NOT NULL,
    tahun YEAR NOT NULL,
    total_hari_kerja INT DEFAULT 0,
    total_hadir INT DEFAULT 0,
    total_terlambat INT DEFAULT 0,
    total_alpa INT DEFAULT 0,
    total_izin INT DEFAULT 0,
    total_dinas_luar INT DEFAULT 0,
    total_menit_terlambat INT DEFAULT 0,
    siltap_bruto DECIMAL(15,2) DEFAULT 0,
    potongan_alpa DECIMAL(15,2) DEFAULT 0,
    potongan_terlambat DECIMAL(15,2) DEFAULT 0,
    siltap_neto DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft', 'final', 'disetujui') DEFAULT 'draft',
    disetujui_oleh BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pegawai_id) REFERENCES pegawais(id) ON DELETE CASCADE,
    CONSTRAINT unique_rekap_siltap UNIQUE (pegawai_id, bulan, tahun)
);

-- 12. Tabel Konfigurasi Siltap
CREATE TABLE konfigurasi_siltaps (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    jabatan_id BIGINT NOT NULL,
    nominal_siltap DECIMAL(15,2) NOT NULL,
    nominal_tunjangan DECIMAL(15,2) DEFAULT 0,
    nilai_potongan_alpa DECIMAL(15,2) DEFAULT 0,
    nilai_potongan_terlambat DECIMAL(15,2) DEFAULT 0,
    berlaku_mulai DATE NOT NULL,
    berlaku_selesai DATE NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (jabatan_id) REFERENCES jabatans(id) ON DELETE CASCADE
);

-- 13. Tabel Audit Log (Tidak dapat dihapus via UI)
CREATE TABLE audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NULL,
    user_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NULL,
    aktivitas VARCHAR(255) NOT NULL,
    modul VARCHAR(100) NULL,
    data_sebelum JSON NULL,
    data_sesudah JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 14. Tabel Pengumuman
CREATE TABLE pengumuman (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    kategori ENUM('rapat', 'kegiatan', 'informasi', 'penting') DEFAULT 'informasi',
    is_pinned BOOLEAN DEFAULT false,
    berlaku_hingga DATE NULL,
    dibuat_oleh BIGINT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE RESTRICT
);

-- 15. Tabel Riwayat Jabatan
CREATE TABLE riwayat_jabatans (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id BIGINT NOT NULL,
    jabatan_id BIGINT NOT NULL,
    mulai_menjabat DATE NOT NULL,
    selesai_menjabat DATE NULL,
    sk_nomor VARCHAR(100) NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (pegawai_id) REFERENCES pegawais(id) ON DELETE CASCADE,
    FOREIGN KEY (jabatan_id) REFERENCES jabatans(id) ON DELETE RESTRICT
);

SET FOREIGN_KEY_CHECKS = 1;
```

---

## 10. IMPLEMENTASI KODE PROGRAM UTAMA

### 10.1 Struktur Direktori Proyek

```
desa-presence/
├── app/
│   ├── Console/Commands/
│   │   └── SerialFingerprintListener.php
│   ├── Http/Middleware/
│   │   └── CheckRole.php
│   ├── Livewire/
│   │   ├── Dashboard.php
│   │   ├── AttendanceImporter.php
│   │   ├── PegawaiManager.php
│   │   ├── IzinManager.php
│   │   ├── SptManager.php
│   │   └── LaporanSPJ.php
│   ├── Models/
│   │   ├── Pegawai.php, LogAbsensi.php, Kehadiran.php
│   │   ├── SuratPerintahTugas.php, IzinSakit.php
│   │   ├── RekapSiltap.php, AuditLog.php
│   │   ├── User.php, Pengumuman.php
│   │   └── UnknownScan.php
│   ├── Services/
│   │   ├── FingerprintIngestionService.php
│   │   ├── AttendanceCalculatorService.php
│   │   └── SiltapCalculatorService.php
│   └── Jobs/
│       ├── ProcessFingerprintLog.php
│       ├── MarkAbsentJob.php
│       └── GenerateMonthlyReportJob.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── JabatanSeeder.php
│       ├── ShiftKerjaSeeder.php
│       └── DefaultUserSeeder.php
├── resources/views/
│   ├── layouts/app.blade.php
│   └── livewire/
│       ├── dashboard.blade.php
│       └── ...
└── docs/
    └── PRD_SADI_v2.0_Desa_Nangtang.md
```

### 10.2 Serial Fingerprint Listener Command

```php
<?php
// app/Console/Commands/SerialFingerprintListener.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FingerprintIngestionService;
use Illuminate\Support\Facades\Log;

class SerialFingerprintListener extends Command
{
    protected $signature = 'fingerprint:listen 
                            {port=COM3 : Nama COM Port (misal: COM3, COM4)} 
                            {--baud=9600 : Baud rate komunikasi serial}';
    protected $description = 'Mendengarkan data real-time dari mesin fingerprint via Serial COM Port';

    public function __construct(private FingerprintIngestionService $ingestionService) 
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $port = $this->argument('port');
        $this->info("╔══════════════════════════════════════════╗");
        $this->info("║     SADI — Serial Fingerprint Listener   ║");
        $this->info("║         Desa Nangtang — KKN 2025         ║");
        $this->info("╚══════════════════════════════════════════╝");
        $this->info("Menghubungkan ke port: {$port}...");

        $fp = @fopen("\\\\.\\{$port}", "r+");

        if (!$fp) {
            $this->error("❌ Gagal membuka port {$port}.");
            $this->line("   Pastikan driver terpasang dan port tidak dipakai aplikasi lain.");
            Log::error("SADI Listener: Gagal membuka port {$port}");
            return Command::FAILURE;
        }

        $this->info("✅ Terhubung! Standby menerima data sidik jari...");
        $buffer = '';
        
        while (true) {
            $chunk = fread($fp, 256);
            if ($chunk !== false && $chunk !== '') {
                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);
                    if (!empty($line)) {
                        $result = $this->ingestionService->ingest($line, 'serial_realtime');
                        $this->renderResult($result, $line);
                    }
                }
            }
            usleep(100000); // 0.1 detik
        }

        fclose($fp);
        return Command::SUCCESS;
    }

    private function renderResult(array $result, string $raw): void
    {
        $time = now()->format('H:i:s');
        match($result['status']) {
            'created'     => $this->info("[{$time}] ✅ [{$result['nama']}] {$result['jenis']} — {$result['status_kehadiran']}"),
            'duplicate'   => $this->warn("[{$time}] ⚠️  Duplikat scan (PIN: {$result['pin']})"),
            'unknown_pin' => $this->warn("[{$time}] ❓ PIN tidak terdaftar: {$result['pin']}"),
            'invalid'     => $this->error("[{$time}] ❌ Format tidak valid: {$raw}"),
            default       => $this->line("[{$time}] ℹ️  {$result['message']}")
        };
    }
}
```

### 10.3 Fingerprint Ingestion Service

```php
<?php
// app/Services/FingerprintIngestionService.php
namespace App\Services;

use App\Models\{LogAbsensi, Pegawai, Kehadiran, UnknownScan, SuratPerintahTugas, AuditLog};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FingerprintIngestionService
{
    public function ingest(string $rawText, string $metode = 'serial_realtime'): array
    {
        $parsed = $this->parseRawData($rawText);
        if (!$parsed) return ['status' => 'invalid'];

        ['pin' => $pin, 'waktu' => $waktu] = $parsed;

        return DB::transaction(function () use ($pin, $waktu, $rawText, $metode) {
            // 1. Anti-duplikasi di raw log
            $log = LogAbsensi::firstOrCreate(
                ['pin_fingerprint' => $pin, 'waktu_scan' => $waktu],
                ['metode_ingest' => $metode, 'raw_data' => $rawText]
            );
            if (!$log->wasRecentlyCreated) return ['status' => 'duplicate', 'pin' => $pin];

            // 2. Cek PIN terdaftar
            $pegawai = Pegawai::with(['jabatan', 'shiftKerja'])
                               ->where('pin_fingerprint', $pin)
                               ->where('status_aktif', true)->first();
            if (!$pegawai) {
                UnknownScan::create(['pin_fingerprint' => $pin, 'waktu_scan' => $waktu]);
                return ['status' => 'unknown_pin', 'pin' => $pin];
            }

            // 3. Proses kehadiran harian
            $tanggal = $waktu->toDateString();
            $kehadiran = Kehadiran::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'tanggal' => $tanggal],
                $this->buildKehadiranData($pegawai, $waktu)
            );

            $jenis = 'masuk';
            if (!$kehadiran->wasRecentlyCreated) {
                $kehadiran->update([
                    'jam_pulang' => $waktu->format('H:i:s'),
                    'durasi_kerja_menit' => Carbon::parse($kehadiran->jam_masuk)->diffInMinutes($waktu),
                ]);
                $jenis = 'pulang';
            }

            $log->update(['is_processed' => true]);

            AuditLog::create([
                'user_name' => $pegawai->nama_lengkap,
                'role'      => $pegawai->jabatan->nama_jabatan ?? '-',
                'aktivitas' => "Scan absen {$jenis} pukul {$waktu->format('H:i:s')}",
                'modul'     => 'Absensi',
            ]);

            return [
                'status'           => 'created',
                'jenis'            => $jenis,
                'nama'             => $pegawai->nama_lengkap,
                'status_kehadiran' => $kehadiran->fresh()->status,
            ];
        });
    }

    private function parseRawData(string $rawText): ?array
    {
        // Format 1: "PIN=001 TIME=2025-08-09 08:05:23"
        if (preg_match('/PIN=(\d+)\s+TIME=(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $rawText, $m)) {
            return ['pin' => $m[1], 'waktu' => Carbon::parse($m[2])];
        }
        // Format 2 ZKTeco Tab-delimited: "PIN\tDEVICE\tYYYYMMDDHHMMSS\t..."
        $parts = explode("\t", trim($rawText));
        if (count($parts) >= 3 && strlen($parts[2]) === 14 && is_numeric($parts[2])) {
            return ['pin' => $parts[0], 'waktu' => Carbon::createFromFormat('YmdHis', $parts[2])];
        }
        return null;
    }

    private function buildKehadiranData(Pegawai $pegawai, Carbon $waktu): array
    {
        $shift = $pegawai->shiftKerja;
        $jamMasukStandar = $shift?->jam_masuk ?? '08:00:00';
        $toleransi = $shift?->toleransi_menit ?? 15;
        $jamScan = $waktu->format('H:i:s');

        // Override jika ada SPT aktif
        $adaSPT = SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $waktu->toDateString())
            ->whereDate('tanggal_selesai', '>=', $waktu->toDateString())->exists();

        if ($adaSPT) return ['jam_masuk' => $jamScan, 'status' => 'Dinas Luar', 'sumber_data' => 'fingerprint'];

        $batasTerlambat = Carbon::parse($jamMasukStandar)->addMinutes($toleransi)->format('H:i:s');
        $terlambatMenit = $jamScan > $batasTerlambat
            ? Carbon::parse($jamMasukStandar)->diffInMinutes(Carbon::parse($jamScan)) : 0;

        return [
            'jam_masuk' => $jamScan,
            'status' => $terlambatMenit > 0 ? 'Terlambat' : 'Tepat Waktu',
            'terlambat_menit' => $terlambatMenit,
            'sumber_data' => 'fingerprint',
        ];
    }
}
```

### 10.4 Dashboard Livewire Component

```php
<?php
// app/Livewire/Dashboard.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\{Kehadiran, AuditLog, Pengumuman, Pegawai};
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $today = Carbon::today()->toDateString();
        $totalPegawai = Pegawai::where('status_aktif', true)->count();
        $kehadiranHariIni = Kehadiran::where('tanggal', $today)->get();

        return view('livewire.dashboard', [
            'statistik' => [
                'totalPegawai' => $totalPegawai,
                'hadir'        => $kehadiranHariIni->whereIn('status', ['Tepat Waktu', 'Terlambat'])->count(),
                'tepat'        => $kehadiranHariIni->where('status', 'Tepat Waktu')->count(),
                'terlambat'    => $kehadiranHariIni->where('status', 'Terlambat')->count(),
                'izinSakit'    => $kehadiranHariIni->whereIn('status', ['Izin', 'Sakit'])->count(),
                'dinasLuar'    => $kehadiranHariIni->where('status', 'Dinas Luar')->count(),
                'alpa'         => $kehadiranHariIni->where('status', 'Alpa')->count(),
                'belumMasuk'   => $totalPegawai - $kehadiranHariIni->count(),
                'persenHadir'  => $totalPegawai > 0 
                    ? round(($kehadiranHariIni->whereIn('status', ['Tepat Waktu','Terlambat'])->count() / $totalPegawai) * 100) 
                    : 0,
            ],
            'listAbsen'  => Kehadiran::with('pegawai.jabatan')
                            ->where('tanggal', $today)->latest('updated_at')->take(15)->get(),
            'auditLogs'  => AuditLog::latest()->take(5)->get(),
            'pengumuman' => Pengumuman::where('is_pinned', true)
                            ->orWhereDate('berlaku_hingga', '>=', now())
                            ->orderByDesc('is_pinned')->take(3)->get(),
            'trendData'  => Kehadiran::selectRaw(
                                "tanggal, COUNT(*) as total,
                                SUM(CASE WHEN status IN ('Tepat Waktu','Terlambat') THEN 1 ELSE 0 END) as hadir")
                            ->where('tanggal', '>=', Carbon::now()->subDays(7)->toDateString())
                            ->groupBy('tanggal')->orderBy('tanggal')->get(),
        ])->layout('layouts.app', ['title' => 'Dashboard — SADI Desa Nangtang']);
    }
}
```

### 10.5 Konfigurasi Laravel Scheduler

```php
// app/Console/Kernel.php (atau routes/console.php di Laravel 11)
use Illuminate\Support\Facades\Schedule;

Schedule::command('attendance:mark-absent')->dailyAt('23:59');
Schedule::command('attendance:update-pulang')->everyFiveMinutes();
Schedule::command('backup:database')->dailyAt('22:00');
Schedule::command('report:generate-monthly')->monthlyOn(1, '01:00');
Schedule::command('auditlog:archive')->weekly()->sundays()->at('02:00');
```

---

## 11. SPESIFIKASI KEAMANAN SISTEM

### 11.1 Keamanan Aplikasi

| Layer | Implementasi |
| :--- | :--- |
| **Autentikasi** | Laravel Auth + bcrypt (cost 12) |
| **Otorisasi** | Middleware `CheckRole` + Gate/Policy Laravel |
| **CSRF Protection** | Laravel CSRF token pada semua form |
| **SQL Injection** | Eloquent ORM + Prepared Statements |
| **XSS Prevention** | Blade auto-escaping |
| **File Upload** | Validasi MIME, ekstensi whitelist, rename random |
| **Rate Limiting** | Throttle login 5x/menit |
| **Session** | HttpOnly, SameSite Strict, expire 8 jam |

### 11.2 Keamanan Data (Sesuai UU PDP No. 27/2022)

- **Enkripsi NIK:** NIK pegawai dienkripsi AES-256 di database
- **Audit Trail Permanen:** Tabel `audit_logs` tidak memiliki `DELETE` privilege dari aplikasi
- **Data Minimal:** Hanya kumpulkan data yang diperlukan
- **Backup Terenkripsi:** File backup dienkripsi sebelum disimpan

### 11.3 Keamanan Jaringan
- Sistem hanya dapat diakses dari jaringan LAN kantor desa
- Tidak ada port yang terbuka ke internet publik
- Firewall Windows: blokir akses dari luar subnet lokal

---

## 12. RENCANA PENGUJIAN (TESTING PLAN)

### 12.1 Matriks Pengujian

| ID | Jenis | Skenario | Kriteria Lulus |
| :--- | :--- | :--- | :--- |
| T-01 | Hardware | Driver COM Port terdeteksi | Status OK di Device Manager |
| T-02 | Hardware | Listener terhubung ke mesin | Pesan "Berhasil terhubung" muncul |
| T-03 | Ingestion | Tap jari → data masuk ke DB | Record terbentuk < 3 detik |
| T-04 | Deduplikasi | Tap 2x dalam 10 detik | Hanya 1 record di `kehadirans` |
| T-05 | Livewire | Dashboard update tanpa refresh | Baris baru tampil ≤ 30 detik |
| T-06 | SPT | Approve SPT → status "Dinas Luar" | Status kehadiran berubah otomatis |
| T-07 | Izin | Approve Izin → status update | Status berubah sesuai jenis |
| T-08 | Laporan | Generate PDF SPJ | PDF terunduh, format benar |
| T-09 | Import | Upload file .dat/.txt | Data masuk tanpa duplikat |
| T-10 | Role | Admin akses halaman auditor | Redirect 403 |
| T-11 | Scheduler | MarkAbsent job 23:59 | Pegawai tanpa scan = "Alpa" |
| T-12 | Backup | Backup otomatis 22:00 | File .sql.gz terbentuk |
| T-13 | Siltap | Kalkulasi potongan | Nominal sesuai formula |
| T-14 | Keamanan | Login salah 6x | Akun terkunci, log tercatat |
| T-15 | Responsif | Akses dari smartphone | Layout responsive berfungsi |

### 12.2 UAT (User Acceptance Testing)

Sesi UAT bersama:
- **Admin Desa:** 1 sesi, 2 jam — tes seluruh fitur admin
- **Kepala Desa:** 1 sesi, 30 menit — review dashboard & laporan
- **2-3 Perangkat Desa:** Uji tap fingerprint + akses profil

---

## 13. RENCANA IMPLEMENTASI & TIMELINE

### 13.1 Fase Pengembangan (8 Minggu)

```
MINGGU 1-2: FONDASI & INFRASTRUKTUR
├── Setup Laragon (PHP 8.3, MySQL 8.0)
├── Install Laravel 11 + Livewire 3 + Tailwind CSS
├── Database migration & seeder data awal
└── Auth system + role-based middleware

MINGGU 3-4: MODUL INTI
├── Master Pegawai (CRUD lengkap + foto)
├── Konfigurasi Jam Kerja & Shift
├── Hari Libur Nasional
├── Serial COM Listener & Ingestion Service
└── Deduplication Engine

MINGGU 5-6: DASHBOARD & FITUR LANJUTAN
├── Dashboard Livewire Real-Time (wire:poll)
├── Modul SPT Digital + alur approval
├── Modul Izin & Sakit + kuota
├── Scheduled Jobs (MarkAbsent, Backup)
└── File Import (Backup Mode)

MINGGU 7: LAPORAN & ANALITIK
├── Buku Matriks Presensi (PDF via DomPDF)
├── Laporan SPJ Bulanan
├── Kalkulasi Rekap Siltap
├── Export Excel (Maatwebsite Excel)
└── Dashboard Analitik & Grafik (Chart.js)

MINGGU 8: PENGUJIAN, POLISH & HANDOVER
├── UAT bersama Admin & Kepala Desa
├── Bug fixing & performance tuning
├── Manual Book Admin (PDF min. 30 hal)
├── Training Admin Desa (2 sesi)
└── Serahterima Sistem (Berita Acara)
```

### 13.2 Deliverables

| Deliverable | Format |
| :--- | :--- |
| Source Code Lengkap | Git Repo (ZIP + USB) |
| Database Schema & Seeder | SQL File |
| Manual Pengguna Admin | PDF, A4, min. 30 halaman |
| Manual Kepala Desa | PDF, A4, max 10 halaman |
| Buku Panduan Teknis IT | PDF, A4, min. 20 halaman |
| Video Tutorial | MP4, max 15 menit |
| Berita Acara Serahterima | Dokumen resmi TTD Ketua KKN + Kades |

---

## 14. PEMELIHARAAN & DUKUNGAN PASCA-LAUNCH

### 14.1 Masa Garansi KKN (3 Bulan)

- Merespons laporan bug dalam 2×24 jam
- Hotfix untuk bug kritis dalam 1×24 jam
- Pendampingan via video call jika dibutuhkan

### 14.2 Checklist Pemeliharaan Mandiri

| Frekuensi | Kegiatan |
| :--- | :--- |
| Harian | Cek dashboard running normal |
| Mingguan | Cek folder backup terisi |
| Bulanan | Update data hari libur, generate rekap Siltap |
| Tahunan | Update konfigurasi Siltap sesuai Perdes baru |

---

## 15. ANALISIS RISIKO & MITIGASI

| ID | Risiko | Kemungkinan | Dampak | Mitigasi |
| :--- | :--- | :---: | :---: | :--- |
| R-01 | Kabel serial terlepas | Tinggi | Tinggi | Jalur backup import file .dat |
| R-02 | PC Server mati/restart | Menengah | Tinggi | Startup otomatis listener + scheduler |
| R-03 | Driver tidak terinstal | Rendah | Tinggi | Bundle installer driver di USB |
| R-04 | Database korup | Rendah | Kritis | Backup otomatis harian + USB berkala |
| R-05 | Admin desa berganti | Menengah | Menengah | Dokumentasi lengkap + multi-admin |
| R-06 | Mesin fingerprint rusak | Rendah | Tinggi | Mode import manual + override admin |
| R-07 | PIN tidak terdaftar | Tinggi | Rendah | `unknown_scans` + notifikasi admin |
| R-08 | Listrik mati | Menengah | Tinggi | Rekomendasi UPS minimal 30 menit |
| R-09 | Akses tidak berwenang | Rendah | Tinggi | Role-based + firewall LAN only |

---

## 16. GLOSARIUM ISTILAH

| Istilah | Definisi |
| :--- | :--- |
| **SADI** | Sistem Absensi Desa Integratif |
| **Siltap** | Penghasilan Tetap Perangkat Desa dari Dana Desa |
| **SPJ** | Surat Pertanggungjawaban — dokumen administrasi keuangan |
| **SPT** | Surat Perintah Tugas — surat penugasan dinas luar |
| **NIPD** | Nomor Induk Perangkat Desa |
| **PIN Fingerprint** | Nomor ID unik yang terdaftar di mesin fingerprint |
| **Alpa** | Status ketidakhadiran tanpa keterangan resmi |
| **COM Port** | Communication Port — port serial Windows untuk data serial |
| **Livewire** | Framework PHP untuk UI interaktif tanpa JavaScript manual |
| **wire:poll** | Direktif Livewire untuk auto-refresh komponen setiap interval |
| **Ingestion** | Proses penerimaan dan pengolahan data mentah ke sistem |
| **Deduplication** | Pencegahan data ganda di database |
| **Audit Trail** | Catatan kronologis aktivitas sistem yang tidak dapat dihapus |

---

## 17. LAMPIRAN TEKNIS

### Lampiran A: Cara Menjalankan Sistem

```bash
# 1. Instalasi dependencies
composer install && npm install && npm run build

# 2. Setup environment
cp .env.example .env && php artisan key:generate

# 3. Konfigurasi .env
DB_DATABASE=sadi_nangtang
DB_USERNAME=root
DB_PASSWORD=

# 4. Migration & seeder
php artisan migrate --seed

# 5. Jalankan web server (akses dari seluruh LAN)
php artisan serve --host=0.0.0.0 --port=8000

# 6. Jalankan scheduler
php artisan schedule:work

# 7. Jalankan listener fingerprint (terminal terpisah)
php artisan fingerprint:listen COM3

# 8. Install sebagai Windows Service (NSSM)
nssm install SADI-Listener php "C:\desa-presence\artisan" "fingerprint:listen COM3"
nssm start SADI-Listener
```

### Lampiran B: Format Data Mesin Fingerprint

```
# ZKTeco SSR (Tab-Delimited)
001    1    20250809080523    1    0    0    0

# MAGIC Series (Key=Value)
PIN=001 TIME=2025-08-09 08:05:23
```

### Lampiran C: Checklist Go-Live

```
[ ] PC Server & driver sudah terkonfigurasi
[ ] Database termigrasi & terseeder data awal
[ ] Akun Admin Desa & Kepala Desa sudah dibuat
[ ] Konfigurasi jam kerja & shift sudah diisi
[ ] Data hari libur nasional sudah diinput
[ ] Master pegawai lengkap beserta PIN fingerprint
[ ] Konfigurasi Siltap per jabatan sesuai Perdes
[ ] Listener terhubung ke mesin (test tap berhasil)
[ ] Dashboard Livewire update otomatis setelah tap
[ ] Backup otomatis berjalan (cek storage/backups/)
[ ] Training Admin Desa minimal 2 sesi selesai
[ ] Manual book cetak & digital sudah diserahkan
[ ] Berita Acara Serahterima ditandatangani
```

---

*Versi 2.0.0 | Approved for Development*  
*Dokumen ini merupakan dokumen hidup yang dapat diperbarui sesuai kebutuhan*

**"Membangun Desa Nangtang yang Transparan, Digital, dan Berdaya"**

*Disusun oleh Tim KKN Universitas [Nama Universitas] — Agustus 2025*
