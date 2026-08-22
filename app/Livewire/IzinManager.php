<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\IzinSakit;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use Carbon\Carbon;

class IzinManager extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showModal = false;

    public ?int $pegawai_id = null;
    public string $jenis = 'izin_pribadi';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public string $keterangan = '';
    public $file_lampiran;

    public function mount()
    {
        $this->tanggal_mulai = Carbon::today()->toDateString();
        $this->tanggal_selesai = Carbon::today()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis' => 'required|in:izin_pribadi,izin_kedinasan,sakit_dengan_surat,sakit_tanpa_surat,cuti_tahunan,duka_cita,melahirkan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function createIzin()
    {
        $this->validate();

        // Cek tumpang tindih izin aktif
        $adaIzinBentrok = IzinSakit::where('pegawai_id', $this->pegawai_id)
            ->where('status', '!=', 'ditolak')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggal_mulai, $this->tanggal_selesai])
                  ->orWhereBetween('tanggal_selesai', [$this->tanggal_mulai, $this->tanggal_selesai])
                  ->orWhere(function ($sub) {
                      $sub->where('tanggal_mulai', '<=', $this->tanggal_mulai)
                          ->where('tanggal_selesai', '>=', $this->tanggal_selesai);
                  });
            })->exists();

        if ($adaIzinBentrok) {
            $this->addError('tanggal_mulai', 'Pegawai sudah memiliki pengajuan izin/sakit yang aktif pada rentang tanggal tersebut.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () {
            $lampiranPath = null;
            if ($this->file_lampiran) {
                $lampiranPath = $this->file_lampiran->store('izin-lampiran', 'public');
            }

            $start = Carbon::parse($this->tanggal_mulai);
            $end = Carbon::parse($this->tanggal_selesai);
            $jumlahHari = $start->diffInDays($end) + 1;

            $isAutoApproved = (auth()->user()->isAdmin() || auth()->user()->isKades());

            $izin = IzinSakit::create([
                'pegawai_id' => $this->pegawai_id,
                'jenis' => $this->jenis,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'jumlah_hari' => $jumlahHari,
                'keterangan' => $this->keterangan,
                'file_lampiran' => $lampiranPath,
                'status' => $isAutoApproved ? 'disetujui' : 'menunggu',
                'diproses_oleh' => $isAutoApproved ? auth()->id() : null,
            ]);

            if ($izin->status === 'disetujui') {
                $this->applyIzinAttendance($izin);
            }

            $pegawai = Pegawai::find($this->pegawai_id);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'User',
                'role' => auth()->user()->role ?? 'Perangkat',
                'aktivitas' => "Pengajuan Izin/Sakit {$this->jenis} untuk {$pegawai->nama_lengkap} ({$jumlahHari} hari)",
                'modul' => 'Izin & Sakit',
            ]);

            $msg = "Pengajuan Izin/Sakit berhasil disimpan.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
            $this->dispatch('refresh-notifications');
            $this->closeModal();
        });
    }

    public function approve(int $id)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
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

    public function reject(int $id)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $izin = IzinSakit::where('id', $id)->lockForUpdate()->firstOrFail();
            $izin->update([
                'status' => 'ditolak',
                'diproses_oleh' => auth()->id(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menolak izin {$izin->jenis} untuk {$izin->pegawai->nama_lengkap}",
                'modul' => 'Izin & Sakit',
            ]);

            $msg = "Pengajuan Izin/Sakit telah ditolak.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'info');
            $this->dispatch('refresh-notifications');
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
                // Hanya update jika belum ada presensi langsung di kantor
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
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->pegawai_id = null;
        $this->jenis = 'izin_pribadi';
        $this->tanggal_mulai = Carbon::today()->toDateString();
        $this->tanggal_selesai = Carbon::today()->toDateString();
        $this->keterangan = '';
        $this->file_lampiran = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.izin-manager', [
            'izins' => IzinSakit::with(['pegawai.jabatan', 'pemproses'])
                ->latest()
                ->paginate(10),
            'pegawais' => Pegawai::where('status_aktif', true)->orderBy('nama_lengkap')->get(),
        ])->layout('layouts.app', ['title' => 'Izin & Sakit — Presence Desa']);
    }
}
