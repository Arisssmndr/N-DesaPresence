<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HariLibur;
use App\Models\AuditLog;

class HariLiburManager extends Component
{
    public bool $showModal = false;
    public ?int $liburId = null;

    public string $tanggal = '';
    public string $nama_hari_libur = '';
    public string $jenis = 'nasional';

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

        session()->flash('success', "Hari libur {$libur->nama_hari_libur} berhasil disimpan.");
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

        session()->flash('success', "Hari libur {$nama} berhasil dihapus.");
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
        return view('livewire.hari-libur-manager', [
            'hariLiburs' => HariLibur::orderBy('tanggal', 'desc')->get(),
        ])->layout('layouts.app', ['title' => 'Hari Libur — Presence Desa']);
    }
}
