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
    public string $target_penerima = 'perangkat_tetap';
    public ?string $berlaku_hingga = null;

    // Filter Mode: 3 Opsi Terpisah ('semua' | 'bagian' | 'individual')
    public string $mode_target = 'semua';
    public array $selected_pegawai_ids = [];
    public string $search_pegawai = '';

    // Log Modal
    public bool $showWaLogModal = false;
    public ?int $selectedPengumumanId = null;

    protected function rules(): array
    {
        $allowedCategories = implode(',', array_keys(Pengumuman::kategoriList()));

        return [
            'judul'           => 'required|string|max:255',
            'isi'             => 'required|string',
            'kategori'        => "required|in:{$allowedCategories}",
            'kirim_wa'        => 'boolean',
            'mode_target'     => 'required|in:semua,bagian,individual',
            'target_penerima' => 'nullable|in:semua,perangkat_tetap,staf,bpd,kemasyarakatan',
            'berlaku_hingga'  => 'nullable|date',
            'selected_pegawai_ids' => 'array',
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

        $targetPenerimaValue = 'semua';
        $pegawaiIdsToSave = null;

        if ($this->mode_target === 'semua') {
            $targetPenerimaValue = 'semua';
            $pegawaiIdsToSave = null;
        } elseif ($this->mode_target === 'bagian') {
            $targetPenerimaValue = in_array($this->target_penerima, ['perangkat_tetap', 'staf', 'bpd', 'kemasyarakatan']) 
                ? $this->target_penerima 
                : 'perangkat_tetap';
            $pegawaiIdsToSave = null;
        } elseif ($this->mode_target === 'individual') {
            if (empty($this->selected_pegawai_ids) && $this->kirim_wa) {
                $this->addError('selected_pegawai_ids', 'Pilih minimal 1 orang pegawai untuk mode penerima individual.');
                return;
            }
            $targetPenerimaValue = 'semua';
            $pegawaiIdsToSave = array_map('intval', array_values($this->selected_pegawai_ids));
        }

        $data = [
            'judul'           => $this->judul,
            'isi'             => $this->isi,
            'kategori'        => $this->kategori,
            'is_pinned'       => $this->is_pinned,
            'kirim_wa'        => $this->kirim_wa,
            'target_penerima' => $targetPenerimaValue,
            'pegawai_ids'     => $pegawaiIdsToSave,
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

        $msg = "Pengumuman '{$p->judul}' berhasil disimpan." . ($waCount > 0 ? " {$waCount} pesan WhatsApp telah langsung dikirim ke penerima." : "");
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
        $this->berlaku_hingga = $p->berlaku_hingga ? $p->berlaku_hingga->format('Y-m-d') : null;

        if (!empty($p->pegawai_ids) && is_array($p->pegawai_ids)) {
            $this->mode_target = 'individual';
            $this->selected_pegawai_ids = array_map('strval', $p->pegawai_ids);
        } elseif ($p->target_penerima === 'semua' || empty($p->target_penerima)) {
            $this->mode_target = 'semua';
            $this->target_penerima = 'perangkat_tetap';
            $this->selected_pegawai_ids = [];
        } else {
            $this->mode_target = 'bagian';
            $this->target_penerima = $p->target_penerima;
            $this->selected_pegawai_ids = [];
        }

        $this->showModal = true;
    }

    public function selectAllPegawai()
    {
        $allIds = Pegawai::where('status_aktif', true)->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selected_pegawai_ids = $allIds;
    }

    public function deselectAllPegawai()
    {
        $this->selected_pegawai_ids = [];
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

        $msg = "Siaran WhatsApp untuk '{$p->judul}' berhasil dikirim langsung ke {$waCount} penerima.";
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
        $this->target_penerima = 'perangkat_tetap';
        $this->mode_target = 'semua';
        $this->selected_pegawai_ids = [];
        $this->search_pegawai = '';
        $this->berlaku_hingga = null;
        $this->resetValidation();
    }

    /**
     * Kirim Langsung WhatsApp Broadcast ke Penerima
     */
    private function dispatchWaBroadcast(Pengumuman $pengumuman, KonfigurasiWaService $configService): int
    {
        $query = Pegawai::where('status_aktif', true)
            ->whereNotNull('no_hp')
            ->where('no_hp', '!=', '');

        // 1. Jika mode individual (pegawai_ids terisi)
        if (!empty($pengumuman->pegawai_ids) && is_array($pengumuman->pegawai_ids)) {
            $query->whereIn('id', $pengumuman->pegawai_ids);
        } 
        // 2. Jika mode kategori/bagian
        elseif ($pengumuman->target_penerima && $pengumuman->target_penerima !== 'semua') {
            $query->where('kategori_pegawai', $pengumuman->target_penerima);
        }

        $pegawais = $query->with('user')->get();
        $count = 0;

        foreach ($pegawais as $pegawai) {
            try {
                KirimWaNotifikasiPengumumanJob::dispatchSync(
                    $pengumuman->id,
                    $pegawai->id,
                    $pegawai->user?->id,
                    $pegawai->no_hp,
                    $pegawai->nama_lengkap
                );
                $count++;
            } catch (\Exception $e) {
                \Log::warning("Gagal mengirim WA pengumuman ke {$pegawai->nama_lengkap} ({$pegawai->no_hp}): " . $e->getMessage());
            }
        }

        return $count;
    }

    public function render(KonfigurasiWaService $configService)
    {
        $selectedPengumuman = $this->selectedPengumumanId 
            ? Pengumuman::with(['waLogs.pegawai'])->find($this->selectedPengumumanId) 
            : null;

        $isWaConfigured = $configService->isEnabled();

        // Pegawai List untuk filter individual di modal
        $pegawaiQuery = Pegawai::where('status_aktif', true)->with('jabatan')->orderBy('nama_lengkap');
        if (!empty($this->search_pegawai)) {
            $s = $this->search_pegawai;
            $pegawaiQuery->where(function($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                  ->orWhere('nipd', 'like', "%{$s}%")
                  ->orWhereHas('jabatan', fn($jq) => $jq->where('nama_jabatan', 'like', "%{$s}%"));
            });
        }
        $pegawaiList = $pegawaiQuery->get();

        return view('livewire.pengumuman-manager', [
            'pengumumans'        => Pengumuman::with(['pembuat', 'waLogs'])->orderByDesc('is_pinned')->latest()->paginate(10),
            'selectedPengumuman' => $selectedPengumuman,
            'isWaConfigured'     => $isWaConfigured,
            'pegawaiList'        => $pegawaiList,
            'kategoriList'       => Pengumuman::kategoriList(),
        ])->layout('layouts.app', ['title' => 'Pengumuman Desa — N-DesaPresence']);
    }
}
