<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KonfigurasiAbsensi;
use App\Models\AuditLog;

class KonfigurasiAbsensiManager extends Component
{
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
            'jam_masuk_mulai' => 'required|date_format:H:i',
            'jam_masuk_selesai' => 'required|date_format:H:i|after:jam_masuk_mulai',
            'jam_pulang_mulai' => 'required|date_format:H:i',
            'jam_pulang_selesai' => 'required|date_format:H:i|after:jam_pulang_mulai',
        ];
    }

    public function simpan(): void
    {
        $this->validate();

        KonfigurasiAbsensi::setNilai('jam_masuk_mulai', $this->jam_masuk_mulai, 'Batas awal jam absensi masuk');
        KonfigurasiAbsensi::setNilai('jam_masuk_selesai', $this->jam_masuk_selesai, 'Batas akhir jam absensi masuk');
        KonfigurasiAbsensi::setNilai('jam_pulang_mulai', $this->jam_pulang_mulai, 'Batas awal jam absensi pulang');
        KonfigurasiAbsensi::setNilai('jam_pulang_selesai', $this->jam_pulang_selesai, 'Batas akhir jam absensi pulang');

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role' => auth()->user()->role ?? 'admin',
            'aktivitas' => "Update jendela jam absensi: Masuk ({$this->jam_masuk_mulai} - {$this->jam_masuk_selesai}), Pulang ({$this->jam_pulang_mulai} - {$this->jam_pulang_selesai})",
            'modul' => 'Konfigurasi Jam Absensi',
        ]);

        $msg = 'Jadwal & jendela jam absensi berhasil diperbarui!';
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function render()
    {
        $nowTime = now()->format('H:i');
        $isMasukNow = ($nowTime >= $this->jam_masuk_mulai && $nowTime <= $this->jam_masuk_selesai);
        $isPulangNow = ($nowTime >= $this->jam_pulang_mulai && $nowTime <= $this->jam_pulang_selesai);

        return view('livewire.konfigurasi-absensi-manager', [
            'nowTime' => $nowTime,
            'isMasukNow' => $isMasukNow,
            'isPulangNow' => $isPulangNow,
        ])->layout('layouts.app', ['title' => 'Konfigurasi Jam Absensi — Presence Desa']);
    }
}
