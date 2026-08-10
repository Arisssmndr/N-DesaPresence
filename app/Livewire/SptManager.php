<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\SuratPerintahTugas;
use App\Models\Pegawai;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SptManager extends Component
{
    use WithPagination, WithFileUploads;

    public bool $showModal = false;
    public ?int $sptId = null;

    public ?int $pegawai_id = null;
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public string $tujuan = '';
    public string $keperluan = '';
    public float $anggaran = 0.0;
    public $file_undangan;
    public $file_bukti;

    public function mount()
    {
        $this->tanggal_mulai = Carbon::today()->toDateString();
        $this->tanggal_selesai = Carbon::today()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'anggaran' => 'nullable|numeric|min:0',
            'file_undangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function createSpt()
    {
        $this->validate();

        $undanganPath = null;
        if ($this->file_undangan) {
            $undanganPath = $this->file_undangan->store('spt-undangan', 'public');
        }

        // Auto-generate nomor SPT: SPT/BULAN/TAHUN/URUTAN
        $month = Carbon::parse($this->tanggal_mulai)->format('m');
        $year = Carbon::parse($this->tanggal_mulai)->format('Y');
        $countThisMonth = SuratPerintahTugas::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        $nomorSpt = sprintf("SPT/%s/%s/%03d", $month, $year, $countThisMonth);

        $spt = SuratPerintahTugas::create([
            'nomor_spt' => $nomorSpt,
            'pegawai_id' => $this->pegawai_id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'tujuan' => $this->tujuan,
            'keperluan' => $this->keperluan,
            'file_undangan' => $undanganPath,
            'anggaran' => $this->anggaran ?? 0,
            'status' => auth()->user()->isKades() ? 'disetujui' : 'diajukan',
            'disetujui_oleh' => auth()->user()->isKades() ? auth()->id() : null,
            'tanggal_persetujuan' => auth()->user()->isKades() ? now() : null,
            'created_by' => auth()->id(),
        ]);

        if ($spt->status === 'disetujui') {
            $this->applySptAttendance($spt);
        }

        $pegawai = Pegawai::find($this->pegawai_id);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Pengajuan SPT Baru {$spt->nomor_spt} untuk {$pegawai->nama_lengkap} (Tujuan: {$spt->tujuan})",
            'modul' => 'Surat Perintah Tugas',
        ]);

        session()->flash('success', "Surat Perintah Tugas {$spt->nomor_spt} berhasil dibuat.");
        $this->closeModal();
    }

    public function approve(int $id)
    {
        $spt = SuratPerintahTugas::findOrFail($id);
        $spt->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_persetujuan' => now(),
        ]);

        $this->applySptAttendance($spt);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Kepala Desa',
            'aktivitas' => "Menyetujui SPT {$spt->nomor_spt} untuk {$spt->pegawai->nama_lengkap}",
            'modul' => 'Surat Perintah Tugas',
        ]);

        session()->flash('success', "SPT {$spt->nomor_spt} telah disetujui Kepala Desa.");
    }

    public function reject(int $id)
    {
        $spt = SuratPerintahTugas::findOrFail($id);
        $spt->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Kepala Desa',
            'aktivitas' => "Menolak SPT {$spt->nomor_spt} untuk {$spt->pegawai->nama_lengkap}",
            'modul' => 'Surat Perintah Tugas',
        ]);

        session()->flash('success', "SPT {$spt->nomor_spt} telah ditolak.");
    }

    private function applySptAttendance(SuratPerintahTugas $spt)
    {
        $start = Carbon::parse($spt->tanggal_mulai);
        $end = Carbon::parse($spt->tanggal_selesai);

        while ($start->lte($end)) {
            Kehadiran::updateOrCreate(
                ['pegawai_id' => $spt->pegawai_id, 'tanggal' => $start->toDateString()],
                [
                    'status' => 'Dinas Luar',
                    'sumber_data' => 'fingerprint',
                    'keterangan' => "Surat Perintah Tugas: {$spt->nomor_spt} ({$spt->tujuan})"
                ]
            );
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
        $this->sptId = null;
        $this->pegawai_id = null;
        $this->tanggal_mulai = Carbon::today()->toDateString();
        $this->tanggal_selesai = Carbon::today()->toDateString();
        $this->tujuan = '';
        $this->keperluan = '';
        $this->anggaran = 0.0;
        $this->file_undangan = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.spt-manager', [
            'spts' => SuratPerintahTugas::with(['pegawai.jabatan', 'persetuju'])
                ->latest()
                ->paginate(10),
            'pegawais' => Pegawai::where('status_aktif', true)->orderBy('nama_lengkap')->get(),
        ])->layout('layouts.app', ['title' => 'Surat Perintah Tugas — Presence Desa']);
    }
}
