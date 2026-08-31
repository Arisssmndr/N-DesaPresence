<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ShiftKerja;
use App\Models\KonfigurasiAbsensi;
use App\Models\AuditLog;
use Carbon\Carbon;

class ShiftManager extends Component
{
    public bool $showModal = false;
    public bool $isEdit = false;
    public ?int $shiftId = null;

    // Shift Form Fields
    public string $nama_shift = '';
    public string $jam_masuk = '08:00';
    public string $jam_pulang = '15:30';
    public int $toleransi_menit = 15;
    public bool $is_active = true;

    // Jendela Waktu Gate Absensi
    public string $jam_masuk_mulai = '06:00';
    public string $jam_masuk_selesai = '11:00';
    public string $jam_pulang_mulai = '14:00';
    public string $jam_pulang_selesai = '18:00';

    public function mount(): void
    {
        $this->jam_masuk_mulai = KonfigurasiAbsensi::getNilai('jam_masuk_mulai', '06:00');
        $this->jam_masuk_selesai = KonfigurasiAbsensi::getNilai('jam_masuk_selesai', '11:00');
        $this->jam_pulang_mulai = KonfigurasiAbsensi::getNilai('jam_pulang_mulai', '14:00');
        $this->jam_pulang_selesai = KonfigurasiAbsensi::getNilai('jam_pulang_selesai', '18:00');
    }

    protected function rules(): array
    {
        return [
            'nama_shift' => 'required|string|max:50',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_menit' => 'required|integer|min:0|max:120',
        ];
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
        $this->shiftId = $id;

        $s = ShiftKerja::findOrFail($id);
        $this->nama_shift = $s->nama_shift;
        $this->jam_masuk = substr($s->jam_masuk, 0, 5);
        $this->jam_pulang = substr($s->jam_pulang, 0, 5);
        $this->toleransi_menit = $s->toleransi_menit;
        $this->is_active = $s->is_active;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nama_shift' => $this->nama_shift,
            'jam_masuk' => $this->jam_masuk . ':00',
            'jam_pulang' => $this->jam_pulang . ':00',
            'toleransi_menit' => $this->toleransi_menit,
            'is_active' => $this->is_active,
        ];

        if ($this->isEdit && $this->shiftId) {
            $shift = ShiftKerja::findOrFail($this->shiftId);
            $shift->update($data);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Mengubah data shift kerja {$shift->nama_shift}",
                'modul' => 'Shift Kerja',
            ]);

            $msg = "Shift kerja {$shift->nama_shift} berhasil diperbarui.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
        } else {
            $shift = ShiftKerja::create($data);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'role' => auth()->user()->role ?? 'Admin',
                'aktivitas' => "Menambahkan shift kerja baru {$shift->nama_shift}",
                'modul' => 'Shift Kerja',
            ]);

            $msg = "Shift kerja baru {$shift->nama_shift} berhasil ditambahkan.";
            session()->flash('success', $msg);
            $this->dispatch('notify', message: $msg, type: 'success');
        }

        $this->closeModal();
    }

    public function simpanJendelaAbsensi(): void
    {
        $this->validate([
            'jam_masuk_mulai' => 'required|date_format:H:i',
            'jam_masuk_selesai' => 'required|date_format:H:i|after:jam_masuk_mulai',
            'jam_pulang_mulai' => 'required|date_format:H:i',
            'jam_pulang_selesai' => 'required|date_format:H:i|after:jam_pulang_mulai',
        ]);

        KonfigurasiAbsensi::setNilai('jam_masuk_mulai', $this->jam_masuk_mulai, 'Batas awal jam absensi masuk');
        KonfigurasiAbsensi::setNilai('jam_masuk_selesai', $this->jam_masuk_selesai, 'Batas akhir jam absensi masuk');
        KonfigurasiAbsensi::setNilai('jam_pulang_mulai', $this->jam_pulang_mulai, 'Batas awal jam absensi pulang');
        KonfigurasiAbsensi::setNilai('jam_pulang_selesai', $this->jam_pulang_selesai, 'Batas akhir jam absensi pulang');

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'admin',
            'aktivitas' => "Update jendela jam absensi: Masuk ({$this->jam_masuk_mulai} - {$this->jam_masuk_selesai}), Pulang ({$this->jam_pulang_mulai} - {$this->jam_pulang_selesai})",
            'modul' => 'Jam & Waktu Absensi',
        ]);

        $msg = 'Pengaturan jendela buka/tutup portal presensi berhasil disimpan!';
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->shiftId = null;
        $this->nama_shift = '';
        $this->jam_masuk = '08:00';
        $this->jam_pulang = '15:30';
        $this->toleransi_menit = 15;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $nowTime = Carbon::now()->format('H:i');
        $isMasukNow = ($nowTime >= $this->jam_masuk_mulai && $nowTime <= $this->jam_masuk_selesai);
        $isPulangNow = ($nowTime >= $this->jam_pulang_mulai && $nowTime <= $this->jam_pulang_selesai);

        return view('livewire.shift-manager', [
            'shifts' => ShiftKerja::withCount('pegawais')->get(),
            'nowTime' => $nowTime,
            'isMasukNow' => $isMasukNow,
            'isPulangNow' => $isPulangNow,
        ])->layout('layouts.app', ['title' => 'Jam Kerja & Waktu Absensi — Presence Desa']);
    }
}
