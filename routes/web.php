<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StafAuthController;
use App\Http\Controllers\StafPortalController;
use App\Http\Controllers\SpjReportController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengajuanAbsenLuarController;
use App\Http\Controllers\StafIzinController;

use App\Livewire\Dashboard;
use App\Livewire\PegawaiManager;
use App\Livewire\ShiftManager;
use App\Livewire\HariLiburManager;
use App\Livewire\AttendanceImporter;
use App\Livewire\ManualAttendanceOverride;
use App\Livewire\SptManager;
use App\Livewire\IzinManager;
use App\Livewire\JadwalPiketManager;
use App\Livewire\PengumumanManager;
use App\Livewire\MatriksPresensi;
use App\Livewire\AnalitikDashboard;
use App\Livewire\PusatLaporan;
use App\Livewire\KonfigurasiWifiManager;
use App\Livewire\KonfigurasiAbsensiManager;
use App\Livewire\UserStafManager;
use App\Livewire\PengajuanAbsenManager;
use App\Livewire\AdminProfilManager;
use App\Livewire\KonfigurasiWhatsAppManager;
use App\Livewire\LaporanDisesuaikanManager;
use App\Http\Controllers\LaporanDisesuaikanController;

// Redirect root to staff portal if guest, or appropriate dashboard
Route::get('/', function () {
    if (auth()->check()) {
        if (in_array(auth()->user()->role, ['admin', 'kepala_desa', 'auditor'])) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('staf.beranda');
    }
    return redirect()->route('staf.login');
});


// ─────────────────────────────────────────────────────────────────────────────
// STATUS JARINGAN WIFI REAL-TIME (Untuk Polling Status di Beranda Staf)
// Digunakan oleh JS beranda staf untuk menampilkan indikator WiFi real-time
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/staf/wifi-check', function (\Illuminate\Http\Request $request) {
    $service = app(\App\Services\AbsensiSignatureService::class);
    $clientIp = $service->resolveClientIp($request);
    $diagnosis = $service->getWifiDiagnosis($clientIp);
    return response()->json([
        'valid'           => $diagnosis['is_valid'],
        'client_ip'       => $clientIp,
        'matched_network' => $diagnosis['matched_network'],
        'message'         => $diagnosis['is_valid']
            ? 'Terhubung ke ' . ($diagnosis['matched_network'] ?? 'WiFi Kantor Desa')
            : ($diagnosis['rejection_reason'] ?? 'Tidak terhubung ke WiFi Kantor Desa.'),
    ]);
})->middleware('throttle:60,1')->name('staf.wifi.check');

// ─────────────────────────────────────────────────────────────────────────────
// PORTAL STAF DESA — Login Tanpa Password (Username-Only) & Presensi Mandiri
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('staf')->group(function () {
    Route::get('/login', [StafAuthController::class, 'showLogin'])->name('staf.login');
    Route::post('/login', [StafAuthController::class, 'login'])->middleware('throttle:15,1')->name('staf.login.post');
    Route::post('/logout', [StafAuthController::class, 'logout'])->name('staf.logout');

    Route::middleware(['auth'])->group(function () {
        Route::get('/beranda', [StafPortalController::class, 'beranda'])->name('staf.beranda');
        Route::get('/absen/{jenis}', [StafPortalController::class, 'halamanAbsen'])->name('staf.absen.form');
        Route::post('/absen/submit', [StafPortalController::class, 'submitAbsen'])->middleware('throttle:30,1')->name('staf.absen.submit');
        Route::get('/wifi-status', [StafPortalController::class, 'wifiStatus'])->name('staf.wifi-status');
        Route::get('/riwayat', [StafPortalController::class, 'riwayat'])->name('staf.riwayat');
        Route::get('/profil', [StafPortalController::class, 'profil'])->name('staf.profil');
        Route::get('/profil/edit', [\App\Http\Controllers\StafEditProfilController::class, 'edit'])->name('staf.profil.edit');
        Route::put('/profil', [\App\Http\Controllers\StafEditProfilController::class, 'update'])->name('staf.profil.update');
        Route::delete('/profil/foto', [\App\Http\Controllers\StafEditProfilController::class, 'hapusFoto'])->name('staf.profil.hapus-foto');
        Route::put('/profil/password', [\App\Http\Controllers\StafEditProfilController::class, 'updatePassword'])->name('staf.profil.update-password');
        Route::put('/profil/ttd', [\App\Http\Controllers\StafEditProfilController::class, 'updateTtd'])->name('staf.profil.update-ttd');

        // ─── Pengajuan Izin & Sakit ───────────────────────────────────────────
        Route::get('/izin', [StafIzinController::class, 'index'])->name('staf.izin');
        Route::post('/izin', [StafIzinController::class, 'store'])->middleware('throttle:15,1')->name('staf.izin.store');

        // ─── Absensi Piket Desa ───────────────────────────────────────────────
        Route::post('/piket/absen', [StafPortalController::class, 'absenPiket'])->name('staf.piket.absen');

        // ─── Pengajuan Absen Luar ─────────────────────────────────────────────
        Route::get('/ajukan-absen', [PengajuanAbsenLuarController::class, 'form'])->name('staf.ajukan.form');
        Route::post('/ajukan-absen', [PengajuanAbsenLuarController::class, 'store'])->middleware('throttle:15,1')->name('staf.ajukan.store');
        Route::get('/riwayat-pengajuan', [PengajuanAbsenLuarController::class, 'riwayat'])->name('staf.riwayat.pengajuan');

        // ─── Surat Perintah Tugas (SPT) Staf ──────────────────────────────────
        Route::get('/spt', [StafPortalController::class, 'riwayatSpt'])->name('staf.spt.riwayat');
        Route::post('/spt/{id}/terima', [StafPortalController::class, 'terimaSpt'])->name('staf.spt.terima');
        Route::post('/spt/{id}/tolak', [StafPortalController::class, 'tolakSpt'])->name('staf.spt.tolak');
    });
});

