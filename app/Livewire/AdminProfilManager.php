<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\AuditLog;

class AdminProfilManager extends Component
{
    use WithFileUploads;

    // Profil state
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public $foto_profil = null;
    public ?string $currentFoto = null;

    // Password state
    public string $password_saat_ini = '';
    public string $password_baru = '';
    public string $password_baru_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->name = $user->name ?? '';
            $this->username = $user->username ?? '';
            $this->email = $user->email ?? '';
            $this->currentFoto = $user->foto_profil ?? $user->pegawai?->foto_profil ?? null;
        }
    }

    public function updateProfil(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->validate([
            'name' => 'required|string|max:150',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'foto_profil.image' => 'File foto harus berupa gambar.',
            'foto_profil.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $updateData = [
            'name' => $this->name,
            'username' => strtolower($this->username),
            'email' => $this->email ?: null,
        ];

        if ($this->foto_profil) {
            $path = $this->foto_profil->store('foto-profil', 'public');
            $updateData['foto_profil'] = $path;
            $this->currentFoto = $path;
        }

        $user->update($updateData);

        // Update nama di tabel pegawais jika terhubung
        if ($user->pegawai) {
            $user->pegawai->update(['nama_lengkap' => $this->name]);
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role ?? 'admin',
            'aktivitas' => "Mengubah profil akun (Username: @{$user->username})",
            'modul' => 'Pengaturan Akun',
        ]);

        $this->foto_profil = null;

        session()->flash('success_profil', 'Profil dan username berhasil diperbarui!');
        $this->dispatch('notify', message: 'Profil dan username berhasil diperbarui!', type: 'success');
        $this->dispatch('refresh-notifications');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $this->validate([
            'password_saat_ini' => 'required|string',
            'password_baru' => 'required|string|min:6|confirmed',
        ], [
            'password_saat_ini.required' => 'Password saat ini wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Verifikasi password saat ini
        if (!Hash::check($this->password_saat_ini, $user->password)) {
            $this->addError('password_saat_ini', 'Password saat ini yang Anda masukkan salah.');
            return;
        }

        // Simpan password baru
        $user->update([
            'password' => Hash::make($this->password_baru),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role' => $user->role ?? 'admin',
            'aktivitas' => "Mengganti password akun (@{$user->username})",
            'modul' => 'Pengaturan Akun',
        ]);

        // Reset input password
        $this->password_saat_ini = '';
        $this->password_baru = '';
        $this->password_baru_confirmation = '';

        session()->flash('success_password', 'Password akun Anda berhasil diganti! Gunakan password baru ini untuk login berikutnya.');
        $this->dispatch('notify', message: 'Password akun berhasil diganti!', type: 'success');
        $this->dispatch('refresh-notifications');
    }

    public function render()
    {
        $user = Auth::user();
        return view('livewire.admin-profil-manager', [
            'user' => $user,
        ])->layout('layouts.app', ['title' => 'Pengaturan Akun & Profil — Presence Desa']);
    }
}
