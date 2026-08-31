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
    public bool $showRejectModal = false;
    public bool $showDetailModal = false;
    public ?int $selectedSptId = null;
    public ?SuratPerintahTugas $selectedSpt = null;
    public ?SuratPerintahTugas $detailSpt = null;
    public string $catatanPenolakan = '';
    public ?int $sptId = null;

    public ?int $pegawai_id = null;
    public string $nomor_spt = '';
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
            'nomor_spt' => 'nullable|string|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan' => 'required|string|max:255',
            'keperluan' => 'required|string',
            'anggaran' => 'nullable|numeric|min:0',
            'file_undangan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'pegawai_id.required' => 'Pilih perangkat desa yang ditugaskan.',
            'tanggal_mulai.required' => 'Tanggal mulai tugas wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai tugas wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'tujuan.required' => 'Tujuan / lokasi kedinasan wajib diisi.',
            'keperluan.required' => 'Keperluan / agenda kedinasan wajib diisi.',
            'file_undangan.required' => 'Berkas / Softfile Surat Perintah Tugas (SPT) atau Surat Undangan dinas wajib diunggah.',
            'file_undangan.file' => 'Berkas harus berupa dokumen valid (PDF, JPG, JPEG, atau PNG).',
            'file_undangan.max' => 'Ukuran berkas maksimal 5 MB.',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function bukaDetailModal(int $id)
    {
        $this->detailSpt = SuratPerintahTugas::with(['pegawai.jabatan', 'pembuat'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function tutupDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailSpt = null;
    }

    public function deleteSpt(int $id)
    {
        $spt = SuratPerintahTugas::with('pegawai')->findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($spt) {
            $spt->batalkanKehadiran();
            if ($spt->file_undangan && \Illuminate\Support\Facades\Storage::disk('public')->exists($spt->file_undangan)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($spt->file_undangan);
            }
            $spt->delete();

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menghapus penugasan SPT ({$spt->tujuan}) untuk {$spt->pegawai->nama_lengkap}",
                'modul' => 'Surat Perintah Tugas',
            ]);
        });

        $msg = "Data Surat Perintah Tugas berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function createSpt()
    {
        $this->validate();

        // Cek tumpang tindih SPT aktif
        $adaSptBentrok = SuratPerintahTugas::where('pegawai_id', $this->pegawai_id)
            ->where('status', '!=', 'ditolak')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggal_mulai, $this->tanggal_selesai])
                  ->orWhereBetween('tanggal_selesai', [$this->tanggal_mulai, $this->tanggal_selesai])
                  ->orWhere(function ($sub) {
                      $sub->where('tanggal_mulai', '<=', $this->tanggal_mulai)
                          ->where('tanggal_selesai', '>=', $this->tanggal_selesai);
                  });
            })->exists();

        if ($adaSptBentrok) {
            $this->addError('tanggal_mulai', 'Pegawai sudah memiliki SPT aktif pada rentang tanggal tersebut.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () {
            $undanganPath = null;
            if ($this->file_undangan) {
                $undanganPath = $this->file_undangan->store('spt-undangan', 'public');
            }

            // Simpan nomor SPT jika diisi manual oleh admin, atau null jika dikosongkan (tanpa generate dummy)
            $nomorSpt = !empty(trim($this->nomor_spt)) ? trim($this->nomor_spt) : null;

            $spt = SuratPerintahTugas::create([
                'nomor_spt' => $nomorSpt,
                'pegawai_id' => $this->pegawai_id,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'tujuan' => $this->tujuan,
                'keperluan' => $this->keperluan,
                'file_undangan' => $undanganPath,
                'anggaran' => $this->anggaran ?? 0,
                'status' => 'diajukan',
                'respons_staf' => 'menunggu',
                'created_by' => auth()->id(),
            ]);

            $pegawai = Pegawai::find($this->pegawai_id);
            $sptLabel = $spt->nomor_spt ? "SPT {$spt->nomor_spt}" : "SPT";

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menerbitkan {$sptLabel} untuk {$pegawai->nama_lengkap} (Tujuan: {$spt->tujuan}) — Menunggu Konfirmasi Staf",
                'modul' => 'Surat Perintah Tugas',
            ]);

            $msg = "Surat Perintah Tugas ({$spt->tujuan}) untuk {$pegawai->nama_lengkap} berhasil diterbitkan dan dikirim ke portal staf.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
            $this->dispatch('refresh-notifications');
            $this->closeModal();
        });
    }

    public function approve(int $id)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $spt = SuratPerintahTugas::where('id', $id)->lockForUpdate()->firstOrFail();
            $spt->update([
                'status' => 'disetujui',
                'respons_staf' => 'diterima',
                'disetujui_oleh' => auth()->id(),
                'tanggal_persetujuan' => now(),
                'waktu_respons_staf' => $spt->waktu_respons_staf ?? now(),
            ]);

            $spt->terapkanKehadiran(null, auth()->id());

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Kepala Desa',
                'aktivitas' => "Menyetujui SPT {$spt->nomor_spt} untuk {$spt->pegawai->nama_lengkap}",
                'modul' => 'Surat Perintah Tugas',
            ]);

            $msg = "SPT {$spt->nomor_spt} telah disetujui secara resmi.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
            $this->dispatch('refresh-notifications');
        });
    }

    public function konfirmasiTolak(int $id)
    {
        $this->selectedSptId = $id;
        $this->selectedSpt = SuratPerintahTugas::with('pegawai')->findOrFail($id);
        $this->catatanPenolakan = '';
        $this->showRejectModal = true;
    }

    public function tutupRejectModal()
    {
        $this->showRejectModal = false;
        $this->selectedSptId = null;
        $this->selectedSpt = null;
        $this->catatanPenolakan = '';
    }

    public function reject()
    {
        $this->validate([
            'catatanPenolakan' => 'required|string|min:5|max:500',
        ], [
            'catatanPenolakan.required' => 'Wajib mengisi alasan penolakan/pembatalan SPT.',
            'catatanPenolakan.min' => 'Alasan minimal 5 karakter.',
        ]);

        $id = $this->selectedSptId;

        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $spt = SuratPerintahTugas::where('id', $id)->lockForUpdate()->firstOrFail();
            $spt->update([
                'status' => 'ditolak',
                'respons_staf' => 'ditolak',
                'disetujui_oleh' => auth()->id(),
                'catatan_penolakan' => $this->catatanPenolakan,
            ]);

            $spt->batalkanKehadiran();

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Kepala Desa',
                'aktivitas' => "Membatalkan/Menolak SPT {$spt->nomor_spt} untuk {$spt->pegawai->nama_lengkap} (Alasan: {$this->catatanPenolakan})",
                'modul' => 'Surat Perintah Tugas',
            ]);

            $msg = "SPT {$spt->nomor_spt} telah ditolak/dibatalkan.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'info');
            $this->dispatch('refresh-notifications');
            $this->tutupRejectModal();
        });
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
        $this->nomor_spt = '';
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