// Redirect /absen & /portal-absensi ke halaman login staf (portal kiosk sudah dihapus)
Route::get('/absen', function () {
    return redirect()->route('staf.login');
});
Route::get('/portal-absensi', function () {
    return redirect()->route('staf.login');
});

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN & KEDINASAN PANEL — Autentikasi Username + Password
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:15,1')->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Admin / Kades Routes
Route::middleware(['auth', 'role:admin,kepala_desa'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/pegawai', PegawaiManager::class)->name('pegawai.index');
    Route::get('/shift', ShiftManager::class)->name('shift.index');
    Route::get('/hari-libur', HariLiburManager::class)->name('hari-libur.index');
    Route::get('/log-absensi', AttendanceImporter::class)->name('attendance.import');
    Route::get('/override-absensi', fn() => redirect()->route('izin.index', ['tab' => 'absen_manual']))->name('attendance.override');
    Route::get('/spt', SptManager::class)->name('spt.index');
    Route::get('/izin', IzinManager::class)->name('izin.index');
    Route::get('/jadwal-piket', JadwalPiketManager::class)->name('jadwal-piket.index');
    Route::get('/pengumuman', PengumumanManager::class)->name('pengumuman.index');
    
    // Pengajuan Absen Luar — Admin Approval
    Route::get('/pengajuan-absen', PengajuanAbsenManager::class)->name('pengajuan-absen.index');

    // Pengaturan Sistem
    Route::get('/pengaturan-profil', AdminProfilManager::class)->name('admin.profil');
    Route::get('/akun-staf', UserStafManager::class)->name('user-staf.index');
    Route::get('/konfigurasi-absensi', fn() => redirect()->route('shift.index'))->name('konfigurasi-absensi.index');
    Route::get('/konfigurasi-wifi', KonfigurasiWifiManager::class)->name('konfigurasi-wifi.index');
    Route::get('/konfigurasi-wa', KonfigurasiWhatsAppManager::class)->name('konfigurasi-wa.index');

    // Phase 4 Routes (Matriks, PDF SPJ, Analitik)
    Route::get('/matriks', MatriksPresensi::class)->name('matriks.index');
    Route::get('/analitik', AnalitikDashboard::class)->name('analitik.index');
    Route::get('/analitik/pdf', [LaporanController::class, 'laporanAnalitikPdf'])->name('analitik.pdf');
    Route::get('/spj-pdf', [SpjReportController::class, 'downloadPdf'])->name('spj.pdf');

    // Phase 5 Routes (Pusat Laporan — Standar Nasional RI)
    Route::get('/laporan', PusatLaporan::class)->name('laporan.index');
    Route::get('/laporan/harian-pdf', [LaporanController::class, 'laporanHarian'])->name('laporan.harian');
    Route::get('/laporan/bulanan-pdf', [LaporanController::class, 'laporanBulanan'])->name('laporan.bulanan');
    Route::get('/laporan/tahunan-pdf', [LaporanController::class, 'laporanTahunan'])->name('laporan.tahunan');
});

// ─────────────────────────────────────────────────────────────────────────────
// SEKDES / ADMIN KHUSUS — Laporan Disesuaikan (Shadow Layer untuk Administrasi)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/laporan-disesuaikan', LaporanDisesuaikanManager::class)->name('laporan-disesuaikan.index');
    Route::get('/laporan-disesuaikan/harian-pdf', [LaporanDisesuaikanController::class, 'laporanHarian'])->name('laporan-disesuaikan.harian');
    Route::get('/laporan-disesuaikan/bulanan-pdf', [LaporanDisesuaikanController::class, 'laporanBulanan'])->name('laporan-disesuaikan.bulanan');
    Route::get('/laporan-disesuaikan/tahunan-pdf', [LaporanDisesuaikanController::class, 'laporanTahunan'])->name('laporan-disesuaikan.tahunan');
    Route::get('/laporan-disesuaikan/rentang-pdf', [LaporanDisesuaikanController::class, 'laporanRentang'])->name('laporan-disesuaikan.rentang');
});

// Fallback Route untuk memastikan seluruh file publik di storage selalu dapat diakses/dilihat
Route::get('/storage/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.local');

