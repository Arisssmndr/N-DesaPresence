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

        if ($this->form['is_active']) {
            // Kebijakan 1 Jaringan Resmi Desa Aktif: Nonaktifkan jaringan lainnya
            KonfigurasiWifi::where('id', '!=', $this->editingId ?? 0)->update(['is_active' => false]);
        }

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
        if (!$wifi->is_active) {
            // Kebijakan 1 Jaringan Resmi Desa Aktif: Mengaktifkan jaringan ini akan otomatis menonaktifkan jaringan lainnya
            KonfigurasiWifi::where('id', '!=', $id)->update(['is_active' => false]);
            $wifi->update(['is_active' => true]);
            $msg = "Jaringan \"{$wifi->nama_jaringan}\" berhasil diaktifkan sebagai Jaringan WiFi Utama Kantor Desa.";
        } else {
            $wifi->update(['is_active' => false]);
            $msg = "Jaringan \"{$wifi->nama_jaringan}\" dinonaktifkan.";
        }
        
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

    public function gunakanIpLangsung(string $ip): void
    {
        $this->editingId = null;
        $this->form = [
            'nama_jaringan' => 'WiFi Kantor Desa Nangtang (IP Tunggal)',
            'ip_address'    => trim($ip),
            'keterangan'    => 'Didaftarkan otomatis dari IP perangkat saat ini (' . $ip . ')',
            'is_active'     => true,
        ];
        $this->showForm = true;
    }

    public function gunakanSubnetLangsung(string $ip): void
    {
        $this->editingId = null;
        $parts = explode('.', trim($ip));
        if (count($parts) === 4) {
            $subnet = $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.*';
        } else {
            $subnet = trim($ip);
        }

        $this->form = [
            'nama_jaringan' => 'Subnet WiFi Kantor Desa Nangtang (' . $subnet . ')',
            'ip_address'    => $subnet,
            'keterangan'    => 'Mencakup seluruh HP perangkat desa dalam jaringan ' . $subnet,
            'is_active'     => true,
        ];
        $this->showForm = true;
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
        $clientIp = request()->ip() ?: '127.0.0.1';
        $parts = explode('.', $clientIp);
        $subnetSaran = count($parts) === 4 ? $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.*' : $clientIp;

        return view('livewire.konfigurasi-wifi-manager', [
            'daftarWifi'  => KonfigurasiWifi::latest()->get(),
            'clientIp'    => $clientIp,
            'subnetSaran' => $subnetSaran,
        ])->layout('layouts.app', ['title' => 'Konfigurasi WiFi Absensi — Presence Desa']);
    }
}
