<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\AuditLog;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserStafManager extends Component
{
    use WithPagination;

    public array $form = [
        'pegawai_id' => '',
        'name' => '',
        'username' => '',
        'email' => '',
        'password' => '',
        'role' => 'perangkat',
        'is_active' => true,
    ];

    public ?int $editingId = null;
    public bool $showForm = false;
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function rules(): array
    {
        $isAdministrative = in_array($this->form['role'] ?? '', UserRole::administrativeRoles());
        $isCreating = empty($this->editingId);

        return [
            'form.pegawai_id' => 'nullable|exists:pegawais,id',
            'form.name' => 'required|string|max:150',
            'form.username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($this->editingId),
            ],
            'form.email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'form.password' => ($isAdministrative && $isCreating)
                ? 'required|string|min:6'
                : 'nullable|string|min:6',
            'form.role' => 'required|in:' . implode(',', UserRole::values()),
            'form.is_active' => 'boolean',
        ];
    }

    public function updatedFormPegawaiId($pegawaiId): void
    {
        if ($pegawaiId && !$this->editingId) {
            $pegawai = Pegawai::find($pegawaiId);
            if ($pegawai) {
                $this->form['name'] = $pegawai->nama_lengkap;
                $cleanName = Str::slug($pegawai->nama_lengkap, '');
                $this->form['username'] = strtolower($cleanName);
                $this->form['email'] = strtolower(Str::slug($pegawai->nama_lengkap, '.')) . '@desanangtang.go.id';
            }
        }
    }

    public function tambahBaru(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm = true;
    }

    public function editData(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->form = [
            'pegawai_id' => $user->pegawai_id ?? '',
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email ?? '',
            'password' => '',
            'role' => $user->role,
            'is_active' => $user->is_active,
        ];
        $this->showForm = true;
    }

    public function simpan(): void
    {
        $this->validate();

        $data = $this->form;
        if (empty($data['pegawai_id'])) {
            $data['pegawai_id'] = null;
        }

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            $pesan = "Update akun pengguna: {$user->name} (@{$user->username})";
        } else {
            $user = User::create($data);
            $pesan = "Buat akun pengguna baru: {$user->name} (@{$user->username})";
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'admin',
            'aktivitas' => $pesan,
            'modul' => 'Manajemen Akun',
        ]);

        $msg = 'Data akun staf berhasil disimpan!';
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
        $this->dispatch('refresh-notifications');
        $this->resetForm();
        $this->showForm = false;
        $this->editingId = null;
    }

    public function toggleAktif(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $msg = "Akun @{$user->username} berhasil {$status}.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    public function hapus(int $id): void
    {
        if ($id === auth()->id()) {
            $err = 'Anda tidak dapat menghapus akun Anda sendiri saat sedang aktif login.';
            session()->flash('error', $err);
            $this->dispatch('notify', message: $err, type: 'error');
            return;
        }

        $user = User::findOrFail($id);
        $username = $user->username;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'admin',
            'aktivitas' => "Hapus akun @{$username}",
            'modul' => 'Manajemen Akun',
        ]);

        $msg = "Akun @{$username} berhasil dihapus.";
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'info');
    }

    private function resetForm(): void
    {
        $this->form = [
            'pegawai_id' => '',
            'name' => '',
            'username' => '',
            'email' => '',
            'password' => '',
            'role' => UserRole::PERANGKAT->value,
            'is_active' => true,
        ];
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::with('pegawai.jabatan')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('username', 'like', "%{$this->search}%");
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10);

        $daftarPegawai = Pegawai::with('jabatan')
            ->where('status_aktif', true)
            ->orderBy('nama_lengkap')
            ->get();

        return view('livewire.user-staf-manager', [
            'users' => $users,
            'daftarPegawai' => $daftarPegawai,
        ])->layout('layouts.app', ['title' => 'Manajemen Akun Staf Desa — Presence Desa']);
    }
}
