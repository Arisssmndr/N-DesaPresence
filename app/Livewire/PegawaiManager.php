<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\ShiftKerja;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class PegawaiManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterJabatan = '';
    public string $filterStatus = '1';

    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $pegawaiId = null;

    // Form fields
    public string $pin_fingerprint = '';
    public string $nipd = '';
    public string $nik = '';
    public string $nama_lengkap = '';
    public string $tempat_lahir = '';
    public ?string $tanggal_lahir = null;
    public string $jenis_kelamin = 'L';
    public ?int $jabatan_id = null;
    public string $kategori_pegawai = 'perangkat_tetap';
    public ?int $shift_id = 1;
    public string $no_hp = '';
    public string $alamat = '';
    public $foto_profil;
    public ?string $existingFoto = null;
    public ?string $periode_mulai = null;
    public ?string $periode_akhir = null;
    public float $siltap_bruto = 0.0;
    public bool $status_aktif = true;

    protected function rules(): array
    {
        return [
            'pin_fingerprint' => 'required|string|max:20|unique:pegawais,pin_fingerprint,' . $this->pegawaiId,
            'nik' => 'required|string|size:16|unique:pegawais,nik,' . $this->pegawaiId,
            'nama_lengkap' => 'required|string|max:100',
            'jabatan_id' => 'required|exists:jabatans,id',
            'kategori_pegawai' => 'required|in:perangkat_tetap,staf,bpd,kemasyarakatan',
            'jenis_kelamin' => 'required|in:L,P',
            'nipd' => 'nullable|string|max:30',
            'no_hp' => 'nullable|string|max:15',
            'foto_profil' => 'nullable|image|max:2048', // 2MB max
            'siltap_bruto' => 'numeric|min:0',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $this->resetForm();
        $this->isEdit = true;
        $this->pegawaiId = $id;

        $p = Pegawai::findOrFail($id);
        $this->pin_fingerprint = $p->pin_fingerprint;
        $this->nipd = $p->nipd ?? '';
        $this->nik = $p->nik;
        $this->nama_lengkap = $p->nama_lengkap;
        $this->tempat_lahir = $p->tempat_lahir ?? '';
        $this->tanggal_lahir = $p->tanggal_lahir ? $p->tanggal_lahir->format('Y-m-d') : null;
        $this->jenis_kelamin = $p->jenis_kelamin ?? 'L';
        $this->jabatan_id = $p->jabatan_id;
        $this->kategori_pegawai = $p->kategori_pegawai;
        $this->shift_id = $p->shift_id;
        $this->no_hp = $p->no_hp ?? '';
        $this->alamat = $p->alamat ?? '';
        $this->existingFoto = $p->foto_profil;
        $this->periode_mulai = $p->periode_mulai ? $p->periode_mulai->format('Y-m-d') : null;
        $this->periode_akhir = $p->periode_akhir ? $p->periode_akhir->format('Y-m-d') : null;
        $this->siltap_bruto = (float) $p->siltap_bruto;
        $this->status_aktif = $p->status_aktif;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $fotoPath = $this->existingFoto;
        if ($this->foto_profil) {
            $fotoPath = $this->foto_profil->store('pegawai-photos', 'public');
        }

        $data = [
            'pin_fingerprint' => $this->pin_fingerprint,
            'nipd' => $this->nipd ?: null,
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'tempat_lahir' => $this->tempat_lahir ?: null,
            'tanggal_lahir' => $this->tanggal_lahir ?: null,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jabatan_id' => $this->jabatan_id,
            'kategori_pegawai' => $this->kategori_pegawai,
            'shift_id' => $this->shift_id ?: 1,
            'no_hp' => $this->no_hp ?: null,
            'alamat' => $this->alamat ?: null,
            'foto_profil' => $fotoPath,
            'periode_mulai' => $this->periode_mulai ?: null,
            'periode_akhir' => $this->periode_akhir ?: null,
            'siltap_bruto' => $this->siltap_bruto,
            'status_aktif' => $this->status_aktif,
        ];

        if ($this->isEdit && $this->pegawaiId) {
            $pegawai = Pegawai::findOrFail($this->pegawaiId);
            $pegawai->update($data);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Mengubah data pegawai {$pegawai->nama_lengkap} (PIN: {$pegawai->pin_fingerprint})",
                'modul' => 'Master Pegawai',
            ]);

            session()->flash('success', "Data pegawai {$pegawai->nama_lengkap} berhasil diperbarui.");
        } else {
            $pegawai = Pegawai::create($data);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menambahkan pegawai baru {$pegawai->nama_lengkap} (PIN: {$pegawai->pin_fingerprint})",
                'modul' => 'Master Pegawai',
            ]);

            session()->flash('success', "Pegawai baru {$pegawai->nama_lengkap} berhasil ditambahkan.");
        }

        $this->closeModal();
    }

    public function toggleStatus(int $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->status_aktif = !$pegawai->status_aktif;
        $pegawai->save();

        $statusText = $pegawai->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'Admin',
            'aktivitas' => "Status pegawai {$pegawai->nama_lengkap} diubah menjadi {$statusText}",
            'modul' => 'Master Pegawai',
        ]);

        session()->flash('success', "Status pegawai {$pegawai->nama_lengkap} berhasil {$statusText}.");
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->pegawaiId = null;
        $this->pin_fingerprint = '';
        $this->nipd = '';
        $this->nik = '';
        $this->nama_lengkap = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = null;
        $this->jenis_kelamin = 'L';
        $this->jabatan_id = Jabatan::first()?->id;
        $this->kategori_pegawai = 'perangkat_tetap';
        $this->shift_id = 1;
        $this->no_hp = '';
        $this->alamat = '';
        $this->foto_profil = null;
        $this->existingFoto = null;
        $this->periode_mulai = null;
        $this->periode_akhir = null;
        $this->siltap_bruto = 0.0;
        $this->status_aktif = true;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Pegawai::with(['jabatan', 'shiftKerja'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('nipd', 'like', '%' . $this->search . '%')
                        ->orWhere('pin_fingerprint', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterJabatan, fn($q) => $q->where('jabatan_id', $this->filterJabatan))
            ->when($this->filterStatus !== '', fn($q) => $q->where('status_aktif', $this->filterStatus === '1'));

        return view('livewire.pegawai-manager', [
            'pegawais' => $query->orderBy('nama_lengkap')->paginate(10),
            'jabatans' => Jabatan::all(),
            'shifts' => ShiftKerja::where('is_active', true)->get(),
        ])->layout('layouts.app', ['title' => 'Master Pegawai — Presence Desa']);
    }
}
