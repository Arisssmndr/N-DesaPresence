<?php

namespace App\Services;

use App\Models\HariLibur;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KalenderNasionalService
{
    /**
     * Daftar Hari Peringatan & Hari Besar Nasional Indonesia (Tetap).
     * Format: 'MM-DD' => 'Nama Peringatan Nasional'
     */
    private const HARI_PERINGATAN_NASIONAL = [
        '01-03' => 'Hari Amal Bakti Kementerian Agama',
        '01-10' => 'Hari Gerakan Satu Juta Pohon',
        '01-15' => 'Hari Dharma Samudera',
        '01-25' => 'Hari Gizi dan Makanan',
        '02-09' => 'Hari Pers Nasional (HPN)',
        '02-28' => 'Hari Gizi Nasional Indonesia',
        '03-01' => 'Hari Penegakan Kedaulatan Negara',
        '03-09' => 'Hari Musik Nasional',
        '03-30' => 'Hari Film Nasional',
        '04-09' => 'Hari TNI Angkatan Udara',
        '04-21' => 'Hari Kartini',
        '04-22' => 'Hari Bumi',
        '04-24' => 'Hari Angkutan Nasional',
        '04-28' => 'Hari Puisi Nasional',
        '05-02' => 'Hari Pendidikan Nasional (Hardiknas)',
        '05-17' => 'Hari Buku Nasional',
        '05-20' => 'Hari Kebangkitan Nasional (Harkitnas)',
        '05-29' => 'Hari Lanjut Usia Nasional',
        '06-01' => 'Hari Lahir Pancasila',
        '06-21' => 'Hari Krida Pertanian',
        '06-24' => 'Hari Bidan Nasional',
        '06-29' => 'Hari Keluarga Nasional (Harganas)',
        '07-01' => 'Hari Bhayangkara (Polri)',
        '07-05' => 'Hari Bank Indonesia',
        '07-12' => 'Hari Koperasi Indonesia',
        '07-22' => 'Hari Kejaksaan Republik Indonesia',
        '07-23' => 'Hari Anak Nasional',
        '08-10' => 'Hari Veteran Nasional / Hari Kebangkitan Teknologi',
        '08-14' => 'Hari Pramuka',
        '08-17' => 'Hari Kemerdekaan Republik Indonesia (HUT RI)',
        '09-01' => 'Hari Polisi Wanita (Polwan)',
        '09-09' => 'Hari Olahraga Nasional (Haornas)',
        '09-11' => 'Hari Radio Republik Indonesia (RRI)',
        '09-17' => 'Hari Perhubungan Nasional / PMI',
        '09-24' => 'Hari Tani Nasional',
        '09-27' => 'Hari Pos dan Telekomunikasi',
        '10-01' => 'Hari Kesaktian Pancasila',
        '10-02' => 'Hari Batik Nasional',
        '10-05' => 'Hari Tentara Nasional Indonesia (TNI)',
        '10-14' => 'Hari Penglihatan Sedunia',
        '10-24' => 'Hari Dokter Nasional',
        '10-28' => 'Hari Sumpah Pemuda',
        '10-30' => 'Hari Keuangan Nasional',
        '11-05' => 'Hari Cinta Puspa dan Satwa Nasional',
        '11-10' => 'Hari Pahlawan Nasional',
        '11-12' => 'Hari Kesehatan Nasional (HKN) / Hari Ayah',
        '11-14' => 'Hari Brimob',
        '11-25' => 'Hari Guru Nasional (PGRI)',
        '11-28' => 'Hari Menanam Pohon Indonesia',
        '11-29' => 'Hari KORPRI',
        '12-03' => 'Hari Disabilitas Internasional',
        '12-09' => 'Hari Anti Korupsi Sedunia (Hakordia)',
        '12-10' => 'Hari Hak Asasi Manusia (HAM)',
        '12-13' => 'Hari Nusantara',
        '12-15' => 'Hari Juang Kartika TNI AD',
        '12-19' => 'Hari Bela Negara',
        '12-22' => 'Hari Ibu',
    ];

    /**
     * Ambil seluruh hari libur nasional & cuti bersama resmi dari API untuk suatu tahun.
     */
    public function getHariLiburTahun(int $tahun): array
    {
        $cacheKey = "kalender_libur_nasional_{$tahun}";

        return Cache::remember($cacheKey, 86400 * 30, function () use ($tahun) {
            $apiUrl = "https://api-hari-libur.vercel.app/api?year={$tahun}";

            try {
                $response = Http::timeout(6)->get($apiUrl);

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['data']) && is_array($json['data'])) {
                        $holidays = [];
                        foreach ($json['data'] as $item) {
                            $isCuti = str_contains(strtolower($item['description'] ?? ''), 'cuti bersama');
                            $holidays[] = [
                                'tanggal'     => $item['date'],
                                'nama'        => $item['description'],
                                'jenis'       => $isCuti ? 'cuti_bersama' : 'nasional',
                                'is_libur'    => true,
                            ];
                        }
                        return $holidays;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gagal fetch API Kalender Nasional ({$tahun}): " . $e->getMessage());
            }

            // Fallback default jika koneksi internet terputus
            return $this->getFallbackHolidays($tahun);
        });
    }

    /**
     * Ambil data lengkap kalender bulanan (Hari Libur Resmi + Hari Peringatan Nasional).
     */
    public function getKalenderBulan(int $tahun, int $bulan): array
    {
        $allHolidays = $this->getHariLiburTahun($tahun);
        $monthStr = sprintf('%02d', $bulan);

        // Filter libur nasional bulan ini
        $liburBulanIni = [];
        foreach ($allHolidays as $h) {
            if (str_starts_with($h['tanggal'], "{$tahun}-{$monthStr}")) {
                $liburBulanIni[$h['tanggal']] = $h;
            }
        }

        // Ambil hari libur lokal dari database
        $liburDb = HariLibur::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        foreach ($liburDb as $ld) {
            $dateStr = $ld->tanggal->format('Y-m-d');
            if (!isset($liburBulanIni[$dateStr])) {
                $liburBulanIni[$dateStr] = [
                    'tanggal'  => $dateStr,
                    'nama'     => $ld->nama_hari_libur,
                    'jenis'    => $ld->jenis,
                    'is_libur' => true,
                ];
            }
        }

        // Ambil Hari Peringatan Nasional (misal: Hari Pahlawan 10 Nov)
        $peringatanBulanIni = [];
        foreach (self::HARI_PERINGATAN_NASIONAL as $mmdd => $namaPeringatan) {
            if (str_starts_with($mmdd, "{$monthStr}-")) {
                $dateStr = "{$tahun}-{$mmdd}";
                $peringatanBulanIni[$dateStr] = [
                    'tanggal'  => $dateStr,
                    'nama'     => $namaPeringatan,
                    'jenis'    => 'peringatan_nasional',
                    'is_libur' => isset($liburBulanIni[$dateStr]),
                ];
            }
        }

        return [
            'libur'      => $liburBulanIni,
            'peringatan' => $peringatanBulanIni,
        ];
    }

    /**
     * Sinkronkan otomatis data libur nasional dari API ke tabel database `hari_liburs`.
     */
    public function sinkronkanKeDatabase(int $tahun): int
    {
        $holidays = $this->getHariLiburTahun($tahun);
        $count = 0;

        foreach ($holidays as $h) {
            HariLibur::updateOrCreate(
                ['tanggal' => $h['tanggal']],
                [
                    'nama_hari_libur' => $h['nama'],
                    'jenis'           => $h['jenis'],
                ]
            );
            $count++;
        }

        Cache::forget("kalender_libur_nasional_{$tahun}");

        AuditLog::create([
            'user_name' => auth()->user()->name ?? 'System API Sync',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Sinkronisasi {$count} hari libur nasional RI tahun {$tahun} dari Kalender Resmi API",
            'modul'     => 'Kalender Nasional',
        ]);

        return $count;
    }

    /**
     * Fallback data hari libur nasional Indonesia (jika API offline).
     */
    private function getFallbackHolidays(int $tahun): array
    {
        return [
            ['tanggal' => "{$tahun}-01-01", 'nama' => 'Tahun Baru Masehi', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-05-01", 'nama' => 'Hari Buruh Internasional', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-06-01", 'nama' => 'Hari Lahir Pancasila', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-08-17", 'nama' => 'Hari Kemerdekaan Republik Indonesia', 'jenis' => 'nasional', 'is_libur' => true],
            ['tanggal' => "{$tahun}-12-25", 'nama' => 'Hari Raya Natal', 'jenis' => 'nasional', 'is_libur' => true],
        ];
    }
}
