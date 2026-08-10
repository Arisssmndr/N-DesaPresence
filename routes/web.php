<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpjReportController;
use App\Livewire\Dashboard;
use App\Livewire\PegawaiManager;
use App\Livewire\ShiftManager;
use App\Livewire\HariLiburManager;
use App\Livewire\AttendanceImporter;
use App\Livewire\ManualAttendanceOverride;
use App\Livewire\SptManager;
use App\Livewire\IzinManager;
use App\Livewire\PengumumanManager;
use App\Livewire\MatriksPresensi;
use App\Livewire\SiltapKalkulator;
use App\Livewire\AnalitikDashboard;

// Redirect root to dashboard (or login)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Management & Kedinasan Routes
    Route::middleware(['role:admin,kepala_desa,auditor'])->group(function () {
        Route::get('/pegawai', PegawaiManager::class)->name('pegawai.index');
        Route::get('/shift', ShiftManager::class)->name('shift.index');
        Route::get('/hari-libur', HariLiburManager::class)->name('hari-libur.index');
        Route::get('/import-absensi', AttendanceImporter::class)->name('attendance.import');
        Route::get('/override-absensi', ManualAttendanceOverride::class)->name('attendance.override');
        Route::get('/spt', SptManager::class)->name('spt.index');
        Route::get('/izin', IzinManager::class)->name('izin.index');
        Route::get('/pengumuman', PengumumanManager::class)->name('pengumuman.index');

        // Phase 4 Routes (Matriks, Siltap, PDF SPJ, Analitik)
        Route::get('/matriks', MatriksPresensi::class)->name('matriks.index');
        Route::get('/siltap', SiltapKalkulator::class)->name('siltap.index');
        Route::get('/analitik', AnalitikDashboard::class)->name('analitik.index');
        Route::get('/spj-pdf', [SpjReportController::class, 'downloadPdf'])->name('spj.pdf');
    });
});
