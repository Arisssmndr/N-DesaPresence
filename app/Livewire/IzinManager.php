<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\IzinSakit;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\PengajuanAbsenLuar;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IzinManager extends Component
{
    use WithPagination, WithFileUploads;

    // ─── TAB & FORM NAVIGATION ──────────────────────────────────────────────
    public string $activeTab = 'izin'; // 'izin' | 'absen_manual'
    public bool $showIzinForm = false;
    public bool $showManualForm = false;

    // ─── MODAL REJECT & CONFLICT ─────────────────────────────────────────────
    public bool $showRejectModal = false;
    public bool $showConflictModal = false;
    public ?array $conflictInfo = null;
    public ?int $selectedIzinId = null;
    public ?IzinSakit $selectedIzin = null;
    public string $catatanPenolakan = '';

    // Form Izin
    public ?int $pegawai_id = null;
    public string $jenis = 'izin_pribadi';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public string $keterangan = '';
    public $file_lampiran;

    // ─── FORM ABSEN MANUAL (OVERRIDE) ────────────────────────────────────────
    public ?int $manual_pegawai_id = null;
    public string $manual_tanggal = '';
    public string $manual_tanggal_mulai = '';
    public string $manual_tanggal_selesai = '';
    public string $manual_jam_masuk = '08:00';
    public string $manual_jam_pulang = '15:30';
    public string $manual_status = 'Hadir';
    public string $manual_keterangan = '';

    protected $queryString = [
        'activeTab' => ['except' => 'izin', 'as' => 'tab'],
    ];

    public function mount()
    {
        $today = Carbon::today()->toDateString();
        $this->tanggal_mulai = $today;
        $this->tanggal_selesai = $today;
        $this->manual_tanggal = $today;
        $this->manual_tanggal_mulai = $today;
        $this->manual_tanggal_selesai = $today;

        if (request()->query('tab') === 'absen_manual') {
            $this->activeTab = 'absen_manual';
        }
    }

    public function openIzinForm()
    {
        $this->resetFormIzin();
        $this->showIzinForm = true;
    }

    public function closeIzinForm()
    {
        $this->showIzinForm = false;
        $this->resetFormIzin();
    }

    public function openManualForm()
    {
        $this->resetManualForm();
        $this->showManualForm = true;
    }

    public function closeManualForm()
    {
        $this->showManualForm = false;
        $this->resetManualForm();
    }

    private function resetManualForm()
    {
        $today = Carbon::today()->toDateString();
        $this->manual_pegawai_id = null;
        $this->manual_tanggal = $today;
        $this->manual_tanggal_mulai = $today;
        $this->manual_tanggal_selesai = $today;
        $this->manual_jam_masuk = '08:00';
        $this->manual_jam_pulang = '15:30';
        $this->manual_status = 'Hadir';
        $this->manual_keterangan = '';
        $this->resetValidation();
    }

    public function closeConflictModal()
    {
        $this->showConflictModal = false;
        $this->conflictInfo = null;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->showIzinForm = false;
        $this->showManualForm = false;
        $this->resetPage('izin_page');
        $this->resetPage('manual_page');
    }

    // ═════════════════════════════════════════════════════════════════════════
    // FITUR 1: MANAJEMEN IZIN / SAKIT / CUTI
    // ═════════════════════════════════════════════════════════════════════════

    protected function rulesIzin(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis' => 'required|in:izin_pribadi,izin_kedinasan,sakit_dengan_surat,sakit_tanpa_surat,cuti_tahunan,duka_cita,melahirkan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:3',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function openCreateModal()
    {
        $this->resetFormIzin();
        $this->showModal = true;
    }

    public function createIzin()
    {
        $this->validate($this->rulesIzin());

        // 1. Cek bentrok dengan presensi langsung fisik di kantor
        $kehadiranBentrok = Kehadiran::where('pegawai_id', $this->pegawai_id)
            ->where(function ($q) {
                $q->whereDate('tanggal', '>=', $this->tanggal_mulai)
                  ->whereDate('tanggal', '<=', $this->tanggal_selesai);
            })
            ->where(function ($q) {
                $q->whereNotNull('jam_masuk')
                  ->orWhereIn('sumber_data', ['web_signature', 'fingerprint']);
            })
            ->first();

        if ($kehadiranBentrok) {
            $pegawai = Pegawai::find($this->pegawai_id);
            $tglStr = Carbon::parse($kehadiranBentrok->tanggal)->isoFormat('dddd, D MMMM Y');
            
            if ($kehadiranBentrok->jam_pulang) {
                $judul = 'Sudah Absen Pulang';
                $statusStr = 'Absen Pulang (' . substr($kehadiranBentrok->jam_pulang, 0, 5) . ' WIB)';
                $pesan = "Pegawai ini sudah menyelesaikan absensi masuk dan pulang pada {$tglStr}.";
            } elseif ($kehadiranBentrok->jam_masuk) {
                $judul = 'Sudah Absen Masuk';
                $statusStr = 'Absen Masuk (' . substr($kehadiranBentrok->jam_masuk, 0, 5) . ' WIB)';
                $pesan = "Pegawai ini sudah melakukan absensi masuk kantor pada {$tglStr}.";
            } else {
                $judul = 'Sudah Melakukan Presensi';
                $statusStr = 'Hadir Sah di Kantor';
                $pesan = "Data kehadiran fisik pegawai sudah sah tercatat pada {$tglStr}.";
            }
            
            $this->conflictInfo = [
                'icon'         => 'warning',
                'tipe'         => 'kehadiran_fisik',
                'title'        => $judul,
                'badge'        => 'Pemberitahuan',
                'pegawai_nama' => $pegawai->nama_lengkap ?? 'Perangkat Desa',
                'tanggal'      => $tglStr,
                'status'       => $statusStr,
                'jam_info'     => $statusStr,
                'pesan'        => $pesan,
            ];
            $this->showConflictModal = true;
            return;
        }

        // 2. Cek tumpang tindih izin aktif
        $izinLama = IzinSakit::where('pegawai_id', $this->pegawai_id)
            ->where('status', '!=', 'ditolak')
            ->where(function ($q) {
                $q->where(function($sub) {
                    $sub->whereDate('tanggal_mulai', '>=', $this->tanggal_mulai)
                        ->whereDate('tanggal_mulai', '<=', $this->tanggal_selesai);
                })->orWhere(function($sub) {
                    $sub->whereDate('tanggal_selesai', '>=', $this->tanggal_mulai)
                        ->whereDate('tanggal_selesai', '<=', $this->tanggal_selesai);
                })->orWhere(function ($sub) {
                    $sub->whereDate('tanggal_mulai', '<=', $this->tanggal_mulai)
                        ->whereDate('tanggal_selesai', '>=', $this->tanggal_selesai);
                });
            })->first();

        if ($izinLama) {
            $pegawai = Pegawai::find($this->pegawai_id);
            $mulai = Carbon::parse($izinLama->tanggal_mulai)->isoFormat('D MMMM Y');
            $selesai = Carbon::parse($izinLama->tanggal_selesai)->isoFormat('D MMMM Y');

            $this->conflictInfo = [
                'icon'         => 'warning',
                'tipe'         => 'izin_aktif',
                'title'        => 'Masa Izin / Sakit Aktif',
                'badge'        => 'Pemberitahuan',
                'pegawai_nama' => $pegawai->nama_lengkap ?? 'Perangkat Desa',
                'tanggal'      => "{$mulai} s/d {$selesai}",
                'status'       => ucfirst(str_replace('_', ' ', $izinLama->jenis)),
                'jam_info'     => "Status: " . ucfirst($izinLama->status),
                'pesan'        => "Pegawai ini sudah memiliki catatan izin aktif ({$izinLama->label_jenis}) pada periode ini.",
            ];
            $this->showConflictModal = true;
            return;
        }

        DB::transaction(function () {
            $lampiranPath = null;
            if ($this->file_lampiran) {
                $lampiranPath = $this->file_lampiran->store('izin-lampiran', 'public');
            }

            $start = Carbon::parse($this->tanggal_mulai);
            $end = Carbon::parse($this->tanggal_selesai);
            $jumlahHari = $start->diffInDays($end) + 1;

            // Dibuat oleh Admin / Kades -> Langsung Disetujui (Otomatis)
            $izin = IzinSakit::create([
                'pegawai_id' => $this->pegawai_id,
                'jenis' => $this->jenis,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'jumlah_hari' => $jumlahHari,
                'keterangan' => $this->keterangan,
                'file_lampiran' => $lampiranPath,
                'status' => 'disetujui',
                'diproses_oleh' => auth()->id(),
            ]);

            $this->applyIzinAttendance($izin);

            $pegawai = Pegawai::find($this->pegawai_id);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Mencatat Izin/Sakit {$this->jenis} langsung untuk {$pegawai->nama_lengkap} ({$jumlahHari} hari: {$this->tanggal_mulai} s/d {$this->tanggal_selesai})",
                'modul' => 'Izin & Sakit',
            ]);

            $msg = "Data Izin/Sakit untuk {$pegawai->nama_lengkap} berhasil disimpan & langsung berlaku.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
            $this->dispatch('refresh-notifications');
            $this->closeIzinForm();
        });
    }

    public function approve(int $id)
    {
        DB::transaction(function () use ($id) {
            $izin = IzinSakit::where('id', $id)->lockForUpdate()->firstOrFail();
            $izin->update([
                'status' => 'disetujui',
                'diproses_oleh' => auth()->id(),
            ]);

            $this->applyIzinAttendance($izin);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menyetujui izin {$izin->jenis} untuk {$izin->pegawai->nama_lengkap}",
                'modul' => 'Izin & Sakit',
            ]);

            $msg = "Pengajuan Izin/Sakit telah disetujui.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
            $this->dispatch('refresh-notifications');
        });
    }

    public function konfirmasiTolak(int $id)
    {
        $this->selectedIzinId = $id;
        $this->selectedIzin = IzinSakit::with('pegawai')->findOrFail($id);
        $this->catatanPenolakan = '';
        $this->showRejectModal = true;
    }

    public function tutupRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedIzinId = null;
        $this->selectedIzin = null;
        $this->catatanPenolakan = '';
    }

    public function reject()
    {
        $this->validate([
            'catatanPenolakan' => 'required|string|min:5|max:500',
        ], [
            'catatanPenolakan.required' => 'Wajib mengisi alasan penolakan.',
            'catatanPenolakan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $id = $this->selectedIzinId;

        DB::transaction(function () use ($id) {
            $izin = IzinSakit::where('id', $id)->lockForUpdate()->firstOrFail();
            $izin->update([
                'status' => 'ditolak',
                'diproses_oleh' => auth()->id(),
                'keterangan' => $izin->keterangan . ' [Ditolak: ' . $this->catatanPenolakan . ']',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menolak izin {$izin->jenis} untuk {$izin->pegawai->nama_lengkap} (Alasan: {$this->catatanPenolakan})",
                'modul' => 'Izin & Sakit',
            ]);

            $msg = "Pengajuan Izin/Sakit telah ditolak.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'info');
            $this->dispatch('refresh-notifications');
            $this->tutupRejectModal();
        });
    }

    private function applyIzinAttendance(IzinSakit $izin)
    {
        $start = Carbon::parse($izin->tanggal_mulai);
        $end = Carbon::parse($izin->tanggal_selesai);
        $statusAbsen = str_contains($izin->jenis, 'sakit') ? 'Sakit' : 'Izin';

        while ($start->lte($end)) {
            $dateStr = $start->toDateString();
            $existing = Kehadiran::where('pegawai_id', $izin->pegawai_id)
                ->whereDate('tanggal', $dateStr)
                ->first();

            if (!$existing) {
                Kehadiran::create([
                    'pegawai_id'        => $izin->pegawai_id,
                    'tanggal'           => $dateStr,
                    'status'            => $statusAbsen,
                    'sumber_data'       => 'manual_admin',
                    'diverifikasi_oleh' => $izin->diproses_oleh ?? auth()->id(),
                    'keterangan'        => "Izin/Sakit (" . ucfirst(str_replace('_', ' ', $izin->jenis)) . "): {$izin->keterangan}"
                ]);
            } elseif (!$existing->jam_masuk) {
                $existing->update([
                    'status'            => $statusAbsen,
                    'sumber_data'       => 'manual_admin',
                    'diverifikasi_oleh' => $izin->diproses_oleh ?? auth()->id(),
                    'keterangan'        => "Izin/Sakit (" . ucfirst(str_replace('_', ' ', $izin->jenis)) . "): {$izin->keterangan}"
                ]);
            }
            $start->addDay();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFormIzin();
    }

    private function resetFormIzin()
    {
        $this->pegawai_id = null;
        $this->jenis = 'izin_pribadi';
        $this->tanggal_mulai = Carbon::today()->toDateString();
        $this->tanggal_selesai = Carbon::today()->toDateString();
        $this->keterangan = '';
        $this->file_lampiran = null;
        $this->resetValidation();
    }

    // ═════════════════════════════════════════════════════════════════════════
    // FITUR 2: INPUT ABSEN MANUAL ADMIN (OVERRIDE KEHADIRAN LANGSUNG)
    // ═════════════════════════════════════════════════════════════════════════

    public function updatedManualTanggal($value)
    {
        $this->manual_tanggal_mulai = $value;
        $this->manual_tanggal_selesai = $value;
    }

    protected function rulesManual(): array
    {
        return [
            'manual_pegawai_id'        => 'required|exists:pegawais,id',
            'manual_tanggal_mulai'     => 'required|date',
            'manual_tanggal_selesai'   => 'nullable|date|after_or_equal:manual_tanggal_mulai',
            'manual_status'            => 'required|in:Hadir,Alpa,Izin,Sakit',
            'manual_keterangan'        => 'required|string|min:5|max:255',
        ];
    }

    public function saveManualAttendance()
    {
        if (!empty($this->manual_tanggal) && (empty($this->manual_tanggal_mulai) || $this->manual_tanggal_mulai === Carbon::today()->toDateString())) {
            $this->manual_tanggal_mulai = $this->manual_tanggal;
            $this->manual_tanggal_selesai = $this->manual_tanggal;
        }
        if (empty($this->manual_tanggal_selesai)) {
            $this->manual_tanggal_selesai = $this->manual_tanggal_mulai;
        }

        $this->validate($this->rulesManual(), [
            'manual_pegawai_id.required'      => 'Pilih pegawai terlebih dahulu.',
            'manual_tanggal_mulai.required'   => 'Pilih tanggal mulai presensi.',
            'manual_tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'manual_keterangan.required'      => 'Wajib mengisi alasan / justifikasi presensi manual.',
            'manual_keterangan.min'           => 'Alasan minimal 5 karakter.',
        ]);

        $pegawai = Pegawai::findOrFail($this->manual_pegawai_id);
        $startDate = Carbon::parse($this->manual_tanggal_mulai);
        $endDate = Carbon::parse($this->manual_tanggal_selesai);

        // 1. Conflict Guard (Hanya jika status Hadir/Alpa): Cek apakah pegawai sedang izin/sakit resmi di rentang tanggal ini
        if (in_array($this->manual_status, ['Hadir', 'Alpa'])) {
            $izinBentrok = IzinSakit::where('pegawai_id', $this->manual_pegawai_id)
                ->where('status', '!=', 'ditolak')
                ->where(function ($q) {
                    $q->whereDate('tanggal_mulai', '<=', $this->manual_tanggal_selesai)
                      ->whereDate('tanggal_selesai', '>=', $this->manual_tanggal_mulai);
                })
                ->first();

            if ($izinBentrok) {
                $mulai = Carbon::parse($izinBentrok->tanggal_mulai)->isoFormat('D MMMM Y');
                $selesai = Carbon::parse($izinBentrok->tanggal_selesai)->isoFormat('D MMMM Y');
                
                $this->conflictInfo = [
                    'icon'         => 'warning',
                    'tipe'         => 'izin_aktif',
                    'title'        => 'Masa Izin / Sakit Aktif',
                    'badge'        => 'Pemberitahuan',
                    'pegawai_nama' => $pegawai->nama_lengkap ?? 'Perangkat Desa',
                    'tanggal'      => "{$mulai} s/d {$selesai}",
                    'status'       => ucfirst(str_replace('_', ' ', $izinBentrok->jenis)),
                    'jam_info'     => "Status: " . ucfirst($izinBentrok->status),
                    'pesan'        => "Pegawai ini sedang dalam masa perizinan aktif ({$izinBentrok->label_jenis}) pada periode ini.",
                ];
                $this->showConflictModal = true;
                $this->addError('manual_tanggal_mulai', "Gagal: Pegawai {$pegawai->nama_lengkap} tercatat memiliki Izin/Sakit aktif ({$izinBentrok->jenis}) periode {$mulai} s/d {$selesai}.");
                $this->addError('manual_tanggal', "Gagal: Pegawai {$pegawai->nama_lengkap} tercatat memiliki Izin/Sakit aktif ({$izinBentrok->jenis}) periode {$mulai} s/d {$selesai}.");
                return;
            }
        }

        // 2. Conflict Guard: Cek apakah ada pengajuan absen luar yang sudah disetujui pada rentang tanggal ini
        $absenLuarBentrok = PengajuanAbsenLuar::where('pegawai_id', $this->manual_pegawai_id)
            ->whereDate('tanggal', '>=', $this->manual_tanggal_mulai)
            ->whereDate('tanggal', '<=', $this->manual_tanggal_selesai)
            ->where('status', 'disetujui')
            ->first();

        if ($absenLuarBentrok) {
            $tglStr = Carbon::parse($absenLuarBentrok->tanggal)->isoFormat('dddd, D MMMM Y');
            $this->conflictInfo = [
                'icon'         => 'warning',
                'tipe'         => 'absen_luar_disetujui',
                'title'        => 'Sudah Ada Absen Luar',
                'badge'        => 'Pemberitahuan',
                'pegawai_nama' => $pegawai->nama_lengkap ?? 'Perangkat Desa',
                'tanggal'      => $tglStr,
                'status'       => $absenLuarBentrok->label_jenis ?? 'Dinas Luar',
                'jam_info'     => "Status: Disetujui",
                'pesan'        => "Pegawai ini sudah memiliki pengajuan Absen Luar Disetujui pada {$tglStr}.",
            ];
            $this->showConflictModal = true;
            $this->addError('manual_tanggal_mulai', "Gagal: Pegawai {$pegawai->nama_lengkap} sudah memiliki pengajuan Absen Luar yang Disetujui ({$absenLuarBentrok->judul}) pada tanggal " . Carbon::parse($absenLuarBentrok->tanggal)->format('d/m/Y') . ".");
            $this->addError('manual_tanggal', "Gagal: Pegawai {$pegawai->nama_lengkap} sudah memiliki pengajuan Absen Luar yang Disetujui.");
            return;
        }

        DB::transaction(function () use ($pegawai, $startDate, $endDate) {
            $durasiMenit = 0;
            if ($this->manual_jam_masuk && $this->manual_jam_pulang) {
                $masuk = Carbon::createFromFormat('H:i', $this->manual_jam_masuk);
                $pulang = Carbon::createFromFormat('H:i', $this->manual_jam_pulang);
                if ($pulang->greaterThan($masuk)) {
                    $durasiMenit = $masuk->diffInMinutes($pulang);
                }
            }

            // Lampirkan tanda tangan digital jika berstatus Hadir
            $ttdMasuk = null;
            if ($this->manual_status === 'Hadir') {
                $ttdMasuk = Kehadiran::where('pegawai_id', $this->manual_pegawai_id)
                    ->whereNotNull('tanda_tangan_masuk')
                    ->latest('tanggal')
                    ->value('tanda_tangan_masuk');

                if (!$ttdMasuk) {
                    $ttdMasuk = PengajuanAbsenLuar::where('pegawai_id', $this->manual_pegawai_id)
                        ->whereNotNull('tanda_tangan')
                        ->latest('tanggal')
                        ->value('tanda_tangan');
                }
            }

            // Loop seluruh tanggal dalam rentang yang diinputkan (misal Senin s/d Rabu)
            $curr = $startDate->copy();
            $totalHari = 0;
            while ($curr->lte($endDate)) {
                $dateStr = $curr->toDateString();
                $totalHari++;

                Kehadiran::updateOrCreate(
                    ['pegawai_id' => $this->manual_pegawai_id, 'tanggal' => $dateStr],
                    [
                        'jam_masuk'           => ($this->manual_status === 'Hadir' && $this->manual_jam_masuk) ? $this->manual_jam_masuk . ':00' : null,
                        'jam_pulang'          => ($this->manual_status === 'Hadir' && $this->manual_jam_pulang) ? $this->manual_jam_pulang . ':00' : null,
                        'durasi_kerja_menit'  => ($this->manual_status === 'Hadir') ? $durasiMenit : 0,
                        'status'              => $this->manual_status,
                        'sumber_data'         => 'manual_admin',
                        'tanda_tangan_masuk'  => $ttdMasuk,
                        'keterangan'          => $this->manual_keterangan,
                        'diverifikasi_oleh'   => auth()->id(),
                    ]
                );

                $curr->addDay();
            }

            // Jika status Sakit atau Izin, sinkronkan juga ke tabel izin_sakits agar data administratif sinkron rapih
            if (in_array($this->manual_status, ['Sakit', 'Izin'])) {
                $jenisIzin = ($this->manual_status === 'Sakit') ? 'sakit_tanpa_surat' : 'izin_pribadi';
                IzinSakit::updateOrCreate(
                    [
                        'pegawai_id'      => $this->manual_pegawai_id,
                        'tanggal_mulai'   => $this->manual_tanggal_mulai,
                        'tanggal_selesai' => $this->manual_tanggal_selesai,
                    ],
                    [
                        'jenis'           => $jenisIzin,
                        'jumlah_hari'     => $totalHari,
                        'keterangan'      => $this->manual_keterangan,
                        'status'          => 'disetujui',
                        'diproses_oleh'   => auth()->id(),
                    ]
                );
            }

            $periodeStr = ($this->manual_tanggal_mulai === $this->manual_tanggal_selesai)
                ? $this->manual_tanggal_mulai
                : "{$this->manual_tanggal_mulai} s/d {$this->manual_tanggal_selesai} ({$totalHari} hari)";

            AuditLog::create([
                'user_id'   => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role'      => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Presensi manual {$pegawai->nama_lengkap} tanggal {$periodeStr} ({$this->manual_status}). Alasan: {$this->manual_keterangan}",
                'modul'     => 'Presensi Manual',
            ]);

            $msg = "Presensi manual untuk {$pegawai->nama_lengkap} periode {$periodeStr} ({$this->manual_status}) berhasil disimpan ke {$totalHari} hari.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
            $this->closeManualForm();
        });
    }

    public function render()
    {
        return view('livewire.izin-manager', [
            'izins' => IzinSakit::with(['pegawai.jabatan', 'pemproses'])
                ->latest()
                ->paginate(10, ['*'], 'izin_page'),
            'overrides' => Kehadiran::with(['pegawai.jabatan', 'verifikator'])
                ->where('sumber_data', 'manual_admin')
                ->latest('tanggal')
                ->paginate(10, ['*'], 'manual_page'),
            'pegawais' => Pegawai::where('status_aktif', true)->orderBy('nama_lengkap')->get(),
        ])->layout('layouts.app', ['title' => 'Manajemen Izin & Presensi Manual — Presence Desa']);
    }
}

