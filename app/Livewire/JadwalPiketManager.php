<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JadwalPiket;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JadwalPiketManager extends Component
{
    use WithPagination;

    // Single modal state (Tambah / Edit Satuan)
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $piketId = null;

    // Form fields (Satuan)
    public ?int $pegawai_id = null;
    public string $tanggal_piket = '';
    public string $jam_mulai = '19:00';
    public string $jam_selesai = '06:00';
    public string $keterangan = 'Piket Jaga Malam Balai Desa';

    // Modal Pola Jadwal Desa (Masa Aktif Otomatis)
    public bool $showPolaModal = false;
    public string $polaDurasi = '1_tahun'; // 1_bulan, 3_bulan, 6_bulan, 1_tahun, custom
    public string $polaTanggalMulai = '';
    public string $polaTanggalSelesai = '';
    public string $polaJamMulai = '19:00';
    public string $polaJamSelesai = '06:00';
    public string $polaKeterangan = 'Piket Jaga Malam Balai Desa';
    public string $polaOpsiKonflik = 'replace'; // replace, skip
    public array $polaHariStaf = [
        1 => [], // Senin
        2 => [], // Selasa
        3 => [], // Rabu
        4 => [], // Kamis
        5 => [], // Jumat
        6 => [], // Sabtu
        0 => [], // Minggu
    ];

    // Bulk Selection in Table
    public array $selectedPiketIds = [];
    public bool $selectAll = false;

    // Filters
    public string $search = '';
    public int $bulan;
    public int $tahun;
    public string $statusFilter = 'semua';

    public function mount()
    {
        $this->tanggal_piket = Carbon::today()->toDateString();
        $this->polaTanggalMulai = Carbon::today()->startOfYear()->toDateString();
        $this->bulan = (int) date('m');
        $this->tahun = (int) date('Y');
        $this->recalculatePolaEndDate();
        $this->polaHariStaf = $this->getDefaultPolaDesaMapping();
    }

    protected function rules(): array
    {
        return [
            'pegawai_id' => [
                'required',
                'exists:pegawais,id',
                function ($attribute, $value, $fail) {
                    $pegawai = Pegawai::find($value);
                    if ($pegawai && $pegawai->jenis_kelamin !== 'L') {
                        $fail('Jadwal piket hanya dapat ditetapkan untuk staf berjenis kelamin laki-laki.');
                    }
                },
            ],
            'tanggal_piket' => 'required|date',
            'jam_mulai'     => 'required|string',
            'jam_selesai'   => 'required|string',
            'keterangan'    => 'required|string|max:255',
        ];
    }

    /**
     * Mapping baku staf desa per hari
     */
    public function getDefaultPolaDesaMapping(): array
    {
        $findId = function(string $name) {
            return Pegawai::where('nama_lengkap', 'like', "%{$name}%")->value('id');
        };

        $dedeSumirna = $findId('DEDE SUMIRNA');
        $rukanda     = $findId('RUKANDA');
        $yayan       = $findId('YAYAN TARYANA');
        $dedeLisman  = $findId('DEDE LISMAN');
        $apip        = $findId('APIP MANSUR');
        $dedi        = $findId('DEDI SUHERMAN');
        $abun        = $findId('ABUN SUPARMAN');
        $heri        = $findId('HERI GINANJAR');
        $zailani     = $findId('ZAILANI RAHMAT');

        return [
            1 => array_map('strval', array_values(array_filter([$dedeSumirna, $rukanda]))), // Senin: Dede Sumirna & Rukanda
            2 => array_map('strval', array_values(array_filter([$yayan, $dedeLisman]))),    // Selasa: Yayan Taryana & Dede Lisman
            3 => array_map('strval', array_values(array_filter([$apip]))),                  // Rabu: Apip Mansur
            4 => array_map('strval', array_values(array_filter([$dedi]))),                  // Kamis: Dedi Suherman
            5 => array_map('strval', array_values(array_filter([$abun]))),                  // Jumat: Abun Suparman
            6 => array_map('strval', array_values(array_filter([$heri]))),                  // Sabtu: Heri Ginanjar
            0 => array_map('strval', array_values(array_filter([$zailani]))),               // Minggu: Zailani Rahmat
        ];
    }

    // ─── POLA JADWAL DESA (UTAMA) ────────────────────────────────────

    public function openPolaModal()
    {
        $this->polaDurasi = '1_tahun';
        $this->polaTanggalMulai = Carbon::today()->startOfYear()->toDateString();
        $this->recalculatePolaEndDate();
        $this->polaHariStaf = $this->getDefaultPolaDesaMapping();
        $this->polaJamMulai = '19:00';
        $this->polaJamSelesai = '06:00';
        $this->polaKeterangan = 'Piket Jaga Malam Balai Desa';
        $this->polaOpsiKonflik = 'replace';

        $this->showPolaModal = true;
    }

    public function closePolaModal()
    {
        $this->showPolaModal = false;
        $this->resetValidation();
    }

    public function updatedPolaDurasi()
    {
        $this->recalculatePolaEndDate();
    }

    public function updatedPolaTanggalMulai()
    {
        $this->recalculatePolaEndDate();
    }

    private function recalculatePolaEndDate()
    {
        if (!$this->polaTanggalMulai) {
            $this->polaTanggalMulai = Carbon::today()->toDateString();
        }

        $start = Carbon::parse($this->polaTanggalMulai);

        switch ($this->polaDurasi) {
            case '1_bulan':
                $this->polaTanggalSelesai = $start->copy()->addMonth()->subDay()->toDateString();
                break;
            case '3_bulan':
                $this->polaTanggalSelesai = $start->copy()->addMonths(3)->subDay()->toDateString();
                break;
            case '6_bulan':
                $this->polaTanggalSelesai = $start->copy()->addMonths(6)->subDay()->toDateString();
                break;
            case '1_tahun':
                $this->polaTanggalSelesai = $start->copy()->addYear()->subDay()->toDateString();
                break;
            case 'custom':
                if (!$this->polaTanggalSelesai || $this->polaTanggalSelesai < $this->polaTanggalMulai) {
                    $this->polaTanggalSelesai = $start->copy()->addYear()->subDay()->toDateString();
                }
                break;
        }
    }

    public function resetPolaKeDefaultDesa()
    {
        $this->polaHariStaf = $this->getDefaultPolaDesaMapping();
        $this->dispatch('notify', message: 'Susunan jadwal direset sesuai jadwal resmi desa.', type: 'info');
    }

    public function generatePolaJadwalDesa()
    {
        $this->validate([
            'polaTanggalMulai'   => 'required|date',
            'polaTanggalSelesai' => 'required|date|after_or_equal:polaTanggalMulai',
            'polaJamMulai'       => 'required|string',
            'polaJamSelesai'     => 'required|string',
            'polaKeterangan'     => 'required|string|max:255',
        ], [
            'polaTanggalSelesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $totalStafTerjadwal = 0;
        foreach ($this->polaHariStaf as $stafList) {
            $totalStafTerjadwal += count($stafList);
        }

        if ($totalStafTerjadwal === 0) {
            $this->dispatch('notify', message: 'Pilih minimal satu staf pada salah satu hari.', type: 'warning');
            return;
        }

        $start = Carbon::parse($this->polaTanggalMulai);
        $end   = Carbon::parse($this->polaTanggalSelesai);

        $current = $start->copy();
        $totalCreated = 0;
        $totalUpdated = 0;

        while ($current->lte($end)) {
            $dayOfWeek = $current->dayOfWeek;
            $assignedStafIds = $this->polaHariStaf[$dayOfWeek] ?? [];
            $dateStr = $current->toDateString();

            if (!empty($assignedStafIds)) {
                foreach ($assignedStafIds as $stafId) {
                    $stafId = (int) $stafId;
                    if ($stafId <= 0) continue;

                    $existing = JadwalPiket::where('pegawai_id', $stafId)
                        ->whereDate('tanggal_piket', $dateStr)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'jam_mulai'     => $this->polaJamMulai . (strlen($this->polaJamMulai) === 5 ? ':00' : ''),
                            'jam_selesai'   => $this->polaJamSelesai . (strlen($this->polaJamSelesai) === 5 ? ':00' : ''),
                            'keterangan'    => $this->polaKeterangan,
                        ]);
                        $totalUpdated++;
                    } else {
                        JadwalPiket::create([
                            'pegawai_id'    => $stafId,
                            'tanggal_piket' => $dateStr,
                            'jam_mulai'     => $this->polaJamMulai . (strlen($this->polaJamMulai) === 5 ? ':00' : ''),
                            'jam_selesai'   => $this->polaJamSelesai . (strlen($this->polaJamSelesai) === 5 ? ':00' : ''),
                            'keterangan'    => $this->polaKeterangan,
                            'status'        => 'terjadwal',
                            'created_by'    => Auth::id(),
                        ]);
                        $totalCreated++;
                    }
                }
            }

            $current->addDay();
        }

        $this->bulan = (int) $start->format('m');
        $this->tahun = (int) $start->format('Y');

        AuditLog::create([
            'user_id'   => Auth::id(),
            'user_name' => Auth::user()->name ?? 'Admin',
            'role'      => Auth::user()->role ?? 'Admin',
            'aktivitas' => "Menerapkan jadwal piket desa ({$this->polaDurasi}) {$start->format('d/m/Y')} s/d {$end->format('d/m/Y')}",
            'modul'     => 'Jadwal Piket',
        ]);

        $msg = "Jadwal piket berhasil diterapkan! Total: {$totalCreated} baru, {$totalUpdated} diperbarui.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');

        $this->closePolaModal();
    }

    public function generateDummyMingguan()
    {
        $pola = $this->getDefaultPolaDesaMapping();
        $startDate = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $totalCreated = 0;

        for ($i = 0; $i < 7; $i++) {
            $tgl = $startDate->copy()->addDays($i);
            $dayOfWeek = $tgl->dayOfWeek;
            $stafIds = $pola[$dayOfWeek] ?? [];

            foreach ($stafIds as $stafId) {
                JadwalPiket::updateOrCreate(
                    [
                        'pegawai_id'    => (int) $stafId,
                        'tanggal_piket' => $tgl->toDateString()
                    ],
                    [
                        'jam_mulai'   => '19:00:00',
                        'jam_selesai' => '06:00:00',
                        'keterangan'  => 'Piket Jaga Malam Balai Desa',
                        'status'      => 'terjadwal',
                        'created_by'  => Auth::id() ?? 1,
                    ]
                );
                $totalCreated++;
            }
        }

        $this->bulan = (int) $startDate->format('m');
        $this->tahun = (int) $startDate->format('Y');

        $msg = "Jadwal piket 1 minggu berhasil diterapkan sesuai jadwal desa!";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    // ─── CRUD SATUAN ─────────────────────────────────────────────────

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetForm();
        $this->isEdit = true;
        $this->piketId = $id;

        $p = JadwalPiket::findOrFail($id);
        $this->pegawai_id = $p->pegawai_id;
        $this->tanggal_piket = $p->tanggal_piket->format('Y-m-d');
        $this->jam_mulai = substr($p->jam_mulai, 0, 5);
        $this->jam_selesai = substr($p->jam_selesai, 0, 5);
        $this->keterangan = $p->keterangan;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'pegawai_id'    => $this->pegawai_id,
            'tanggal_piket' => $this->tanggal_piket,
            'jam_mulai'     => $this->jam_mulai . (strlen($this->jam_mulai) === 5 ? ':00' : ''),
            'jam_selesai'   => $this->jam_selesai . (strlen($this->jam_selesai) === 5 ? ':00' : ''),
            'keterangan'    => $this->keterangan,
        ];

        $pegawai = Pegawai::find($this->pegawai_id);

        if ($this->isEdit && $this->piketId) {
            $piket = JadwalPiket::findOrFail($this->piketId);
            $piket->update($data);

            $msg = "Jadwal piket untuk {$pegawai->nama_lengkap} berhasil diubah.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
        } else {
            $data['status'] = 'terjadwal';
            $data['created_by'] = Auth::id();
            JadwalPiket::create($data);

            $msg = "Jadwal piket untuk {$pegawai->nama_lengkap} berhasil ditambahkan.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
        }

        $this->closeModal();
    }

    public function delete(int $id)
    {
        $piket = JadwalPiket::findOrFail($id);
        $nama = $piket->pegawai->nama_lengkap ?? 'Perangkat';
        $piket->delete();

        $msg = "Jadwal piket {$nama} berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->piketId = null;
        $this->pegawai_id = null;
        $this->tanggal_piket = Carbon::today()->toDateString();
        $this->jam_mulai = '19:00';
        $this->jam_selesai = '06:00';
        $this->keterangan = 'Piket Jaga Malam Balai Desa';
        $this->resetValidation();
    }

    // ─── VERIFIKASI KEHADIRAN ────────────────────────────────────────

    public function verifikasiHadir(int $id)
    {
        $piket = JadwalPiket::findOrFail($id);
        $piket->update([
            'status'      => 'sedang_piket',
            'waktu_absen' => $piket->waktu_absen ?? now(),
        ]);

        $msg = "Piket {$piket->pegawai->nama_lengkap} diverifikasi masuk.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function verifikasiPulang(int $id)
    {
        $piket = JadwalPiket::findOrFail($id);
        $piket->update([
            'status'       => 'hadir',
            'waktu_absen'  => $piket->waktu_absen ?? now(),
            'waktu_pulang' => $piket->waktu_pulang ?? now(),
        ]);

        // Otomatis catat Lepas Piket di presensi hari berikutnya
        $tglLepasPiket = $piket->waktu_selesai_datetime->toDateString();
        
        $kehadiranLepasPiket = Kehadiran::firstOrNew([
            'pegawai_id' => $piket->pegawai_id,
            'tanggal'    => $tglLepasPiket,
        ]);

        $kehadiranLepasPiket->status              = 'Hadir';
        $kehadiranLepasPiket->jam_masuk           = $piket->waktu_absen ? $piket->waktu_absen->format('H:i:s') : '07:30:00';
        $kehadiranLepasPiket->jam_pulang          = $piket->waktu_pulang ? $piket->waktu_pulang->format('H:i:s') : now()->format('H:i:s');
        $kehadiranLepasPiket->tanda_tangan_masuk  = $piket->tanda_tangan;
        $kehadiranLepasPiket->tanda_tangan_pulang = $piket->tanda_tangan_pulang;
        $kehadiranLepasPiket->sumber_data         = 'manual_admin';
        $kehadiranLepasPiket->diverifikasi_oleh   = Auth::id();
        $kehadiranLepasPiket->keterangan          = "Lepas Piket (Tugas Piket Malam tgl " . $piket->tanggal_piket->format('d/m/Y') . ")";
        $kehadiranLepasPiket->save();

        $msg = "Piket {$piket->pegawai->nama_lengkap} diverifikasi selesai & pulang. Presensi Hadir (Lepas Piket) tanggal {$tglLepasPiket} berhasil dicatat!";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    // ─── BULK ACTIONS TABLE ──────────────────────────────────────────

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPiketIds = JadwalPiket::whereYear('tanggal_piket', $this->tahun)
                ->whereMonth('tanggal_piket', $this->bulan)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedPiketIds = [];
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selectedPiketIds)) {
            $this->dispatch('notify', message: 'Pilih minimal satu jadwal piket untuk dihapus.', type: 'warning');
            return;
        }

        $count = count($this->selectedPiketIds);
        JadwalPiket::whereIn('id', $this->selectedPiketIds)->delete();

        $this->selectedPiketIds = [];
        $this->selectAll = false;

        $msg = "{$count} jadwal piket berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function kosongkanBulanIni()
    {
        $namaBulan = Carbon::create(null, $this->bulan)->locale('id')->isoFormat('MMMM');
        $count = JadwalPiket::whereYear('tanggal_piket', $this->tahun)
            ->whereMonth('tanggal_piket', $this->bulan)
            ->delete();

        $this->selectedPiketIds = [];
        $this->selectAll = false;

        $msg = "Semua jadwal piket bulan {$namaBulan} {$this->tahun} ({$count} data) telah dikosongkan.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function render()
    {
        $query = JadwalPiket::with(['pegawai.jabatan', 'pembuat'])
            ->whereYear('tanggal_piket', $this->tahun)
            ->whereMonth('tanggal_piket', $this->bulan);

        if ($this->statusFilter !== 'semua') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->whereHas('pegawai', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nipd', 'like', '%' . $this->search . '%');
            })->orWhere('keterangan', 'like', '%' . $this->search . '%');
        }

        $pikets = $query->orderBy('tanggal_piket', 'asc')->paginate(15);
        $pegawais = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->where('jenis_kelamin', 'L')
            ->orderBy('nama_lengkap')
            ->get();

        return view('livewire.jadwal-piket-manager', [
            'pikets'   => $pikets,
            'pegawais' => $pegawais,
        ])->layout('layouts.app', ['title' => 'Jadwal Piket — Presence Desa']);
    }
}
