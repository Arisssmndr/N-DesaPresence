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
    public array $form = [
        'fonnte_api_key'         => '',
        'fonnte_sender_number'   => '',
        'wa_notifikasi_enabled'  => false,
        'wa_template_pengumuman' => '',
        'wa_country_code'        => '62',
    ];

    // Testing modal / state
    public string $testNomorHp = '';
    public string $testPesan = '';
    public ?array $testResult = null;
    public bool $isTesting = false;

    // Device connection status
    public ?array $deviceInfo = null;
    public bool $isCheckingDevice = false;

    protected function rules(): array
    {
        return [
            'form.fonnte_api_key'         => 'nullable|string|max:255',
            'form.fonnte_sender_number'   => 'nullable|string|max:30',
            'form.wa_notifikasi_enabled'  => 'boolean',
            'form.wa_template_pengumuman' => 'required|string',
            'form.wa_country_code'        => 'required|string|max:5',
        ];
    }

    public function mount(KonfigurasiWaService $configService): void
    {
        $this->form = [
            'fonnte_api_key'         => (string) ($configService->get('fonnte_api_key') ?? ''),
            'fonnte_sender_number'   => (string) ($configService->get('fonnte_sender_number') ?? ''),
            'wa_notifikasi_enabled'  => (bool) $configService->get('wa_notifikasi_enabled', false),
            'wa_template_pengumuman' => (string) ($configService->get('wa_template_pengumuman') ?? "📢 *PENGUMUMAN RESMI PEMERINTAH DESA NANGTANG*\n*N-DesaPresence Notification System*\n\n📌 *Kategori:* {kategori}\n🏷️ *Perihal:* {judul}\n\n{isi}\n\n📅 *Berlaku s/d:* {berlaku_hingga}\n👤 *Diumumkan Oleh:* {pembuat}\n\n--------------------------------------------\n_Pesan otomatis dikirim melalui Sistem N-DesaPresence Desa Nangtang (KKN 0226 LP3I Tasikmalaya © 2026)_"),
            'wa_country_code'        => (string) ($configService->get('wa_country_code') ?? '62'),
        ];

        $this->testPesan = "Halo! Ini adalah pesan uji coba (test notification) dari Sistem N-DesaPresence Pemerintah Desa Nangtang menggunakan Gateway Fonnte WhatsApp. Koneksi berhasil aktif!";
    }

    public function simpan(KonfigurasiWaService $configService): void
    {
        $this->validate();

        $configService->set('fonnte_api_key', $this->form['fonnte_api_key'] ?: null, 'Token API Fonnte Resmi (dikelola admin)');
        $configService->set('fonnte_sender_number', $this->form['fonnte_sender_number'] ?: null, 'Nomor WhatsApp Sender Terdaftar di Fonnte');
        $configService->set('wa_notifikasi_enabled', $this->form['wa_notifikasi_enabled'] ? '1' : '0', 'Master Sakelar Aktif/Nonaktif Pengiriman WhatsApp Otomatis');
        $configService->set('wa_template_pengumuman', $this->form['wa_template_pengumuman'], 'Template Pesan WhatsApp untuk Pengumuman Desa');
        $configService->set('wa_country_code', $this->form['wa_country_code'], 'Default Country Code Nomor WhatsApp');

        AuditLog::create([
            'user_id'   => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'role'      => auth()->user()->role ?? 'admin',
            'aktivitas' => 'Memperbarui Konfigurasi Gateway WhatsApp (Fonnte) & Template Notifikasi',
            'modul'     => 'Konfigurasi WhatsApp',
        ]);

        $msg = 'Konfigurasi WhatsApp Gateway berhasil disimpan.';
        session()->flash('success', $msg);
        $this->dispatch('notify', message: $msg, type: 'success');
    }

    public function cekStatusPerangkat(FonnteWhatsAppService $waService): void
    {
        $this->isCheckingDevice = true;
        $this->deviceInfo = $waService->checkDeviceStatus($this->form['fonnte_api_key'] ?: null);
        $this->isCheckingDevice = false;

        if (!empty($this->deviceInfo['device']) && empty($this->form['fonnte_sender_number'])) {
            $this->form['fonnte_sender_number'] = $this->deviceInfo['device'];
        }
    }

    public function testKirim(FonnteWhatsAppService $waService, KonfigurasiWaService $configService): void
    {
        $this->validate([
            'testNomorHp' => 'required|string|min:8|max:20',
            'testPesan'   => 'required|string|min:5',
        ], [
            'testNomorHp.required' => 'Nomor HP tujuan uji coba wajib diisi.',
            'testPesan.required'   => 'Pesan uji coba wajib diisi.',
        ]);

        $this->isTesting = true;

        // Simpan konfigurasi sementara jika user memasukkan API key baru sebelum simpan
        if (!empty($this->form['fonnte_api_key'])) {
            $configService->set('fonnte_api_key', $this->form['fonnte_api_key']);
        }

        $res = $waService->send($this->testNomorHp, $this->testPesan);
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
            $this->dispatch('notify', message: 'Test pesan WhatsApp berhasil dikirim!', type: 'success');
        } else {
            $this->dispatch('notify', message: 'Test pengiriman gagal: ' . ($res['message'] ?? 'Error'), type: 'error');
        }
    }

    public function resetTemplate(): void
    {
        $this->form['wa_template_pengumuman'] = "📢 *PENGUMUMAN RESMI PEMERINTAH DESA NANGTANG*\n*N-DesaPresence Notification System*\n\n📌 *Kategori:* {kategori}\n🏷️ *Perihal:* {judul}\n\n{isi}\n\n📅 *Berlaku s/d:* {berlaku_hingga}\n👤 *Diumumkan Oleh:* {pembuat}\n\n--------------------------------------------\n_Pesan otomatis dikirim melalui Sistem N-DesaPresence Desa Nangtang (KKN 0226 LP3I Tasikmalaya © 2026)_";
        $this->dispatch('notify', message: 'Template pesan dikembalikan ke standar nasional desa.', type: 'info');
    }

    public function render()
    {
        $totalPegawai      = Pegawai::where('status_aktif', true)->count();
        $pegawaiDenganHp   = Pegawai::where('status_aktif', true)->whereNotNull('no_hp')->where('no_hp', '!=', '')->count();
        $totalWaPengumuman = Pengumuman::where('kirim_wa', true)->count();
        $totalTerkirim     = WaNotifikasiLog::where('status', 'terkirim')->count();
        $totalGagal        = WaNotifikasiLog::where('status', 'gagal')->count();
        $logsTerbaru       = WaNotifikasiLog::with(['pegawai', 'pengumuman'])->latest()->take(8)->get();

        return view('livewire.konfigurasi-whatsapp-manager', [
            'totalPegawai'      => $totalPegawai,
            'pegawaiDenganHp'   => $pegawaiDenganHp,
            'totalWaPengumuman' => $totalWaPengumuman,
            'totalTerkirim'     => $totalTerkirim,
            'totalGagal'        => $totalGagal,
            'logsTerbaru'       => $logsTerbaru,
        ])->layout('layouts.app', ['title' => 'Konfigurasi WhatsApp Gateway — N-DesaPresence']);
    }
}
