<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use Carbon\Carbon;

class ManualAttendanceOverride extends Component
{
    use WithPagination;

    public ?int $pegawai_id = null;
    public string $tanggal = '';
    public string $jam_masuk = '08:00';
    public string $jam_pulang = '15:30';
    public string $status = 'Hadir';
    public string $keterangan = '';

    public function mount()
    {
        $this->tanggal = Carbon::today()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:Hadir,Alpa,Izin,Sakit',
            'keterangan' => 'required|string|min:5|max:255',
        ];
    }

    public function saveOverride()
    {
        $this->validate();

        $pegawai = Pegawai::findOrFail($this->pegawai_id);

        $durasiMenit = 0;
        if ($this->jam_masuk && $this->jam_pulang) {
            $masuk = Carbon::createFromFormat('H:i', $this->jam_masuk);
            $pulang = Carbon::createFromFormat('H:i', $this->jam_pulang);
            if ($pulang->greaterThan($masuk)) {
                $durasiMenit = $masuk->diffInMinutes($pulang);
            }
        }

        // Jika override berstatus Hadir, lampirkan sampel TTD digital pegawai jika tersedia
        $ttdMasuk = null;
        if ($this->status === 'Hadir') {
            $ttdMasuk = Kehadiran::where('pegawai_id', $this->pegawai_id)
                ->whereNotNull('tanda_tangan_masuk')
                ->latest('tanggal')
                ->value('tanda_tangan_masuk');

            if (!$ttdMasuk) {
                $ttdMasuk = \App\Models\PengajuanAbsenLuar::where('pegawai_id', $this->pegawai_id)
                    ->whereNotNull('tanda_tangan')
                    ->latest('tanggal')
                    ->value('tanda_tangan');
            }
        }

        $kehadiran = Kehadiran::updateOrCreate(
            ['pegawai_id' => $this->pegawai_id, 'tanggal' => $this->tanggal],
            [
                'jam_masuk'           => $this->jam_masuk ? $this->jam_masuk . ':00' : null,
                'jam_pulang'          => $this->jam_pulang ? $this->jam_pulang . ':00' : null,
                'durasi_kerja_menit'  => $durasiMenit,
                'status'              => $this->status,
                'sumber_data'         => 'manual_admin',
                'tanda_tangan_masuk'  => $ttdMasuk,
                'keterangan'          => $this->keterangan,
                'diverifikasi_oleh'   => auth()->id(),
            ]
        );

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Override presensi manual {$pegawai->nama_lengkap} tanggal {$this->tanggal} ({$this->status}). Alasan: {$this->keterangan}",
            'modul' => 'Override Absensi',
        ]);

        $msg = "Override presensi untuk {$pegawai->nama_lengkap} tanggal {$this->tanggal} berhasil disimpan.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
        $this->reset(['pegawai_id', 'keterangan']);
    }

    public function render()
    {
        return view('livewire.manual-attendance-override', [
            'pegawais' => Pegawai::where('status_aktif', true)->orderBy('nama_lengkap')->get(),
            'overrides' => Kehadiran::with(['pegawai.jabatan', 'verifikator'])
                ->where('sumber_data', 'manual_admin')
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app', ['title' => 'Override Presensi — Presence Desa']);
    }
}
