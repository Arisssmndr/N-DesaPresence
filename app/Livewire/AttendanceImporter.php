<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\AuditLog;
use Livewire\WithPagination;
use Carbon\Carbon;

/**
 * Log Absensi Digital — menampilkan riwayat absensi tanda tangan web.
 * (Menggantikan AttendanceImporter yang sebelumnya dipakai untuk import log fingerprint USB)
 */
class AttendanceImporter extends Component
{
    use WithPagination;

    public string $filterTanggal   = '';
    public string $filterPegawai   = '';
    public string $filterSumber    = '';

    protected $queryString = [
        'filterTanggal' => ['except' => ''],
        'filterPegawai' => ['except' => ''],
        'filterSumber'  => ['except' => ''],
    ];

    public function updatingFilterTanggal(): void { $this->resetPage(); }
    public function updatingFilterPegawai(): void { $this->resetPage(); }
    public function updatingFilterSumber(): void  { $this->resetPage(); }

    public function resetFilter(): void
    {
        $this->filterTanggal = '';
        $this->filterPegawai = '';
        $this->filterSumber  = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Kehadiran::with(['pegawai.jabatan', 'verifikator'])
            ->orderByDesc('tanggal')
            ->orderByDesc('updated_at');

        if ($this->filterTanggal) {
            $query->where('tanggal', $this->filterTanggal);
        }

        if ($this->filterPegawai) {
            $query->whereHas('pegawai', fn($q) =>
                $q->where('nama_lengkap', 'like', "%{$this->filterPegawai}%")
            );
        }

        if ($this->filterSumber) {
            $query->where('sumber_data', $this->filterSumber);
        }

        $kehadirans = $query->paginate(15);

        $stats = [
            'total'          => Kehadiran::count(),
            'web_signature'  => Kehadiran::where('sumber_data', 'web_signature')->count(),
            'manual_admin'   => Kehadiran::where('sumber_data', 'manual_admin')->count(),
            'hari_ini'       => Kehadiran::where('tanggal', Carbon::today()->toDateString())->count(),
        ];

        return view('livewire.attendance-importer', [
            'kehadirans' => $kehadirans,
            'stats'      => $stats,
        ])->layout('layouts.app', ['title' => 'Log Absensi Digital — Presence Desa']);
    }
}
