<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pengumuman;
use App\Models\AuditLog;
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
    public ?string $berlaku_hingga = null;

    protected function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:rapat,kegiatan,informasi,penting',
            'berlaku_hingga' => 'nullable|date',
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
            'judul' => $this->judul,
            'isi' => $this->isi,
            'kategori' => $this->kategori,
            'is_pinned' => $this->is_pinned,
            'berlaku_hingga' => $this->berlaku_hingga ?: null,
            'dibuat_oleh' => auth()->id(),
        ];

        $p = Pengumuman::updateOrCreate(['id' => $this->pengumumanId], $data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menyimpan pengumuman {$p->judul}",
            'modul' => 'Pengumuman',
        ]);

        session()->flash('success', "Pengumuman {$p->judul} berhasil disimpan.");
        $this->closeModal();
    }

    public function delete(int $id)
    {
        $p = Pengumuman::findOrFail($id);
        $judul = $p->judul;
        $p->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Menghapus pengumuman {$judul}",
            'modul' => 'Pengumuman',
        ]);

        session()->flash('success', "Pengumuman {$judul} berhasil dihapus.");
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
        $this->berlaku_hingga = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.pengumuman-manager', [
            'pengumumans' => Pengumuman::with('pembuat')->orderByDesc('is_pinned')->latest()->paginate(10),
        ])->layout('layouts.app', ['title' => 'Pengumuman Desa — Presence Desa']);
    }
}
