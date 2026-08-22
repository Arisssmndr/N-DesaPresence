<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class PusatLaporan extends Component
{
    // Harian
    public string $tanggalHarian;

    // Bulanan
    public int $bulanBulanan;
    public int $tahunBulanan;

    // Tahunan
    public int $tahunTahunan;

    public array $listBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function mount()
    {
        $this->tanggalHarian = date('Y-m-d');
        $this->bulanBulanan  = (int) date('m');
        $this->tahunBulanan  = (int) date('Y');
        $this->tahunTahunan  = (int) date('Y');
    }

    public function getUrlHarian(): string
    {
        return route('laporan.harian', ['tanggal' => $this->tanggalHarian]);
    }

    public function getUrlBulanan(): string
    {
        return route('laporan.bulanan', ['bulan' => $this->bulanBulanan, 'tahun' => $this->tahunBulanan]);
    }

    public function getUrlTahunan(): string
    {
        return route('laporan.tahunan', ['tahun' => $this->tahunTahunan]);
    }

    public function render()
    {
        $tahunOptions = range(date('Y') - 3, date('Y') + 1);
        $urlHarian  = $this->getUrlHarian();
        $urlBulanan = $this->getUrlBulanan();
        $urlTahunan = $this->getUrlTahunan();

        return view('livewire.pusat-laporan', compact(
            'tahunOptions', 'urlHarian', 'urlBulanan', 'urlTahunan'
        ))->layout('layouts.app', ['title' => 'Pusat Laporan — Presence Desa']);
    }
}
