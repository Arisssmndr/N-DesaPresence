<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\HariLibur;
use App\Models\AbsensiDisesuaikan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LaporanDisesuaikanManager extends Component
{
    use WithPagination;

    // Mode tampilan: 'harian', 'bulanan', 'rentang', 'pusat_cetak'
    public string $mode = 'harian';

    // Filters
    public string $tanggalHarian = '';
    public int $bulanBulanan = 8;
    public int $tahunBulanan = 2026;
    public int $tahunTahunan = 2026;
    public string $tanggalMulai = '';
    public string $tanggalSelesai = '';
    public string $search = '';

    // Modal Edit
    public bool $showEditModal = false;
    public ?int $editPegawaiId = null;
    public string $editTanggal = '';
    public string $editNamaPegawai = '';
    public string $editStatusAsli = 'Alpa';
    public string $editStatusDisesuaikan = 'Hadir';
    public string $editJamMasuk = '08:00';
    public string $editJamPulang = '15:30';
    public ?string $editTandaTangan = null;
    public ?string $editSumberTtd = null;
    public ?string $editTanggalSumberTtd = null;
    public string $editKeterangan = '';

    public array $listBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function mount()
    {
        $this->tanggalHarian   = date('Y-m-d');
        $this->bulanBulanan    = (int) date('m');
        $this->tahunBulanan    = (int) date('Y');
        $this->tahunTahunan    = (int) date('Y');
        $this->tanggalMulai    = date('Y-m-01');
        $this->tanggalSelesai  = date('Y-m-d');
    }

    public function setMode(string $newMode): void
    {
        $this->mode = $newMode;
        $this->resetPage();
    }

    // ─── MODAL EDIT TUNGGAL ──────────────────────────────────────────────────
    public function bukaEdit(int $pegawaiId, string $tanggal): void
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);
        $this->editPegawaiId   = $pegawaiId;
        $this->editTanggal     = $tanggal;
        $this->editNamaPegawai = $pegawai->nama_lengkap;

        // Cek data murni asli
        $kehadiranAsli = Kehadiran::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        // Cek data disesuaikan yang sudah tersimpan
        $disesuaikan = AbsensiDisesuaikan::where('pegawai_id', $pegawaiId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($disesuaikan) {
            $this->editStatusAsli        = $disesuaikan->status_asli;
            $this->editStatusDisesuaikan = $disesuaikan->status_disesuaikan;
            $this->editJamMasuk          = $disesuaikan->jam_masuk_format ?: '08:00';
            $this->editJamPulang         = $disesuaikan->jam_pulang_format ?: '15:30';
            $this->editTandaTangan       = $disesuaikan->tanda_tangan_disesuaikan;
            $this->editSumberTtd         = $disesuaikan->sumber_tanda_tangan;
            $this->editTanggalSumberTtd  = $disesuaikan->tanggal_sumber_ttd ? $disesuaikan->tanggal_sumber_ttd->toDateString() : null;
            $this->editKeterangan        = $disesuaikan->keterangan ?: '';
        } elseif ($kehadiranAsli) {
            $this->editStatusAsli        = $kehadiranAsli->status;
            $this->editStatusDisesuaikan = in_array($kehadiranAsli->status, ['Izin', 'Sakit', 'Alpa']) ? 'Hadir' : $kehadiranAsli->status;
            $this->editJamMasuk          = $kehadiranAsli->jam_masuk_format ?: '08:00';
            $this->editJamPulang         = $kehadiranAsli->jam_pulang_format ?: '15:30';
            $this->editTandaTangan       = $kehadiranAsli->tanda_tangan_masuk ?: $kehadiranAsli->tanda_tangan_pulang;
            $this->editSumberTtd         = $this->editTandaTangan ? 'asli' : null;
            $this->editTanggalSumberTtd  = $this->editTandaTangan ? $tanggal : null;
            $this->editKeterangan        = '';

            // Jika tanda tangan kosong dan status diubah jadi Hadir, cari tanda tangan terdekat
            if (!$this->editTandaTangan && in_array($this->editStatusDisesuaikan, ['Hadir', 'Tepat Waktu', 'Dinas Luar'])) {
                $ttdFound = AbsensiDisesuaikan::cariTandaTanganPegawai($pegawaiId, $tanggal, 7);
                if ($ttdFound) {
                    $this->editTandaTangan      = $ttdFound['signature'];
                    $this->editSumberTtd        = $ttdFound['source'];
                    $this->editTanggalSumberTtd = $ttdFound['date'];
                }
            }
        } else {
            $this->editStatusAsli        = 'Alpa';
            $this->editStatusDisesuaikan = 'Hadir';
            $this->editJamMasuk          = '08:00';
            $this->editJamPulang         = '15:30';
            $this->editKeterangan        = '';

            // Cari tanda tangan otomatis
            $ttdFound = AbsensiDisesuaikan::cariTandaTanganPegawai($pegawaiId, $tanggal, 7);
            if ($ttdFound) {
                $this->editTandaTangan      = $ttdFound['signature'];
                $this->editSumberTtd        = $ttdFound['source'];
                $this->editTanggalSumberTtd = $ttdFound['date'];
            } else {
                $this->editTandaTangan      = null;
                $this->editSumberTtd        = null;
                $this->editTanggalSumberTtd = null;
            }
        }

        $this->showEditModal = true;
    }

    public function updatedEditStatusDisesuaikan($val)
    {
        // Jika diubah ke Hadir dan TTD belum ada, otomatis cari TTD
        if (in_array($val, ['Hadir', 'Tepat Waktu', 'Dinas Luar']) && !$this->editTandaTangan && $this->editPegawaiId) {
            $ttdFound = AbsensiDisesuaikan::cariTandaTanganPegawai($this->editPegawaiId, $this->editTanggal, 7);
            if ($ttdFound) {
                $this->editTandaTangan      = $ttdFound['signature'];
                $this->editSumberTtd        = $ttdFound['source'];
                $this->editTanggalSumberTtd = $ttdFound['date'];
            }
        }
    }

    public function cariUlangTandaTangan(): void
    {
        if (!$this->editPegawaiId) return;

        $ttdFound = AbsensiDisesuaikan::cariTandaTanganPegawai($this->editPegawaiId, $this->editTanggal, 14);
        if ($ttdFound) {
            $this->editTandaTangan      = $ttdFound['signature'];
            $this->editSumberTtd        = $ttdFound['source'];
            $this->editTanggalSumberTtd = $ttdFound['date'];
            $this->dispatch('notify', message: "Tanda tangan ditemukan dari tanggal {$ttdFound['date']} ({$ttdFound['source']})", type: 'success');
        } else {
            $this->dispatch('notify', message: "Tidak ditemukan tanda tangan arsip untuk pegawai ini.", type: 'error');
        }
    }

    public function simpanEdit(): void
    {
        $this->validate([
            'editStatusDisesuaikan' => 'required|in:Hadir,Tepat Waktu,Terlambat,Izin,Sakit,Dinas Luar,Alpa,Libur',
            'editJamMasuk'          => 'nullable|date_format:H:i',
            'editJamPulang'         => 'nullable|date_format:H:i',
        ]);

        $kehadiranAsli = Kehadiran::where('pegawai_id', $this->editPegawaiId)
            ->whereDate('tanggal', $this->editTanggal)
            ->first();

        $durasiMenit = 0;
        if ($this->editJamMasuk && $this->editJamPulang) {
            $in = Carbon::createFromFormat('H:i', $this->editJamMasuk);
            $out = Carbon::createFromFormat('H:i', $this->editJamPulang);
            if ($out->gt($in)) {
                $durasiMenit = $in->diffInMinutes($out);
            }
        }

        AbsensiDisesuaikan::updateOrCreate(
            [
                'pegawai_id' => $this->editPegawaiId,
                'tanggal'    => $this->editTanggal,
            ],
            [
                'kehadiran_id'             => $kehadiranAsli?->id,
                'status_asli'              => $this->editStatusAsli,
                'status_disesuaikan'       => $this->editStatusDisesuaikan,
                'jam_masuk'                => $this->editJamMasuk ? $this->editJamMasuk . ':00' : null,
                'jam_pulang'               => $this->editJamPulang ? $this->editJamPulang . ':00' : null,
                'durasi_kerja_menit'       => $durasiMenit,
                'tanda_tangan_disesuaikan' => $this->editTandaTangan,
                'sumber_tanda_tangan'      => $this->editSumberTtd,
                'tanggal_sumber_ttd'       => $this->editTanggalSumberTtd,
                'keterangan'               => $this->editKeterangan ?: 'Disesuaikan oleh Sekdes/Admin untuk Laporan',
                'dibuat_oleh'              => Auth::id(),
                'diubah_oleh'              => Auth::id(),
            ]
        );

        AuditLog::create([
            'user_id'   => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Admin Sekdes',
            'role'      => Auth::user()->role ?? 'Admin',
            'aktivitas' => "Menyesuaikan absensi {$this->editNamaPegawai} tgl {$this->editTanggal} dari '{$this->editStatusAsli}' ke '{$this->editStatusDisesuaikan}'",
            'modul'     => 'Laporan Disesuaikan',
        ]);

        $this->showEditModal = false;
        $this->dispatch('notify', message: "Data absensi disesuaikan untuk {$this->editNamaPegawai} berhasil disimpan.", type: 'success');
    }

    // ─── 1-CLICK INSTANT ACTIONS ─────────────────────────────────────────────
    public function hadirkanPegawaiCepat(int $pegawaiId, string $tanggal): void
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);
        $kehadiranAsli = Kehadiran::where('pegawai_id', $pegawaiId)->whereDate('tanggal', $tanggal)->first();
        $statusAsli = $kehadiranAsli ? $kehadiranAsli->status : 'Alpa';

        // Ambil tanda tangan asli jika ada, atau pinjam dari H-1 s/d H-7
        $ttd = $kehadiranAsli?->tanda_tangan_masuk ?: $kehadiranAsli?->tanda_tangan_pulang;
        $sumberTtd = 'asli';
        $tglSumber = $tanggal;

        if (!$ttd) {
            $ttdFound = AbsensiDisesuaikan::cariTandaTanganPegawai($pegawaiId, $tanggal, 7);
            if ($ttdFound) {
                $ttd = $ttdFound['signature'];
                $sumberTtd = $ttdFound['source'];
                $tglSumber = $ttdFound['date'];
            }
        }

        AbsensiDisesuaikan::updateOrCreate(
            ['pegawai_id' => $pegawaiId, 'tanggal' => $tanggal],
            [
                'kehadiran_id'             => $kehadiranAsli?->id,
                'status_asli'              => $statusAsli,
                'status_disesuaikan'       => 'Hadir',
                'jam_masuk'                => '08:00:00',
                'jam_pulang'               => '15:30:00',
                'durasi_kerja_menit'       => 450,
                'tanda_tangan_disesuaikan' => $ttd,
                'sumber_tanda_tangan'      => $sumberTtd,
                'tanggal_sumber_ttd'       => $tglSumber,
                'keterangan'               => 'Disesuaikan Hadir Cepat oleh Admin/Sekdes',
                'dibuat_oleh'              => Auth::id(),
                'diubah_oleh'              => Auth::id(),
            ]
        );

        $this->dispatch('notify', message: "{$pegawai->nama_lengkap} berhasil disesuaikan statusnya menjadi HADIR.", type: 'success');
    }

    public function hadirkanSemuaHariIni(): void
    {
        $pegawais = Pegawai::where('status_aktif', true)->get();
        $tgl = $this->tanggalHarian;
        $count = 0;

        foreach ($pegawais as $p) {
            $kehadiranAsli = Kehadiran::where('pegawai_id', $p->id)->whereDate('tanggal', $tgl)->first();
            $statusAsli = $kehadiranAsli ? $kehadiranAsli->status : 'Alpa';

            // Jika asli sudah hadir & punya TTD, lewati jika belum disesuaikan
            $ttd = $kehadiranAsli?->tanda_tangan_masuk ?: $kehadiranAsli?->tanda_tangan_pulang;
            $sumberTtd = $ttd ? 'asli' : null;
            $tglSumber = $ttd ? $tgl : null;

            if (!$ttd) {
                $ttdFound = AbsensiDisesuaikan::cariTandaTanganPegawai($p->id, $tgl, 7);
                if ($ttdFound) {
                    $ttd = $ttdFound['signature'];
                    $sumberTtd = $ttdFound['source'];
                    $tglSumber = $ttdFound['date'];
                }
            }

            AbsensiDisesuaikan::updateOrCreate(
                ['pegawai_id' => $p->id, 'tanggal' => $tgl],
                [
                    'kehadiran_id'             => $kehadiranAsli?->id,
                    'status_asli'              => $statusAsli,
                    'status_disesuaikan'       => 'Hadir',
                    'jam_masuk'                => $kehadiranAsli?->jam_masuk ?: '08:00:00',
                    'jam_pulang'               => $kehadiranAsli?->jam_pulang ?: '15:30:00',
                    'durasi_kerja_menit'       => 450,
                    'tanda_tangan_disesuaikan' => $ttd,
                    'sumber_tanda_tangan'      => $sumberTtd,
                    'tanggal_sumber_ttd'       => $tglSumber,
                    'keterangan'               => 'Batch Hadirkan Seluruh Pegawai untuk Laporan',
                    'dibuat_oleh'              => Auth::id(),
                    'diubah_oleh'              => Auth::id(),
                ]
            );
            $count++;
        }

        AuditLog::create([
            'user_id'   => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Admin Sekdes',
            'role'      => Auth::user()->role ?? 'Admin',
            'aktivitas' => "Batch penyesuaian: Menghadirkan semua {$count} pegawai untuk tanggal {$tgl}",
            'modul'     => 'Laporan Disesuaikan',
        ]);

        $this->dispatch('notify', message: "Berhasil menyesuaikan {$count} pegawai menjadi HADIR untuk tanggal {$tgl}.", type: 'success');
    }

    public function resetKeDataAsli(int $pegawaiId, string $tanggal): void
    {
        AbsensiDisesuaikan::where('pegawai_id', $pegawaiId)->whereDate('tanggal', $tanggal)->delete();
        $this->dispatch('notify', message: 'Data penyesuaian dihapus, kembali ke data presensi murni.', type: 'info');
    }

    public function resetSemuaTanggal(): void
    {
        $count = AbsensiDisesuaikan::whereDate('tanggal', $this->tanggalHarian)->delete();
        $this->dispatch('notify', message: "{$count} data penyesuaian tanggal {$this->tanggalHarian} telah di-reset ke data murni.", type: 'info');
    }

    // ─── URL GENERATORS FOR PDF DOWNLOADS ────────────────────────────────────
    public function getUrlHarian(): string
    {
        return route('laporan-disesuaikan.harian', ['tanggal' => $this->tanggalHarian]);
    }

    public function getUrlBulanan(): string
    {
        return route('laporan-disesuaikan.bulanan', ['bulan' => $this->bulanBulanan, 'tahun' => $this->tahunBulanan]);
    }

    public function getUrlTahunan(): string
    {
        return route('laporan-disesuaikan.tahunan', ['tahun' => $this->tahunTahunan]);
    }

    public function getUrlRentang(): string
    {
        return route('laporan-disesuaikan.rentang', [
            'tanggal_mulai'   => $this->tanggalMulai,
            'tanggal_selesai' => $this->tanggalSelesai,
        ]);
    }

    public function render()
    {
        $pegawaisQuery = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->when($this->search, fn($q) => $q->where('nama_lengkap', 'like', '%' . $this->search . '%'))
            ->orderBy('nama_lengkap');

        $pegawais = $pegawaisQuery->get();
        $pegawaiIds = $pegawais->pluck('id')->toArray();

        // ─── DATA HARIAN ─────────────────────────────────────────────────────
        $hariLiburHariIni = HariLibur::whereDate('tanggal', $this->tanggalHarian)->first();
        $isWeekendHarian = Carbon::parse($this->tanggalHarian)->isWeekend();

        $disesuaikanHarian = AbsensiDisesuaikan::whereIn('pegawai_id', $pegawaiIds)
            ->whereDate('tanggal', $this->tanggalHarian)
            ->get()
            ->keyBy('pegawai_id');

        $kehadiranHarian = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereDate('tanggal', $this->tanggalHarian)
            ->get()
            ->keyBy('pegawai_id');

        $harianList = [];
        $rekapHarian = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'dinas' => 0, 'alpa' => 0, 'disesuaikan' => 0];

        foreach ($pegawais as $p) {
            $adj = $disesuaikanHarian->get($p->id);
            $ori = $kehadiranHarian->get($p->id);

            $isAdjusted = !is_null($adj);
            $activeStatus = $adj ? $adj->status_disesuaikan : ($ori ? $ori->status : ($isWeekendHarian || $hariLiburHariIni ? 'Libur' : 'Alpa'));
            $originalStatus = $ori ? $ori->status : ($isWeekendHarian || $hariLiburHariIni ? 'Libur' : 'Alpa');

            $jamMasuk = $adj ? $adj->jam_masuk_format : ($ori ? $ori->jam_masuk_format : '-');
            $jamPulang = $adj ? $adj->jam_pulang_format : ($ori ? $ori->jam_pulang_format : '-');
            $durasi = $adj ? ($adj->durasi_kerja_menit ? floor($adj->durasi_kerja_menit/60).'j '.($adj->durasi_kerja_menit%60).'m' : '-') : ($ori && $ori->durasi_kerja_menit ? floor($ori->durasi_kerja_menit/60).'j '.($ori->durasi_kerja_menit%60).'m' : '-');

            $hasTtd = $adj ? !empty($adj->tanda_tangan_disesuaikan) : ($ori ? (!empty($ori->tanda_tangan_masuk) || !empty($ori->tanda_tangan_pulang)) : false);
            $ttdLabel = $adj ? $adj->label_sumber_ttd : ($hasTtd ? 'Tanda Tangan Asli' : 'Belum Ada TTD');
            $ttdSrc = $adj ? $adj->tanda_tangan_src : ($ori ? ($ori->tanda_tangan_masuk_src ?: $ori->tanda_tangan_pulang_src) : null);

            if ($isAdjusted) {
                $rekapHarian['disesuaikan']++;
            }

            match ($activeStatus) {
                'Hadir', 'Tepat Waktu' => $rekapHarian['hadir']++,
                'Terlambat'            => $rekapHarian['terlambat']++,
                'Izin'                 => $rekapHarian['izin']++,
                'Sakit'                => $rekapHarian['sakit']++,
                'Dinas Luar'           => $rekapHarian['dinas']++,
                default                => $rekapHarian['alpa']++,
            };

            $harianList[] = [
                'pegawai'         => $p,
                'status_asli'     => $originalStatus,
                'status_aktif'    => $activeStatus,
                'is_adjusted'     => $isAdjusted,
                'jam_masuk'       => $jamMasuk,
                'jam_pulang'      => $jamPulang,
                'durasi'          => $durasi,
                'has_ttd'         => $hasTtd,
                'ttd_label'       => $ttdLabel,
                'ttd_src'         => $ttdSrc,
                'adjusted_record' => $adj,
                'original_record' => $ori,
            ];
        }

        // ─── DATA BULANAN MATRIX ─────────────────────────────────────────────
        $carbonBulan  = Carbon::createFromDate($this->tahunBulanan, $this->bulanBulanan, 1);
        $daysInMonth  = $carbonBulan->daysInMonth;
        $namaBulan    = $carbonBulan->translatedFormat('F');

        $hariLiburBulan = HariLibur::whereYear('tanggal', $this->tahunBulanan)
            ->whereMonth('tanggal', $this->bulanBulanan)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->format('Y-m-d'))
            ->flip()
            ->toArray();

        $adjBulan = AbsensiDisesuaikan::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $this->tahunBulanan)
            ->whereMonth('tanggal', $this->bulanBulanan)
            ->get()
            ->groupBy('pegawai_id');

        $oriBulan = Kehadiran::whereIn('pegawai_id', $pegawaiIds)
            ->whereYear('tanggal', $this->tahunBulanan)
            ->whereMonth('tanggal', $this->bulanBulanan)
            ->get()
            ->groupBy('pegawai_id');

        $matrixBulanan = [];
        $summaryBulanan = [];
        $todayStr = Carbon::today()->toDateString();

        foreach ($pegawais as $p) {
            $adjMap = $adjBulan->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));
            $oriMap = $oriBulan->get($p->id, collect())->keyBy(fn($k) => Carbon::parse($k->tanggal)->format('Y-m-d'));

            $pSum = ['H' => 0, 'T' => 0, 'A' => 0, 'I' => 0, 'D' => 0, 'L' => 0, 'adjusted_count' => 0];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dStr = sprintf("%04d-%02d-%02d", $this->tahunBulanan, $this->bulanBulanan, $d);
                $dt = Carbon::createFromDate($this->tahunBulanan, $this->bulanBulanan, $d);
                $isWk = $dt->isWeekend();
                $isHoli = isset($hariLiburBulan[$dStr]) || $isWk;
                $isFut = ($dStr > $todayStr);

                $adjRec = $adjMap->get($dStr);
                $oriRec = $oriMap->get($dStr);
                $rec = $adjRec ?: $oriRec;

                if ($adjRec) {
                    $pSum['adjusted_count']++;
                }

                if ($rec) {
                    $st = $rec->status_disesuaikan ?? $rec->status;
                    $code = match ($st) {
                        'Hadir', 'Tepat Waktu' => 'H',
                        'Terlambat'            => 'T',
                        'Izin', 'Sakit'        => 'I',
                        'Dinas Luar'           => 'D',
                        default                => 'A',
                    };
                } elseif ($isHoli) {
                    $code = 'L';
                } elseif ($isFut) {
                    $code = '-';
                } else {
                    $code = 'A';
                }

                $matrixBulanan[$p->id][$d] = [
                    'code'        => $code,
                    'is_adjusted' => !is_null($adjRec),
                    'date_str'    => $dStr,
                ];

                if (isset($pSum[$code])) {
                    $pSum[$code]++;
                }
            }

            $totalHk = $daysInMonth - $pSum['L'];
            $totalHadir = $pSum['H'] + $pSum['T'];
            $pSum['persen'] = $totalHk > 0 ? round(($totalHadir / $totalHk) * 100, 1) : 0;
            $summaryBulanan[$p->id] = $pSum;
        }

        $tahunOptions = range(date('Y') - 3, date('Y') + 1);

        return view('livewire.laporan-disesuaikan-manager', [
            'pegawais'         => $pegawais,
            'harianList'       => $harianList,
            'rekapHarian'      => $rekapHarian,
            'isWeekendHarian'  => $isWeekendHarian,
            'hariLiburHariIni' => $hariLiburHariIni,
            'daysInMonth'      => $daysInMonth,
            'namaBulan'        => $namaBulan,
            'matrixBulanan'    => $matrixBulanan,
            'summaryBulanan'   => $summaryBulanan,
            'tahunOptions'     => $tahunOptions,
            'urlHarian'        => $this->getUrlHarian(),
            'urlBulanan'       => $this->getUrlBulanan(),
            'urlTahunan'       => $this->getUrlTahunan(),
            'urlRentang'       => $this->getUrlRentang(),
        ])->layout('layouts.app', ['title' => 'Laporan Disesuaikan (Shadow Layer) — Presence Desa']);
    }
}
