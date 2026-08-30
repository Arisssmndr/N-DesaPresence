<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JadwalPiket;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use Carbon\Carbon;

class JadwalPiketManager extends Component
{
    use WithPagination;

    // Single modal state
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $piketId = null;

    // Form fields (Single)
    public ?int $pegawai_id = null;
    public string $tanggal_piket = '';
    public string $jam_mulai = '19:00';
    public string $jam_selesai = '06:00';
    public string $keterangan = 'Piket Jaga Malam Balai Desa';

    // Bulk Generator modal state
    public bool $showGeneratorModal = false;
    public string $generatorDurasi = '1_minggu'; // 1_minggu, 1_bulan, 6_bulan, 1_tahun, custom
    public string $generatorTanggalMulai = '';
    public string $generatorTanggalSelesai = '';
    public array $selectedStafIds = [];
    public string $generatorTipeHari = 'setiap_hari'; // setiap_hari, hari_kerja, akhir_pekan
    public string $generatorOpsiKonflik = 'skip'; // skip, replace
    public string $generatorJamMulai = '19:00';
    public string $generatorJamSelesai = '06:00';
    public string $generatorKeterangan = 'Piket Jaga Malam Balai Desa';

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
        $this->generatorTanggalMulai = Carbon::today()->toDateString();
        $this->generatorTanggalSelesai = Carbon::today()->addDays(6)->toDateString();
        $this->bulan = (int) date('m');
        $this->tahun = (int) date('Y');
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

            AuditLog::create([
                'user_id'   => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role'      => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Mengubah jadwal piket {$pegawai->nama_lengkap} tanggal {$this->tanggal_piket}",
                'modul'     => 'Jadwal Piket',
            ]);

