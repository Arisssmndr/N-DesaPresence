# 📘 MANUAL PENGGUNAAN & DOKUMENTASI TEKNIS
## PRESENCE DESA NANGTANG (SADI v2.0)
**Sistem Absensi Desa Integratif & Real-Time Hardware Ingestion**
*Program KKN Universitas 2025 | Desa Nangtang, Kecamatan Cigalontang, Kabupaten Tasikmalaya*

---

## 📌 1. AKUN KREDENSIAL DEFAULTS

| User Level | Username | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin Desa (Sekdes)** | `admin` | `admin123` | Akses penuh CRUD pegawai, shift, libur, import USB, override, Siltap, SPJ PDF |
| **Kepala Desa** | `kades` | `kades123` | Approval SPT kedinasan, approval izin, monitoring dashboard & SPJ |
| **Auditor Inspektorat** | `auditor` | `auditor123` | Akses read-only audit trail, matriks presensi, & laporan SPJ |

---

## 🔌 2. CARA MENJALANKAN ENGINE LISTENER HARDWARE REAL-TIME

Sistem Presence Desa terhubung langsung dengan mesin sidik jari MAGIC / ZKTeco SSR melalui port USB Serial COM di PC Server Kantor Desa.

### Langkah Pengoperasian:
1. Pastikan Driver USB Serial (CH340 / CP2102 / ZKTeco) telah terinstal pada PC Windows server.
2. Tentukan nomor COM Port melalui **Device Manager** (contoh: `COM3` atau `COM4`).
3. Buka PowerShell / Terminal di folder proyek `d:\KKN\website\desa-presence`.
4. Jalankan perintah listener:
   ```bash
   php artisan fingerprint:listen COM3
   ```
5. Mesin akan standby. Setiap kali perangkat desa menempelkan sidik jari, log transaksi akan masuk secara instan ke database tanpa jeda dan tampil di dashboard.

---

## 💾 3. CARA IMPORT LOG ABSENSI FLASHDISK (USB BACKUP)

Jika kabel serial terlepas atau mati listrik, admin dapat mengunduh log dari mesin via USB Flashdisk:
1. Masuk ke menu **Import Log USB** ([`/import-absensi`](http://localhost:8000/import-absensi)).
2. Unggah berkas log bertipe `.dat` atau `.txt` (contoh: `1_attlog.dat`).
3. Klik **MULAI IMPORT LOG PRESENSI**.
4. Sistem akan otomatis memparsi, memvalidasi PIN, dan menolak duplikat data.

---

## 📄 4. CARA CETAK LAPORAN SPJ PRESENSI RESMI (PDF)

1. Masuk ke menu **Buku Matriks Presensi** ([`/matriks`](http://localhost:8000/matriks)).
2. Pilih **Bulan** dan **Tahun** laporan yang ingin dicetak.
3. Klik tombol **Cetak SPJ PDF Resmi** di pojok kanan atas.
4. Laporan PDF berformat A4 Landscape akan langsung ter-generate, lengkap dengan Kop Surat Pemdes Nangtang dan Lembar Tanda Tangan Kepala Desa & Sekretaris Desa.

---

## ⏱️ 5. OPERASIONAL SCHEDULED JOBS OTOMATIS

Sistem secara otomatis menjalankan 3 tugas latar belakang:
- `MarkAbsentJob` (23:59 WIB): Otomatis menandai "Alpa" pegawai yang tidak absen (dikecualikan weekend, libur nasional, SPT, dan izin).
- `UpdateJamPulangJob` (Tiap 5 menit): Memperbarui jam pulang dan durasi kerja dari scan terakhir.
- `DatabaseBackupJob` (22:00 WIB): Otomatis mem-backup database ke folder `storage/backups/`.

---

*Hak Cipta &copy; 2025 Tim KKN Universitas — Pemerintah Desa Nangtang.*
