<?php

namespace App\Services;

use App\Models\LogAbsensi;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\UnknownScan;
use App\Models\SuratPerintahTugas;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FingerprintIngestionService
{
    /**
     * Ingest raw text data from fingerprint device or file importer
     */
    public function ingest(string $rawText, string $metode = 'serial_realtime'): array
    {
        $parsed = $this->parseRawData($rawText);
        if (!$parsed) {
            return [
                'status' => 'invalid',
                'message' => 'Format data mentah tidak dapat dikenali'
            ];
        }

        ['pin' => $pin, 'waktu' => $waktu] = $parsed;

        return DB::transaction(function () use ($pin, $waktu, $rawText, $metode) {
            // 1. Check and save to raw log table
            $log = LogAbsensi::firstOrCreate(
                ['pin_fingerprint' => $pin, 'waktu_scan' => $waktu],
                ['metode_ingest' => $metode, 'raw_data' => $rawText]
            );

            if (!$log->wasRecentlyCreated) {
                return [
                    'status' => 'duplicate',
                    'pin' => $pin,
                    'message' => 'Data scan terdeteksi duplikat pada detik yang sama'
                ];
            }

            // 2. Find associated active Pegawai by PIN
            $pegawai = Pegawai::with(['jabatan', 'shiftKerja'])
                ->where('pin_fingerprint', $pin)
                ->where('status_aktif', true)
                ->first();

            if (!$pegawai) {
                UnknownScan::create([
                    'pin_fingerprint' => $pin,
                    'waktu_scan' => $waktu,
                    'keterangan' => 'Scan diterima dari PIN yang belum terdaftar di master pegawai'
                ]);

                return [
                    'status' => 'unknown_pin',
                    'pin' => $pin,
                    'message' => "PIN {$pin} belum terdaftar di master pegawai"
                ];
            }

            // 3. Process Daily Attendance
            $tanggal = $waktu->toDateString();
            $jamScan = $waktu->format('H:i:s');

            $kehadiran = Kehadiran::firstOrCreate(
                ['pegawai_id' => $pegawai->id, 'tanggal' => $tanggal],
                $this->buildKehadiranData($pegawai, $waktu)
            );

            $jenisScan = 'masuk';

            if (!$kehadiran->wasRecentlyCreated) {
                // Update jam_pulang if jam_masuk already exists
                $kehadiran->update([
                    'jam_pulang' => $jamScan,
                    'durasi_kerja_menit' => Carbon::parse($kehadiran->jam_masuk)->diffInMinutes($waktu),
                ]);
                $jenisScan = 'pulang';
            }

            $log->update(['is_processed' => true]);

            // Log activity
            AuditLog::create([
                'user_name' => $pegawai->nama_lengkap,
                'role' => $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa',
                'aktivitas' => "Scan Absen {$jenisScan} ({$kehadiran->fresh()->status}) pukul {$jamScan}",
                'modul' => 'Absensi Fingerprint',
            ]);

            return [
                'status' => 'created',
                'jenis' => $jenisScan,
                'pin' => $pin,
                'nama' => $pegawai->nama_lengkap,
                'status_kehadiran' => $kehadiran->fresh()->status,
                'message' => "Berhasil mencatat scan {$jenisScan} untuk {$pegawai->nama_lengkap}"
            ];
        });
    }

    /**
     * Parse raw string from fingerprint hardware or USB log files
     */
    private function parseRawData(string $rawText): ?array
    {
        $rawText = trim($rawText);

        // Format 1 (MAGIC Series Key=Value): "PIN=001 TIME=2026-08-10 08:05:23"
        if (preg_match('/PIN=(\d+)\s+TIME=(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})/', $rawText, $m)) {
            return [
                'pin' => $m[1],
                'waktu' => Carbon::parse($m[2]),
            ];
        }

        // Format 2 (ZKTeco SSR Tab-delimited): "001\t1\t20260810080523\t1\t0\t0\t0"
        $parts = preg_split('/\s+/', $rawText);
        if (count($parts) >= 3 && is_numeric($parts[0])) {
            $pin = $parts[0];
            $timeStr = $parts[2];

            if (strlen($timeStr) === 14 && is_numeric($timeStr)) {
                return [
                    'pin' => $pin,
                    'waktu' => Carbon::createFromFormat('YmdHis', $timeStr),
                ];
            } elseif (count($parts) >= 4 && str_contains($parts[2], '-')) {
                // Format "001  2026-08-10  08:05:23"
                return [
                    'pin' => $pin,
                    'waktu' => Carbon::parse($parts[2] . ' ' . $parts[3]),
                ];
            }
        }

        return null;
    }

    /**
     * Compute initial attendance status
     */
    private function buildKehadiranData(Pegawai $pegawai, Carbon $waktu): array
    {
        $shift = $pegawai->shiftKerja;
        $jamMasukStandar = $shift?->jam_masuk ?? '08:00:00';
        $toleransi = $shift?->toleransi_menit ?? 15;
        $jamScan = $waktu->format('H:i:s');

        // Check active Surat Perintah Tugas (SPT)
        $adaSPT = SuratPerintahTugas::where('pegawai_id', $pegawai->id)
            ->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $waktu->toDateString())
            ->whereDate('tanggal_selesai', '>=', $waktu->toDateString())
            ->exists();

        if ($adaSPT) {
            return [
                'jam_masuk' => $jamScan,
                'status' => 'Dinas Luar',
                'sumber_data' => 'fingerprint',
            ];
        }

        $batasTerlambat = Carbon::parse($jamMasukStandar)->addMinutes($toleransi)->format('H:i:s');
        $terlambatMenit = $jamScan > $batasTerlambat
            ? Carbon::parse($jamMasukStandar)->diffInMinutes(Carbon::parse($jamScan))
            : 0;

        return [
            'jam_masuk' => $jamScan,
            'status' => $terlambatMenit > 0 ? 'Terlambat' : 'Tepat Waktu',
            'terlambat_menit' => $terlambatMenit,
            'sumber_data' => 'fingerprint',
        ];
    }
}
