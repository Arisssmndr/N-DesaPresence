<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanAbsenLuar;
use App\Models\Kehadiran;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengajuanAbsenManager extends Component
{
    use WithPagination;

    // ─── Filters ─────────────────────────────────────────────────────────────
    public string $filterStatus  = 'menunggu';
    public string $filterJenis   = '';
    public string $filterCari    = '';
    public string $filterTanggal = '';

    // ─── Modal Detail ─────────────────────────────────────────────────────────
    public bool $showModal        = false;
    public ?int $selectedId       = null;
    public ?PengajuanAbsenLuar $selected = null;

    // ─── Modal Approve/Reject ─────────────────────────────────────────────────
    public bool   $showApproveModal  = false;
    public bool   $showRejectModal   = false;
    public string $catatanAdmin      = '';
    public ?int   $actionTargetId    = null;

    protected $paginationTheme = 'tailwind';

    public function updatingFilterStatus()  { $this->resetPage(); }
    public function updatingFilterJenis()   { $this->resetPage(); }
    public function updatingFilterCari()    { $this->resetPage(); }
    public function updatingFilterTanggal() { $this->resetPage(); }

    // ─── Buka Modal Detail ────────────────────────────────────────────────────
    public function lihatDetail(int $id): void
    {
        $this->selectedId = $id;
        $this->selected   = PengajuanAbsenLuar::with(['pegawai.jabatan', 'user', 'diprosesoleh'])->findOrFail($id);
        $this->showModal  = true;
    }

    public function tutupModal(): void
    {
        $this->showModal        = false;
        $this->showApproveModal = false;
        $this->showRejectModal  = false;
        $this->selected         = null;
        $this->selectedId       = null;
        $this->catatanAdmin     = '';
        $this->actionTargetId   = null;
    }

    // ─── Buka Konfirmasi Approve ──────────────────────────────────────────────
    public function konfirmasiSetujui(int $id): void
    {
        $this->actionTargetId   = $id;
        $this->selected         = PengajuanAbsenLuar::with(['pegawai.jabatan'])->findOrFail($id);
        $this->catatanAdmin     = '';
        $this->showModal        = false;
        $this->showApproveModal = true;
    }

    // ─── Buka Konfirmasi Reject ───────────────────────────────────────────────
    public function konfirmasiTolak(int $id): void
    {
        $this->actionTargetId  = $id;
        $this->selected        = PengajuanAbsenLuar::with(['pegawai.jabatan'])->findOrFail($id);
        $this->catatanAdmin    = '';
        $this->showModal       = false;
        $this->showRejectModal = true;
    }

    // ─── Eksekusi Setujui ─────────────────────────────────────────────────────
    public function setujui(): void
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $pengajuan = PengajuanAbsenLuar::where('id', $this->actionTargetId)->lockForUpdate()->firstOrFail();

                if ($pengajuan->status !== 'menunggu') {
                    $err = 'Pengajuan ini sudah diproses sebelumnya.';
                    session()->flash('error', $err);
                    $this->dispatch('notify', message: $err, type: 'error');
                    return;
                }

                $pengajuan->update([
                    'status'        => 'disetujui',
                    'catatan_admin' => $this->catatanAdmin ?: null,
                    'diproses_oleh' => Auth::id(),
                    'diproses_pada' => now(),
                ]);

                // Otomatis buat / update record kehadiran
                $this->buatAtauUpdateKehadiran($pengajuan);

                \App\Models\AuditLog::create([
                    'user_id'   => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'Admin',
                    'role'      => Auth::user()->role ?? 'Admin',
                    'aktivitas' => "Menyetujui pengajuan absen luar {$pengajuan->label_jenis} pegawai {$pengajuan->pegawai->nama_lengkap}",
                    'modul'     => 'Persetujuan Absen Luar',
                ]);

                $msg = "Pengajuan dari {$pengajuan->pegawai->nama_lengkap} berhasil DISETUJUI.";
                session()->flash('success', $msg);
                $this->dispatch('notify', message: $msg, type: 'success');
                $this->dispatch('refresh-notifications');
            });
        } finally {
            $this->tutupModal();
        }
    }

    // ─── Eksekusi Tolak ───────────────────────────────────────────────────────
    public function tolak(): void
    {
        $this->validate([
            'catatanAdmin' => 'required|string|min:5|max:500',
        ], [
            'catatanAdmin.required' => 'Wajib isi alasan penolakan.',
            'catatanAdmin.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $pengajuan = PengajuanAbsenLuar::where('id', $this->actionTargetId)->lockForUpdate()->firstOrFail();

                if ($pengajuan->status !== 'menunggu') {
                    $err = 'Pengajuan ini sudah diproses sebelumnya.';
                    session()->flash('error', $err);
                    $this->dispatch('notify', message: $err, type: 'error');
                    return;
                }

                $pengajuan->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => $this->catatanAdmin,
                    'diproses_oleh' => Auth::id(),
                    'diproses_pada' => now(),
                ]);

                \App\Models\AuditLog::create([
                    'user_id'   => Auth::id(),
                    'user_name' => Auth::user()->name ?? 'Admin',
                    'role'      => Auth::user()->role ?? 'Admin',
                    'aktivitas' => "Menolak pengajuan absen luar {$pengajuan->label_jenis} pegawai {$pengajuan->pegawai->nama_lengkap} (Alasan: {$this->catatanAdmin})",
                    'modul'     => 'Persetujuan Absen Luar',
                ]);

                $msg = "Pengajuan dari {$pengajuan->pegawai->nama_lengkap} telah DITOLAK.";
                session()->flash('success', $msg);
                $this->dispatch('notify', message: $msg, type: 'info');
                $this->dispatch('refresh-notifications');
            });
        } finally {
            $this->tutupModal();
        }
    }

    // ─── Helper: Buat/Update Record Kehadiran ────────────────────────────────
    private function buatAtauUpdateKehadiran(PengajuanAbsenLuar $pengajuan): void
    {
        $dateStr = $pengajuan->tanggal->toDateString();
        $kehadiran = Kehadiran::where('pegawai_id', $pengajuan->pegawai_id)
            ->whereDate('tanggal', $dateStr)
            ->first();

        if (!$kehadiran) {
            $kehadiran = new Kehadiran([
                'pegawai_id' => $pengajuan->pegawai_id,
                'tanggal'    => $dateStr,
            ]);
        }

        $labelJenis = $pengajuan->label_jenis;
        if ($pengajuan->jenis === 'dinas_luar_undangan' && $pengajuan->instansi_pengundang) {
            $labelJenis .= ': ' . $pengajuan->instansi_pengundang;
        } elseif ($pengajuan->jenis === 'dinas_luar_surat_tugas' && $pengajuan->nomor_surat_tugas) {
            $labelJenis .= ' (SPT: ' . $pengajuan->nomor_surat_tugas . ')';
        }

        $kehadiran->status            = $pengajuan->jenis === 'kegiatan_sosial' ? 'Hadir' : 'Dinas Luar';
        $kehadiran->sumber_data       = 'pengajuan_luar';
        $kehadiran->keterangan        = "[{$labelJenis}] {$pengajuan->judul}";
        $kehadiran->diverifikasi_oleh = Auth::id();

        // Simpan jam pengajuan aktual (dari created_at pengajuan atau jam sekarang)
        if (!$kehadiran->jam_masuk) {
            $kehadiran->jam_masuk = $pengajuan->created_at ? $pengajuan->created_at->format('H:i:s') : now()->format('H:i:s');
        }

        // Tanda tangan dari pengajuan → simpan ke kolom tanda_tangan_masuk
        if ($pengajuan->tanda_tangan) {
            $kehadiran->tanda_tangan_masuk = $pengajuan->tanda_tangan;
        }

        $kehadiran->save();
    }

    // ─── Render ───────────────────────────────────────────────────────────────
    public function render()
    {
        $query = PengajuanAbsenLuar::with(['pegawai.jabatan', 'user', 'diprosesoleh'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
            ->when($this->filterTanggal, fn($q) => $q->whereDate('tanggal', $this->filterTanggal))
            ->when($this->filterCari, fn($q) => $q->whereHas('pegawai', function ($sq) {
                $sq->where('nama_lengkap', 'like', '%' . $this->filterCari . '%');
            }))
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 1 WHEN status = 'disetujui' THEN 2 ELSE 3 END")
            ->orderByDesc('tanggal');

        $pengajuans    = $query->paginate(15);
        $totalMenunggu = PengajuanAbsenLuar::menunggu()->count();

        return view('livewire.pengajuan-absen-manager', compact('pengajuans', 'totalMenunggu'));
    }
}
