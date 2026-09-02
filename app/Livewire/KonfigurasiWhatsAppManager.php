<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Pegawai;
use App\Models\WaNotifikasiLog;
use App\Models\Pengumuman;
use App\Services\KonfigurasiWaService;
use App\Services\FonnteWhatsAppService;

class KonfigurasiWhatsAppManager extends Component
{
    // Active navigation tab
    public string $activeTab = 'device'; // 'device' | 'settings' | 'logs'

    // Form settings
    public array $form = [
        'fonnte_account_token'  => '',
        'fonnte_api_key'        => '',
        'fonnte_sender_number'  => '',
        'wa_notifikasi_enabled' => false,
        'wa_country_code'       => '62',
    ];

    // Device Manager State
    public array $devicesList = [];
    public bool $isLoadingDevices = false;
    public ?string $deviceListError = null;

    // Device Detail Modal State
    public bool $showDeviceDetailModal = false;
    public ?array $selectedDeviceDetail = null;

    // Add Device Modal State
    public bool $showAddDeviceModal = false;
    public string $newDeviceName = '';
    public string $newDeviceNumber = '';
    public int $modalStep = 1; // 1: Form Input, 2: Scan QR, 3: Success Connected
    public ?string $qrCodeData = null;
    public ?string $activeQrDevice = null;
    public ?string $activeQrToken = null;
    public ?string $createdDeviceToken = null;
    public bool $isGeneratingQr = false;
    public bool $isCheckingQrConnection = false;
    public ?string $qrErrorMessage = null;

    // Manual QR Modal State (for existing device)
    public bool $showQrModal = false;

    // Testing state
    public string $testNomorHp = '';
    public string $testPesan = 'Halo! Ini adalah pesan uji coba (test notification) dari Sistem N-DesaPresence Pemerintah Desa Nangtang menggunakan Gateway Fonnte WhatsApp. Koneksi berhasil aktif!';
    public ?array $testResult = null;
    public bool $isTesting = false;

    // Live Device Quick Status
    public ?array $deviceInfo = null;

    public function mount(KonfigurasiWaService $configService, FonnteWhatsAppService $waService): void
    {
        $this->form = [
            'fonnte_account_token'  => (string) ($configService->get('fonnte_account_token') ?? ''),
            'fonnte_api_key'        => (string) ($configService->get('fonnte_api_key') ?? ''),
            'fonnte_sender_number'  => (string) ($configService->get('fonnte_sender_number') ?? ''),
            'wa_notifikasi_enabled' => (bool) $configService->get('wa_notifikasi_enabled', false),
            'wa_country_code'       => (string) ($configService->get('wa_country_code') ?? '62'),
        ];

        // 1. Muat perangkat dari cache lokal secara instan (0ms render, tidak akan hilang saat refresh)
        $this->devicesList = $configService->getCachedDevices();

        // 2. Jika cache lokal belum ada dan token sudah tersimpan, muat dari Fonnte
        if (empty($this->devicesList) && !empty($this->form['fonnte_account_token'])) {
            $this->loadDevices($waService, $configService);
        }

        // 3. Cek status live device aktif jika ada api key
        if (!empty($this->form['fonnte_api_key'])) {
            $this->cekStatusPerangkat($waService);
        }
    }

