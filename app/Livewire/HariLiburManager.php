<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HariLibur;
use App\Models\AuditLog;
use App\Services\KalenderNasionalService;

class HariLiburManager extends Component
{
    public bool $showModal = false;
    public ?int $liburId = null;

    public string $tanggal = '';
    public string $nama_hari_libur = '';
    public string $jenis = 'nasional';

    public int $filterTahun = 2026;
    public string $search = '';

    public function mount()
    {
        $this->filterTahun = (int) date('Y');
    }

    protected function rules(): array
    {
        return [
            'tanggal' => 'required|date|unique:hari_liburs,tanggal,' . $this->liburId,
            'nama_hari_libur' => 'required|string|max:100',
            'jenis' => 'required|in:nasional,cuti_bersama,lokal',
        ];
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function sinkronkanApi(KalenderNasionalService $service)
    {
        try {
            $count = $service->sinkronkanKeDatabase($this->filterTahun);
            $msg = "Berhasil menyinkronkan {$count} hari libur nasional & cuti bersama resmi RI tahun {$this->filterTahun} dari API!";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
        } catch (\Throwable $e) {
            $err = "Gagal menyinkronkan kalender nasional: " . $e->getMessage();
            session()->flash('error', $err);
            $this->dispatch('notify', message: $err, type: 'error');
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tanggal' => $this->tanggal,
            'nama_hari_libur' => $this->nama_hari_libur,
            'jenis' => $this->jenis,
        ];

        $libur = HariLibur::updateOrCreate(['id' => $this->liburId], $data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menyimpan hari libur {$libur->nama_hari_libur} ({$libur->tanggal})",
            'modul' => 'Hari Libur',
        ]);

        $msg = "Hari libur {$libur->nama_hari_libur} berhasil disimpan.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
        $this->closeModal();
    }

    public function delete(int $id)
    {
        $libur = HariLibur::findOrFail($id);
        $nama = $libur->nama_hari_libur;
        $libur->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menghapus hari libur {$nama}",
            'modul' => 'Hari Libur',
        ]);

        $msg = "Hari libur {$nama} berhasil dihapus.";
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
        $this->liburId = null;
        $this->tanggal = '';
        $this->nama_hari_libur = '';
        $this->jenis = 'nasional';
        $this->resetValidation();
    }

    public function render()
    {
        $query = HariLibur::query()
            ->when($this->filterTahun, fn($q) => $q->whereYear('tanggal', $this->filterTahun))
            ->when($this->search, fn($q) => $q->where('nama_hari_libur', 'like', '%' . $this->search . '%'))
            ->orderBy('tanggal', 'asc');

        return view('livewire.hari-libur-manager', [
            'hariLiburs' => $query->get(),
        ])->layout('layouts.app', ['title' => 'Hari Libur — Presence Desa']);
    }
}
