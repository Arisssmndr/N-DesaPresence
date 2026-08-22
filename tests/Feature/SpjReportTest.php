<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use Barryvdh\DomPDF\Facade\Pdf;

class SpjReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $user = User::first();
        if ($user) {
            $this->actingAs($user);
        }
    }

    public function test_spj_pdf_renders_and_fits_single_page()
    {
        $response = $this->get('/spj-pdf?bulan=8&tahun=2026');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $pegawais = Pegawai::with('jabatan')->where('status_aktif', true)->orderBy('nama_lengkap')->get();
        $this->assertNotEmpty($pegawais);

        $pdf = Pdf::loadView('reports.spj-pdf', [
            'bulan' => 8,
            'tahun' => 2026,
            'namaBulan' => 'Agustus',
            'daysInMonth' => 31,
            'pegawais' => $pegawais,
            'matrix' => [],
            'summary' => [],
            'kades' => $pegawais->first(),
            'sekdes' => $pegawais->skip(1)->first(),
        ])->setPaper('a4', 'landscape');

        $pdf->render();
        $pageCount = $pdf->getCanvas()->get_page_count();
        $this->assertEquals(1, $pageCount, "SPJ PDF should fit in exactly 1 page, got {$pageCount} pages");
    }

    public function test_pusat_laporan_all_pdf_endpoints()
    {
        // 1. Laporan Harian
        $resHarian = $this->get('/laporan/harian-pdf?tanggal=' . date('Y-m-d'));
        $resHarian->assertStatus(200);
        $resHarian->assertHeader('content-type', 'application/pdf');

        $pdfHarian = Pdf::loadView('reports.laporan-harian-pdf', [
            'tanggal' => '2026-08-21',
            'dt' => \Carbon\Carbon::parse('2026-08-21'),
            'pegawais' => Pegawai::with(['jabatan', 'kehadirans'])->where('status_aktif', true)->orderBy('nama_lengkap')->get(),
            'rekap' => ['hadir' => 1, 'terlambat' => 0, 'alpa' => 12, 'izin' => 0, 'sakit' => 0, 'dinas' => 1, 'libur' => 0],
            'isWeekend' => false,
            'hariLiburs' => false,
            'kades' => Pegawai::first(),
            'sekdes' => Pegawai::skip(1)->first(),
            'nomorLaporan' => '001/PRES-HRN/08/2026',
        ])->setPaper('a4', 'portrait');
        $pdfHarian->render();
        $this->assertEquals(1, $pdfHarian->getCanvas()->get_page_count(), 'Laporan Harian PDF must fit in 1 page');

        // 2. Laporan Bulanan
        $resBulanan = $this->get('/laporan/bulanan-pdf?bulan=8&tahun=2026');
        $resBulanan->assertStatus(200);
        $resBulanan->assertHeader('content-type', 'application/pdf');

        // 3. Laporan Tahunan
        $resTahunan = $this->get('/laporan/tahunan-pdf?tahun=2026');
        $resTahunan->assertStatus(200);
        $resTahunan->assertHeader('content-type', 'application/pdf');

        // 4. Laporan Siltap
        $resSiltap = $this->get('/laporan/siltap-pdf?bulan=8&tahun=2026');
        $resSiltap->assertStatus(200);
        $resSiltap->assertHeader('content-type', 'application/pdf');
    }
}