    /**
     * Simpan Master Account Token Fonnte
     */
    public function saveAccountToken(KonfigurasiWaService $configService, FonnteWhatsAppService $waService): void
    {
        $this->validate([
            'form.fonnte_account_token' => 'required|string|min:8',
        ], [
            'form.fonnte_account_token.required' => 'Account Token Fonnte wajib diisi.',
        ]);

        $configService->set('fonnte_account_token', trim($this->form['fonnte_account_token']), 'Master Account Token Fonnte (untuk kelola Device & QR Code)');

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'admin',
            'aktivitas' => 'Menyimpan Master Account Token Fonnte',
            'modul'     => 'Konfigurasi WhatsApp',
        ]);

        $this->dispatch('notify', message: 'Master Account Token Fonnte berhasil disimpan!', type: 'success');
        $this->loadDevices($waService, $configService);
    }

    /**
     * Muat list seluruh device dari Fonnte API dengan fallback ke cache lokal
     */
    public function loadDevices(FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        $this->isLoadingDevices = true;
        $this->deviceListError = null;

        $res = $waService->getDevicesList($this->form['fonnte_account_token'] ?: null);

        $this->isLoadingDevices = false;

        if ($res['success']) {
            $this->devicesList = $res['data'] ?? [];
            if (empty($this->devicesList)) {
                $this->deviceListError = 'Belum ada perangkat WhatsApp yang terdaftar di akun Fonnte Anda.';
            }
        } else {
            // Ambil dari cache lokal sebagai fallback darurat
            $cached = $configService->getCachedDevices();
            if (!empty($cached)) {
                $this->devicesList = $cached;
            } else {
                $this->devicesList = [];
                $this->deviceListError = $res['message'] ?? 'Gagal memuat daftar perangkat dari Fonnte.';
            }
        }
    }

    /**
     * Buka Modal Detail Device
     */
    public function openDeviceDetail(array $device): void
    {
        $this->selectedDeviceDetail = $device;
        $this->showDeviceDetailModal = true;
    }

    public function closeDeviceDetail(): void
    {
        $this->showDeviceDetailModal = false;
        $this->selectedDeviceDetail = null;
    }

    /**
     * Buka Modal Tambah Device Baru
     */
    public function openAddDeviceModal(): void
    {
        $this->resetValidation();
        $this->newDeviceName = 'WA Desa Nangtang';
        $this->newDeviceNumber = '';
        $this->modalStep = 1;
        $this->qrCodeData = null;
        $this->activeQrDevice = null;
        $this->activeQrToken = null;
        $this->createdDeviceToken = null;
        $this->qrErrorMessage = null;
        $this->showAddDeviceModal = true;
    }

    /**
     * Proses Tambah Device ke Fonnte lalu langsung ambil QR Code dengan Device Token
     */
    public function submitAddDevice(FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        $this->validate([
            'newDeviceName'   => 'required|string|max:50',
            'newDeviceNumber' => 'required|string|min:8|max:20',
        ], [
            'newDeviceName.required'   => 'Nama perangkat wajib diisi.',
            'newDeviceNumber.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $this->isGeneratingQr = true;
        $this->qrErrorMessage = null;

        // 1. Panggil Add Device API
        $addRes = $waService->addDevice(
            $this->newDeviceName,
            $this->newDeviceNumber,
            $this->form['fonnte_account_token'] ?: null
        );

        if (!$addRes['success']) {
            $this->isGeneratingQr = false;
            $this->qrErrorMessage = $addRes['message'] ?? 'Gagal menambahkan device ke Fonnte.';
            return;
        }

        $this->createdDeviceToken = $addRes['token'] ?? null;
        $this->activeQrDevice = $addRes['device'] ?? $this->newDeviceNumber;
        $this->activeQrToken = $addRes['token'] ?? null;

        // 2. Ambil QR Code menggunakan Device Token
        $this->fetchQrCode($waService);
        $this->modalStep = 2; // Pindah ke layar Scan QR
        $this->isGeneratingQr = false;

        // Muat ulang daftar device di background
        $this->loadDevices($waService, $configService);
    }

    /**
     * Buka Modal QR untuk Device yang Sudah Ada
     */
    public function openQrModalForDevice(string $deviceNumber, ?string $deviceToken, FonnteWhatsAppService $waService): void
    {
        $this->activeQrDevice = $deviceNumber;
        $this->activeQrToken = $deviceToken;
        $this->qrCodeData = null;
        $this->qrErrorMessage = null;
        $this->showQrModal = true;
        $this->fetchQrCode($waService);
    }

    /**
     * Helper fetch QR Code
     */
    public function fetchQrCode(FonnteWhatsAppService $waService): void
    {
        if (empty($this->activeQrDevice)) {
            return;
        }

        $this->isGeneratingQr = true;
        $this->qrErrorMessage = null;

        $qrRes = $waService->getQrCode(
            $this->activeQrDevice,
            $this->activeQrToken ?: $this->createdDeviceToken,
            $this->form['fonnte_account_token'] ?: null
        );

        $this->isGeneratingQr = false;

        if ($qrRes['success'] && !empty($qrRes['url'])) {
            $this->qrCodeData = $qrRes['url'];
        } else {
            $this->qrErrorMessage = $qrRes['message'] ?? 'QR Code belum tersedia atau perangkat sudah terhubung.';
        }
    }

    /**
     * Auto Polling Status QR Code (Otomatis seperti WhatsApp Web)
     */
    public function autoCheckQrStatus(FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        // Hanya jalan jika modal QR sedang terbuka dan belum sukses terhubung
        if ((!$this->showAddDeviceModal || $this->modalStep !== 2) && !$this->showQrModal) {
            return;
        }

        if (empty($this->activeQrDevice)) {
            return;
        }

        $tokenToTest = $this->activeQrToken ?: $this->createdDeviceToken;
        $isConnected = false;
        $matchedToken = $tokenToTest;

        // Cek 1: Cek status via token langsung
        if (!empty($tokenToTest)) {
            $statusCheck = $waService->checkDeviceStatus($tokenToTest);
            if ($statusCheck['connected'] ?? false) {
                $isConnected = true;
            }
        }

        // Cek 2: Fallback cek via list perangkat
        if (!$isConnected && !empty($this->form['fonnte_account_token'])) {
            $res = $waService->getDevicesList($this->form['fonnte_account_token']);
            if ($res['success']) {
                $this->devicesList = $res['data'] ?? [];
                foreach ($this->devicesList as $dev) {
                    $devNum = $dev['device'] ?? ($dev['whatsapp'] ?? '');
                    if ($devNum == $this->activeQrDevice || str_ends_with($this->activeQrDevice, substr($devNum, -8))) {
                        $st = strtolower($dev['status'] ?? ($dev['device_status'] ?? ''));
                        if (in_array($st, ['connect', 'connected'])) {
                            $isConnected = true;
                            if (!empty($dev['token'])) {
                                $matchedToken = $dev['token'];
                            }
                            break;
                        }
                    }
                }
            }
        }

        // Jika terdeteksi terhubung, langsung jadikan pengirim aktif dan alihkan otomatis!
        if ($isConnected && !empty($matchedToken)) {
            $this->form['fonnte_api_key'] = $matchedToken;
            $this->form['fonnte_sender_number'] = $this->activeQrDevice;
            $this->form['wa_notifikasi_enabled'] = true;

            $configService->set('fonnte_api_key', $matchedToken, 'Token API Fonnte Resmi (dikelola admin)');
            $configService->set('fonnte_sender_number', $this->activeQrDevice, 'Nomor WhatsApp Sender Terdaftar di Fonnte');
            $configService->set('wa_notifikasi_enabled', '1', 'Master Sakelar Aktif/Nonaktif Pengiriman WhatsApp Otomatis');

            $this->cekStatusPerangkat($waService);
            $this->loadDevices($waService, $configService);

            if ($this->showAddDeviceModal) {
                $this->modalStep = 3; // Layar Sukses
            } else {
                $this->showQrModal = false;
            }

            $this->dispatch('notify', message: 'WhatsApp Berhasil Terhubung!', type: 'success');
        }
    }

    /**
     * Cek status koneksi QR / Device manual
     */
    public function checkQrConnection(FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        $this->isCheckingQrConnection = true;
        $this->autoCheckQrStatus($waService, $configService);
        $this->isCheckingQrConnection = false;
    }

    /**
     * Set device tertentu sebagai Pengirim Aktif di sistem (1 Click!)
     */
    public function setActiveDevice(string $deviceNumber, ?string $token, KonfigurasiWaService $configService, FonnteWhatsAppService $waService): void
    {
        if (empty($token)) {
            $this->dispatch('notify', message: 'Token untuk perangkat ini belum tersedia. Silakan tautkan ulang atau copy dari Fonnte.', type: 'error');
            return;
        }

        $this->form['fonnte_api_key'] = $token;
        $this->form['fonnte_sender_number'] = $deviceNumber;
        $this->form['wa_notifikasi_enabled'] = true;

        $configService->set('fonnte_api_key', $token, 'Token API Fonnte Resmi (dikelola admin)');
        $configService->set('fonnte_sender_number', $deviceNumber, 'Nomor WhatsApp Sender Terdaftar di Fonnte');
        $configService->set('wa_notifikasi_enabled', '1', 'Master Sakelar Aktif/Nonaktif Pengiriman WhatsApp Otomatis');

        $this->cekStatusPerangkat($waService);
        $this->closeDeviceDetail();

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'admin',
            'aktivitas' => "Mengatur perangkat WA {$deviceNumber} sebagai Gateway Pengirim Utama",
            'modul'     => 'Konfigurasi WhatsApp',
        ]);

        $this->dispatch('notify', message: "Perangkat {$deviceNumber} aktif sebagai bot pengirim utama!", type: 'success');
    }

    /**
     * Putuskan koneksi device di Fonnte
     */
    public function disconnectDevice(string $deviceNumber, ?string $deviceToken, FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        $this->closeDeviceDetail();

        $res = $waService->disconnectDevice($deviceNumber, $deviceToken, $this->form['fonnte_account_token'] ?: null);
        
        $this->dispatch('notify', message: $res['message'] ?? 'Koneksi perangkat diputuskan.', type: 'info');
        $this->loadDevices($waService, $configService);
        $this->cekStatusPerangkat($waService);
    }

    /**
     * Hapus device dari Fonnte dan reset konfigurasi jika aktif (Instan & Non-blocking)
     */
    public function deleteDevice(string $deviceNumber, ?string $deviceToken, FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        // 1. Langsung hapus dari daftar lokal di memory & cache agar UI responsif seketika
        $this->devicesList = array_values(array_filter($this->devicesList, function($d) use ($deviceNumber) {
            $dNum = $d['device'] ?? ($d['whatsapp'] ?? '');
            return $dNum !== $deviceNumber;
        }));
        $configService->setCachedDevices($this->devicesList);

        // 2. Jika device yang dihapus adalah pengirim aktif saat ini, bersihkan konfigurasi
        if ($this->form['fonnte_sender_number'] === $deviceNumber) {
            $this->form['fonnte_api_key'] = '';
            $this->form['fonnte_sender_number'] = '';
            $this->form['wa_notifikasi_enabled'] = false;

            $configService->set('fonnte_api_key', null);
            $configService->set('fonnte_sender_number', null);
            $configService->set('wa_notifikasi_enabled', '0');
            $this->deviceInfo = null;
        }

        // 3. Tutup modal detail secara instan
        $this->closeDeviceDetail();

        // 4. Kirim notifikasi responsif ke pengguna
        $this->dispatch('notify', message: "Perangkat {$deviceNumber} berhasil dihapus.", type: 'success');

        // 5. Panggil API Fonnte untuk hapus di server
        $waService->deleteDevice($deviceNumber, $deviceToken, $this->form['fonnte_account_token'] ?: null);
    }

    /**
     * Toggle master sakelar WhatsApp
     */
    public function toggleWaEnabled(KonfigurasiWaService $configService): void
    {
        $this->form['wa_notifikasi_enabled'] = !$this->form['wa_notifikasi_enabled'];
        $configService->set('wa_notifikasi_enabled', $this->form['wa_notifikasi_enabled'] ? '1' : '0');

        $status = $this->form['wa_notifikasi_enabled'] ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('notify', message: "Notifikasi WhatsApp otomatis berhasil {$status}.", type: 'info');
    }

    /**
     * Cek status live device aktif
     */
    public function cekStatusPerangkat(FonnteWhatsAppService $waService): void
    {
        if (empty($this->form['fonnte_api_key'])) {
            $this->deviceInfo = null;
            return;
        }

        $this->deviceInfo = $waService->checkDeviceStatus($this->form['fonnte_api_key']);

        if (!empty($this->deviceInfo['device']) && empty($this->form['fonnte_sender_number'])) {
            $this->form['fonnte_sender_number'] = $this->deviceInfo['device'];
        }
    }

    /**
     * Uji coba kirim pesan WhatsApp
     */
    public function testKirim(FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        $this->validate([
            'testNomorHp' => 'required|string|min:8|max:20',
            'testPesan'   => 'required|string|min:2',
        ], [
            'testNomorHp.required' => 'Nomor WhatsApp tujuan wajib diisi.',
            'testPesan.required'   => 'Isi pesan uji coba wajib diisi.',
        ]);

        if (empty($this->form['fonnte_api_key'])) {
            $this->dispatch('notify', message: 'Belum ada perangkat WhatsApp aktif yang dipilih di Tab Perangkat & QR.', type: 'error');
            return;
        }

        $this->isTesting = true;

        $res = $waService->send($this->testNomorHp, $this->testPesan, [
            'api_key' => $this->form['fonnte_api_key'],
        ]);

        $this->testResult = $res;
        $this->isTesting = false;

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'admin',
            'aktivitas' => "Uji coba kirim WhatsApp ke nomor {$this->testNomorHp} (Status: {$res['status']})",
            'modul'     => 'Konfigurasi WhatsApp',
        ]);

        if ($res['success']) {
            $this->dispatch('notify', message: 'Pesan uji coba berhasil dikirim ke WhatsApp tujuan!', type: 'success');
            $this->cekStatusPerangkat($waService);
        } else {
            $this->dispatch('notify', message: 'Gagal kirim: ' . ($res['message'] ?? 'Error'), type: 'error');
        }
    }

    public function closeModals(): void
    {
        $this->showAddDeviceModal = false;
        $this->showQrModal = false;
        $this->showDeviceDetailModal = false;
        $this->qrCodeData = null;
        $this->activeQrDevice = null;
        $this->activeQrToken = null;
        $this->qrErrorMessage = null;
    }

    public function render()
    {
        $totalPegawai    = Pegawai::where('status_aktif', true)->count();
        $pegawaiDenganHp = Pegawai::where('status_aktif', true)->whereNotNull('no_hp')->where('no_hp', '!=', '')->count();
        $totalTerkirim   = WaNotifikasiLog::where('status', 'terkirim')->count();
        $totalGagal      = WaNotifikasiLog::where('status', 'gagal')->count();
        $logsTerbaru     = WaNotifikasiLog::with(['pegawai', 'pengumuman'])->latest()->take(10)->get();

        return view('livewire.konfigurasi-whatsapp-manager', [
            'totalPegawai'    => $totalPegawai,
            'pegawaiDenganHp' => $pegawaiDenganHp,
            'totalTerkirim'   => $totalTerkirim,
            'totalGagal'      => $totalGagal,
            'logsTerbaru'     => $logsTerbaru,
        ])->layout('layouts.app', ['title' => 'Konfigurasi WhatsApp Gateway — N-DesaPresence']);
    }
}
