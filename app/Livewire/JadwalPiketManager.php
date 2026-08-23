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

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $piketId = null;

    // Form fields
    public ?int $pegawai_id = null;
    public string $tanggal_piket = '';
    public string $jam_mulai = '19:00';
    public string $jam_selesai = '06:00';
    public string $keterangan = 'Piket Jaga Malam Balai Desa';

    // Filters
    public string $search = '';
    public int $bulan;
    public int $tahun;
    public string $statusFilter = 'semua';

    public function mount()
    {
        $this->tanggal_piket = Carbon::today()->toDateString();
        $this->bulan = (int) date('m');
        $this->tahun = (int) date('Y');
    }

    protected function rules(): array
    {
        return [
            'pegawai_id'    => 'required|exists:pegawais,id',
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

        $pikets = $query->orderBy('tanggal_piket', 'desc')->paginate(10);
        $pegawais = Pegawai::where('status_aktif', true)->orderBy('nama_lengkap')->get();

        return view('livewire.jadwal-piket-manager', [
            'pikets'   => $pikets,
            'pegawais' => $pegawais,
        ])->layout('layouts.app', ['title' => 'Jadwal Piket — Presence Desa']);
    }
}
