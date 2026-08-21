<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KonfigurasiWifi;
use App\Models\AuditLog;
use Illuminate\Validation\Rule;

class KonfigurasiWifiManager extends Component
{
    public array $form = [
        'nama_jaringan' => '',
        'ip_address'    => '',
        'keterangan'    => '',
        'is_active'     => true,
    ];

    public ?int $editingId = null;
    public bool $showForm = false;

    protected function rules(): array
    {
        return [
            'form.nama_jaringan' => 'required|string|max:100',
            'form.ip_address'    => 'required|string|max:45',
            'form.keterangan'    => 'nullable|string|max:255',
            'form.is_active'     => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'form.nama_jaringan.required' => 'Nama jaringan wajib diisi.',
            'form.ip_address.required'    => 'Alamat IP wajib diisi.',
        ];
    }

    public function tambahBaru(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function editData(int $id): void
    {
        $data = KonfigurasiWifi::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'nama_jaringan' => $data->nama_jaringan,
            'ip_address'    => $data->ip_address,
            'keterangan'    => $data->keterangan ?? '',
            'is_active'     => $data->is_active,
        ];
        $this->showForm = true;
    }

    public function simpan(): void
    {
        $this->validate();

        if ($this->editingId) {
            $wifi = KonfigurasiWifi::findOrFail($this->editingId);
            $wifi->update($this->form);
            $aksi = "Update konfigurasi WiFi: {$this->form['nama_jaringan']} ({$this->form['ip_address']})";
        } else {
            KonfigurasiWifi::create($this->form);
            $aksi = "Tambah konfigurasi WiFi baru: {$this->form['nama_jaringan']} ({$this->form['ip_address']})";
        }

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'admin',
            'aktivitas' => $aksi,
            'modul'     => 'Konfigurasi WiFi',
        ]);

        $msg = 'Konfigurasi WiFi berhasil disimpan.';
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
        $this->resetForm();
        $this->showForm = false;
        $this->editingId = null;
    }

    public function toggleAktif(int $id): void
    {
        $wifi = KonfigurasiWifi::findOrFail($id);
        $wifi->update(['is_active' => !$wifi->is_active]);
        $status = $wifi->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $msg = "Jaringan \"{$wifi->nama_jaringan}\" berhasil {$status}.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function hapus(int $id): void
    {
        $wifi = KonfigurasiWifi::findOrFail($id);
        $nama = $wifi->nama_jaringan;
        $wifi->delete();

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'admin',
            'aktivitas' => "Hapus konfigurasi WiFi: {$nama}",
            'modul'     => 'Konfigurasi WiFi',
        ]);

        $msg = "Konfigurasi WiFi \"{$nama}\" berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    private function resetForm(): void
    {
        $this->form = [
            'nama_jaringan' => '',
            'ip_address'    => '',
            'keterangan'    => '',
            'is_active'     => true,
        ];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.konfigurasi-wifi-manager', [
            'daftarWifi' => KonfigurasiWifi::latest()->get(),
            'clientIp'   => request()->ip(),
        ])->layout('layouts.app', ['title' => 'Konfigurasi WiFi Absensi — Presence Desa']);
    }
}