            $msg = "Jadwal piket untuk {$pegawai->nama_lengkap} berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
        } else {
            $data['status'] = 'terjadwal';
            $data['created_by'] = auth()->id();
            $piket = JadwalPiket::create($data);

            AuditLog::create([
                'user_id'   => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role'      => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menetapkan jadwal piket baru {$pegawai->nama_lengkap} pada tanggal {$this->tanggal_piket}",
                'modul'     => 'Jadwal Piket',
            ]);

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
        $tgl = $piket->tanggal_piket->format('d/m/Y');
        $piket->delete();

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menghapus jadwal piket {$nama} tanggal {$tgl}",
            'modul'     => 'Jadwal Piket',
        ]);

        $msg = "Jadwal piket {$nama} berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

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

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menghapus secara massal {$count} jadwal piket",
            'modul'     => 'Jadwal Piket',
        ]);

        $this->selectedPiketIds = [];
        $this->selectAll = false;

        $msg = "{$count} jadwal piket berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function clearBulanIni()
    {
        $this->hapusJadwalPeriode('semua_bulan');
    }

    public function hapusJadwalPeriode(string $target)
    {
        $namaBulan = Carbon::create(null, $this->bulan)->locale('id')->isoFormat('MMMM');
        $daysInMonth = Carbon::createFromDate($this->tahun, $this->bulan, 1)->daysInMonth;

        $startDate = null;
        $endDate = null;
        $label = '';

        switch ($target) {
            case 'minggu_1':
                $startDate = Carbon::create($this->tahun, $this->bulan, 1)->toDateString();
                $endDate   = Carbon::create($this->tahun, $this->bulan, 7)->toDateString();
                $label = "Minggu ke-1 (1 - 7 {$namaBulan} {$this->tahun})";
                break;
            case 'minggu_2':
                $startDate = Carbon::create($this->tahun, $this->bulan, 8)->toDateString();
                $endDate   = Carbon::create($this->tahun, $this->bulan, 14)->toDateString();
                $label = "Minggu ke-2 (8 - 14 {$namaBulan} {$this->tahun})";
                break;
            case 'minggu_3':
                $startDate = Carbon::create($this->tahun, $this->bulan, 15)->toDateString();
                $endDate   = Carbon::create($this->tahun, $this->bulan, 21)->toDateString();
                $label = "Minggu ke-3 (15 - 21 {$namaBulan} {$this->tahun})";
                break;
            case 'minggu_4':
                $startDate = Carbon::create($this->tahun, $this->bulan, 22)->toDateString();
                $endDate   = Carbon::create($this->tahun, $this->bulan, 28)->toDateString();
                $label = "Minggu ke-4 (22 - 28 {$namaBulan} {$this->tahun})";
                break;
            case 'minggu_5':
                if ($daysInMonth >= 29) {
                    $startDate = Carbon::create($this->tahun, $this->bulan, 29)->toDateString();
                    $endDate   = Carbon::create($this->tahun, $this->bulan, $daysInMonth)->toDateString();
                    $label = "Minggu ke-5 (29 - {$daysInMonth} {$namaBulan} {$this->tahun})";
                }
                break;
            case 'semua_bulan':
            default:
                $startDate = Carbon::create($this->tahun, $this->bulan, 1)->toDateString();
                $endDate   = Carbon::create($this->tahun, $this->bulan, $daysInMonth)->toDateString();
                $label = "Seluruh Bulan {$namaBulan} {$this->tahun}";
                break;
        }

        if (!$startDate || !$endDate) {
            $this->dispatch('notify', message: 'Periode tidak valid.', type: 'warning');
            return;
        }

        $query = JadwalPiket::whereDate('tanggal_piket', '>=', $startDate)
            ->whereDate('tanggal_piket', '<=', $endDate);

        $count = $query->count();

        if ($count === 0) {
            $this->dispatch('notify', message: "Tidak ada jadwal piket pada {$label}.", type: 'info');
            return;
        }

        $query->delete();

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menghapus {$count} jadwal piket periode {$label}",
            'modul'     => 'Jadwal Piket',
        ]);

        $this->selectedPiketIds = [];
        $this->selectAll = false;

        $msg = "Jadwal piket pada {$label} ({$count} data) berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function verifikasiHadir(int $id)
    {
        $piket = JadwalPiket::findOrFail($id);
        $piket->update([
            'status'      => 'hadir',
            'waktu_absen' => $piket->waktu_absen ?? now(),
        ]);

        // Otomatis masukkan status "Lepas Piket" ke presensi hari berikutnya
        $besokStr = Carbon::parse($piket->tanggal_piket)->addDay()->toDateString();
        
        $kehadiranBesok = Kehadiran::firstOrNew([
            'pegawai_id' => $piket->pegawai_id,
            'tanggal'    => $besokStr,
        ]);

        $kehadiranBesok->status             = 'Hadir';
        $kehadiranBesok->jam_masuk          = $piket->waktu_absen ? $piket->waktu_absen->format('H:i:s') : '07:30:00';
        $kehadiranBesok->tanda_tangan_masuk = $piket->tanda_tangan;
        $kehadiranBesok->sumber_data        = 'manual_admin';
        $kehadiranBesok->diverifikasi_oleh  = auth()->id();
        $kehadiranBesok->keterangan         = "Lepas Piket (Tugas Piket Malam tgl " . $piket->tanggal_piket->format('d/m/Y') . ")";
        $kehadiranBesok->save();

        $msg = "Piket {$piket->pegawai->nama_lengkap} dikonfirmasi hadir. Presensi Lepas Piket hari berikutnya ({$besokStr}) otomatis dicatat.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
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

    // ─── GENERATOR MODAL LOGIC ──────────────────────────────────────

    public function openGeneratorModal()
    {
        $this->generatorDurasi = '1_minggu';
        $this->generatorTanggalMulai = Carbon::today()->toDateString();
        $this->recalculateGeneratorEndDate();
        $this->selectedStafIds = Pegawai::where('status_aktif', true)
            ->where('jenis_kelamin', 'L')
            ->orderBy('nama_lengkap')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();
        $this->generatorTipeHari = 'setiap_hari';
        $this->generatorOpsiKonflik = 'skip';
        $this->generatorJamMulai = '19:00';
        $this->generatorJamSelesai = '06:00';
        $this->generatorKeterangan = 'Piket Jaga Malam Balai Desa';

        $this->showGeneratorModal = true;
    }

    public function closeGeneratorModal()
    {
        $this->showGeneratorModal = false;
        $this->resetValidation();
    }

    public function updatedGeneratorDurasi()
    {
        $this->recalculateGeneratorEndDate();
    }

    public function updatedGeneratorTanggalMulai()
    {
        $this->recalculateGeneratorEndDate();
    }

    private function recalculateGeneratorEndDate()
    {
        if (!$this->generatorTanggalMulai) {
            $this->generatorTanggalMulai = Carbon::today()->toDateString();
        }

        $start = Carbon::parse($this->generatorTanggalMulai);

        switch ($this->generatorDurasi) {
            case '1_minggu':
                $this->generatorTanggalSelesai = $start->copy()->addDays(6)->toDateString();
                break;
            case '1_bulan':
                $this->generatorTanggalSelesai = $start->copy()->addMonth()->subDay()->toDateString();
                break;
            case '6_bulan':
                $this->generatorTanggalSelesai = $start->copy()->addMonths(6)->subDay()->toDateString();
                break;
            case '1_tahun':
                $this->generatorTanggalSelesai = $start->copy()->addYear()->subDay()->toDateString();
                break;
            case 'custom':
                if (!$this->generatorTanggalSelesai || $this->generatorTanggalSelesai < $this->generatorTanggalMulai) {
                    $this->generatorTanggalSelesai = $start->copy()->addDays(6)->toDateString();
                }
                break;
        }
    }

    public function toggleSelectAllStaf()
    {
        $allIds = Pegawai::where('status_aktif', true)
            ->where('jenis_kelamin', 'L')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        if (count($this->selectedStafIds) === count($allIds)) {
            $this->selectedStafIds = [];
        } else {
            $this->selectedStafIds = $allIds;
        }
    }

    public function generateJadwalBulk()
    {
        $this->validate([
            'generatorTanggalMulai'   => 'required|date',
            'generatorTanggalSelesai' => 'required|date|after_or_equal:generatorTanggalMulai',
            'selectedStafIds'         => 'required|array|min:1',
            'selectedStafIds.*'       => 'exists:pegawais,id',
            'generatorJamMulai'       => 'required|string',
            'generatorJamSelesai'     => 'required|string',
            'generatorKeterangan'     => 'required|string|max:255',
        ], [
            'selectedStafIds.required' => 'Pilih minimal satu staf laki-laki untuk rotasi jadwal piket.',
            'selectedStafIds.min'      => 'Pilih minimal satu staf laki-laki untuk rotasi jadwal piket.',
            'generatorTanggalSelesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        // Ambil ID staf laki-laki yang valid & aktif sesuai urutan
        $validStafIds = Pegawai::whereIn('id', $this->selectedStafIds)
            ->where('status_aktif', true)
            ->where('jenis_kelamin', 'L')
            ->orderBy('nama_lengkap')
            ->pluck('id')
            ->toArray();

        if (empty($validStafIds)) {
            $this->addError('selectedStafIds', 'Tidak ada staf laki-laki aktif yang valid terpilih.');
            return;
        }

        $start = Carbon::parse($this->generatorTanggalMulai);
        $end   = Carbon::parse($this->generatorTanggalSelesai);

        $current = $start->copy();
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;
        $stafIndex = 0;
        $stafCount = count($validStafIds);

        while ($current->lte($end)) {
            $dayOfWeek = $current->dayOfWeek; // 0 = Sunday, 6 = Saturday

            // Filter tipe hari
            $shouldSchedule = true;
            if ($this->generatorTipeHari === 'hari_kerja' && ($dayOfWeek === 0 || $dayOfWeek === 6)) {
                $shouldSchedule = false;
            } elseif ($this->generatorTipeHari === 'akhir_pekan' && ($dayOfWeek !== 0 && $dayOfWeek !== 6)) {
                $shouldSchedule = false;
            }

            if ($shouldSchedule) {
                $pegawaiId = $validStafIds[$stafIndex % $stafCount];
                $dateStr = $current->toDateString();

                $existing = JadwalPiket::whereDate('tanggal_piket', $dateStr)->first();

                if ($existing) {
                    if ($this->generatorOpsiKonflik === 'replace') {
                        $existing->update([
                            'pegawai_id'    => $pegawaiId,
                            'jam_mulai'     => $this->generatorJamMulai . (strlen($this->generatorJamMulai) === 5 ? ':00' : ''),
                            'jam_selesai'   => $this->generatorJamSelesai . (strlen($this->generatorJamSelesai) === 5 ? ':00' : ''),
                            'keterangan'    => $this->generatorKeterangan,
                        ]);
                        $totalUpdated++;
                    } else {
                        $totalSkipped++;
                    }
                } else {
                    JadwalPiket::create([
                        'pegawai_id'    => $pegawaiId,
                        'tanggal_piket' => $dateStr,
                        'jam_mulai'     => $this->generatorJamMulai . (strlen($this->generatorJamMulai) === 5 ? ':00' : ''),
                        'jam_selesai'   => $this->generatorJamSelesai . (strlen($this->generatorJamSelesai) === 5 ? ':00' : ''),
                        'keterangan'    => $this->generatorKeterangan,
                        'status'        => 'terjadwal',
                        'created_by'    => auth()->id(),
                    ]);
                    $totalCreated++;
                }

                $stafIndex++;
            }

            $current->addDay();
        }

        // Set bulan & tahun filter sesuai tanggal mulai agar langsung terlihat
        $this->bulan = (int) $start->format('m');
        $this->tahun = (int) $start->format('Y');

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Generate jadwal piket otomatis ({$this->generatorDurasi}) {$start->format('d/m/Y')} s/d {$end->format('d/m/Y')}: {$totalCreated} baru, {$totalUpdated} diperbarui, {$totalSkipped} dilewati",
            'modul'     => 'Jadwal Piket',
        ]);

        $msg = "Penjadwalan piket otomatis berhasil! Dibuat: {$totalCreated} baru, Diperbarui: {$totalUpdated}, Dilewati: {$totalSkipped}.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');

        $this->closeGeneratorModal();
    }

    public function generateDummyMingguan()
    {
        $malePegawais = Pegawai::where('status_aktif', true)
            ->where('jenis_kelamin', 'L')
            ->orderBy('nama_lengkap')
            ->get();

        if ($malePegawais->isEmpty()) {
            $this->dispatch('notify', message: 'Tidak ada data staf laki-laki aktif untuk membuat data dummy.', type: 'warning');
            return;
        }

        $startDate = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $totalCreated = 0;
        $stafCount = $malePegawais->count();

        for ($i = 0; $i < 7; $i++) {
            $tgl = $startDate->copy()->addDays($i);
            $pegawai = $malePegawais[$i % $stafCount];

            JadwalPiket::updateOrCreate(
                ['tanggal_piket' => $tgl->toDateString()],
                [
                    'pegawai_id'  => $pegawai->id,
                    'jam_mulai'   => '19:00:00',
                    'jam_selesai' => '06:00:00',
                    'keterangan'  => 'Piket Jaga Malam Balai Desa',
                    'status'      => 'terjadwal',
                    'created_by'  => auth()->id() ?? 1,
                ]
            );
            $totalCreated++;
        }

        $this->bulan = (int) $startDate->format('m');
        $this->tahun = (int) $startDate->format('Y');

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Mengisi data dummy jadwal piket mingguan (7 hari)",
            'modul'     => 'Jadwal Piket',
        ]);

        $msg = "Data dummy jadwal piket 1 minggu ({$startDate->format('d/m/Y')} s/d {$startDate->copy()->addDays(6)->format('d/m/Y')}) berhasil dibuat untuk staf laki-laki!";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
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
