<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pengumuman;
use App\Models\Pegawai;
use App\Models\WaNotifikasiLog;
use App\Models\AuditLog;
use App\Services\KonfigurasiWaService;
use App\Jobs\KirimWaNotifikasiPengumumanJob;
use Carbon\Carbon;

class PengumumanManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $pengumumanId = null;

    public string $judul = '';
    public string $isi = '';
    public string $kategori = 'informasi';
    public bool $is_pinned = false;
    public bool $kirim_wa = false;
    public string $target_penerima = 'semua';
    public ?string $berlaku_hingga = null;

    // Log Modal
    public bool $showWaLogModal = false;
    public ?int $selectedPengumumanId = null;

    protected function rules(): array
    {
        return [
            'judul'           => 'required|string|max:255',
            'isi'             => 'required|string',
            'kategori'        => 'required|in:rapat,kegiatan,informasi,penting',
            'kirim_wa'        => 'boolean',
            'target_penerima' => 'required|in:semua,perangkat_tetap,staf,bpd,kemasyarakatan',
            'berlaku_hingga'  => 'nullable|date',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save(KonfigurasiWaService $configService)
    {
        $this->validate();

        $data = [
            'judul'           => $this->judul,
            'isi'             => $this->isi,
            'kategori'        => $this->kategori,
            'is_pinned'       => $this->is_pinned,
            'kirim_wa'        => $this->kirim_wa,
            'target_penerima' => $this->target_penerima,
            'berlaku_hingga'  => $this->berlaku_hingga ?: null,
            'dibuat_oleh'     => auth()->id(),
        ];

        $p = Pengumuman::updateOrCreate(['id' => $this->pengumumanId], $data);

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menyimpan pengumuman '{$p->judul}'" . ($this->kirim_wa ? ' dengan opsi Siaran WhatsApp' : ''),
            'modul'     => 'Pengumuman',
        ]);

        $waCount = 0;
        if ($this->kirim_wa) {
            $waCount = $this->dispatchWaBroadcast($p, $configService);
        }

        $msg = "Pengumuman '{$p->judul}' berhasil disimpan." . ($waCount > 0 ? " Antrian {$waCount} pesan WhatsApp sedang diproses gateway." : "");
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
        $this->closeModal();
    }

    public function edit(int $id)
    {
        $this->resetForm();
        $this->pengumumanId = $id;
        $p = Pengumuman::findOrFail($id);
        $this->judul = $p->judul;
        $this->isi = $p->isi;
        $this->kategori = $p->kategori;
        $this->is_pinned = (bool) $p->is_pinned;
        $this->kirim_wa = (bool) $p->kirim_wa;
        $this->target_penerima = $p->target_penerima ?: 'semua';
        $this->berlaku_hingga = $p->berlaku_hingga ? $p->berlaku_hingga->format('Y-m-d') : null;
        $this->showModal = true;
    }

    public function broadcastWaManual(int $id, KonfigurasiWaService $configService)
    {
        $p = Pengumuman::findOrFail($id);
        $p->update(['kirim_wa' => true]);

        $waCount = $this->dispatchWaBroadcast($p, $configService);

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Mengirim ulang siaran WhatsApp untuk pengumuman '{$p->judul}'",
            'modul'     => 'Pengumuman',
        ]);

        $msg = "Siaran WhatsApp untuk '{$p->judul}' berhasil dimasukkan ke antrian ({$waCount} penerima).";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function openWaLogs(int $id)
    {
        $this->selectedPengumumanId = $id;
        $this->showWaLogModal = true;
    }

    public function closeWaLogs()
    {
        $this->showWaLogModal = false;
        $this->selectedPengumumanId = null;
    }

    public function togglePin(int $id)
    {
        $p = Pengumuman::findOrFail($id);
        $p->is_pinned = !$p->is_pinned;
        $p->save();

        $status = $p->is_pinned ? 'disematkan' : 'dilepas dari sematan';
        $msg = "Pengumuman '{$p->judul}' berhasil {$status}.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function delete(int $id)
    {
        $p = Pengumuman::findOrFail($id);
        $judul = $p->judul;
        $p->delete();

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menghapus pengumuman {$judul}",
            'modul'     => 'Pengumuman',
        ]);

        $msg = "Pengumuman {$judul} berhasil dihapus.";
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
        $this->pengumumanId = null;
        $this->judul = '';
        $this->isi = '';
        $this->kategori = 'informasi';
        $this->is_pinned = false;
        $this->kirim_wa = false;
        $this->target_penerima = 'semua';
        $this->berlaku_hingga = null;
        $this->resetValidation();
    }

    /**
     * Dispatch WhatsApp Broadcast Jobs ke antrian background
     */
    private function dispatchWaBroadcast(Pengumuman $pengumuman, KonfigurasiWaService $configService): int
    {
        $query = Pegawai::where('status_aktif', true)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '');

        if ($pengumuman->target_penerima && $pengumuman->target_penerima !== 'semua') {
            $query->where('kategori_pegawai', $pengumuman->target_penerima);
        }

        $pegawais = $query->with('user')->get();
        $count = 0;

        foreach ($pegawais as $pegawai) {
            KirimWaNotifikasiPengumumanJob::dispatch(
                $pengumuman->id,
                $pegawai->id,
                $pegawai->user?->id,
                $pegawai->no_hp,
                $pegawai->nama_lengkap
            );
            $count++;
        }

        return $count;
    }

    public function render(KonfigurasiWaService $configService)
    {
        $selectedPengumuman = $this->selectedPengumumanId 
            ? Pengumuman::with(['waLogs.pegawai'])->find($this->selectedPengumumanId) 
            : null;

        $isWaConfigured = $configService->isEnabled();

        return view('livewire.pengumuman-manager', [
            'pengumumans'        => Pengumuman::with(['pembuat', 'waLogs'])->orderByDesc('is_pinned')->latest()->paginate(10),
            'selectedPengumuman' => $selectedPengumuman,
            'isWaConfigured'     => $isWaConfigured,
        ])->layout('layouts.app', ['title' => 'Pengumuman Desa — N-DesaPresence']);
    }
}
