<div class="space-y-6">

    <!-- Page Header & Tab Controls -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Manajemen Izin & Presensi Manual</h1>
            <p class="text-xs text-slate-500 mt-1">Pusat persetujuan izin, cuti, sakit, dan pencatatan presensi manual kedinasan desa</p>
        </div>

        <!-- Navigation Tabs Switcher -->
        <div class="flex items-center p-1 bg-white border border-[#C9A84C]/30 rounded-2xl shadow-xs self-start md:self-auto">
            <button wire:click="setTab('izin')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 flex items-center gap-2 cursor-pointer {{ $activeTab === 'izin' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'izin' ? 'text-[#E2C268]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Izin, Cuti & Sakit</span>
                @php
                    $pendingCount = $izins->where('status', 'menunggu')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-400 text-slate-900 leading-none">
                        {{ $pendingCount }}
                    </span>
                @endif
            </button>

            <button wire:click="setTab('absen_manual')"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 flex items-center gap-2 cursor-pointer {{ $activeTab === 'absen_manual' ? 'bg-[#064E3B] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 {{ $activeTab === 'absen_manual' ? 'text-[#E2C268]' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Input Presensi Manual</span>
            </button>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: DAFTAR & FORM IZIN / SAKIT / CUTI                               -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if ($activeTab === 'izin')
        @if ($showIzinForm)
            <!-- FORM INLINE: CATAT IZIN / SAKIT (SERAGAM & CLEAN) -->
            <div class="sadi-card p-6 bg-white border border-[#C9A84C]/40 rounded-3xl shadow-sm space-y-5 animate-in fade-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="closeIzinForm"
                                class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer flex items-center gap-1.5 text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            <span>Kembali ke Daftar</span>
                        </button>
                        <div>
                            <h3 class="font-outfit text-base font-extrabold text-[#064E3B]">Formulir Catat Izin & Sakit</h3>
                            <p class="text-[11px] text-slate-500">Pencatatan resmi izin atau sakit oleh Administrator (Langsung Berlaku)</p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="createIzin" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Perangkat Desa / Pegawai <span class="text-red-500">*</span></label>
                        <select wire:model="pegawai_id" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-medium">
                            <option value="">-- Pilih Perangkat Desa --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? 'Perangkat' }})</option>
                            @endforeach
                        </select>
                        @error('pegawai_id') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Izin / Keterangan <span class="text-red-500">*</span></label>
                        <select wire:model="jenis" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-semibold text-slate-800">
                            <option value="izin_pribadi">Izin Keperluan Pribadi</option>
                            <option value="izin_kedinasan">Izin Keperluan Kedinasan</option>
                            <option value="sakit_dengan_surat">Sakit (dengan Surat Dokter)</option>
                            <option value="sakit_tanpa_surat">Sakit (Tanpa Surat Dokter)</option>
                            <option value="cuti_tahunan">Cuti Tahunan Perangkat</option>
                            <option value="duka_cita">Duka Cita Keluarga Inti</option>
                            <option value="melahirkan">Melahirkan</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_mulai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-mono">
                            @error('tanggal_mulai') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_selesai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-mono">
                            @error('tanggal_selesai') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Alasan Lengkap <span class="text-red-500">*</span></label>
                        <textarea wire:model="keterangan" rows="3" placeholder="Jelaskan keperluan izin / kondisi kesehatan..." class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white"></textarea>
                        @error('keterangan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Surat Dokter / Lampiran (Opsional)</label>
                        <input type="file" wire:model="file_lampiran" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#064E3B] file:text-white cursor-pointer">
                        @error('file_lampiran') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeIzinForm" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">Batal</button>
                        <button type="submit" class="btn-sadi-primary px-7 py-2.5 rounded-xl text-xs font-bold text-white transition cursor-pointer shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan & Berlakukan</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- TABEL DAFTAR PENGURUSAN IZIN & SAKIT -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-[#064E3B]">Daftar Pengajuan Izin & Sakit</h3>
                        <p class="text-xs text-slate-500">Pengajuan mandiri staf desa dan pencatatan izin langsung oleh admin</p>
                    </div>
                    <button wire:click="openIzinForm" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Catat Izin / Sakit</span>
                    </button>
                </div>

                <!-- Data Table Izin -->
                <div class="sadi-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-[#064E3B] text-white">
                                <tr>
                                    <th class="py-3 px-4 font-extrabold text-white">Pegawai</th>
                                    <th class="py-3 px-4 font-extrabold text-white">Jenis Izin</th>
                                    <th class="py-3 px-4 font-extrabold text-white">Tanggal & Durasi</th>
                                    <th class="py-3 px-4 font-extrabold text-white">Keterangan</th>
                                    <th class="py-3 px-4 text-center font-extrabold text-[#E2C268]">Status</th>
                                    <th class="py-3 px-4 text-right font-extrabold text-[#E2C268]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white">
                                @forelse ($izins as $i)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 font-bold text-slate-800">
                                            {{ $i->pegawai->nama_lengkap ?? '-' }}
                                            <p class="text-[10px] text-slate-400 font-normal">{{ $i->pegawai->jabatan->nama_jabatan ?? '' }}</p>
                                        </td>
                                        <td class="py-3 px-4">
                                            @if(str_contains($i->jenis, 'sakit'))
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                    {{ ucfirst(str_replace('_', ' ', $i->jenis)) }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                                    {{ ucfirst(str_replace('_', ' ', $i->jenis)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-slate-700 font-mono text-[11px]">
                                            {{ $i->tanggal_mulai->format('d/m/Y') }} — {{ $i->tanggal_selesai->format('d/m/Y') }}
                                            <p class="text-[10px] text-teal-700 font-bold font-sans">({{ $i->jumlah_hari }} Hari)</p>
                                        </td>
                                        <td class="py-3 px-4 max-w-xs text-slate-600">
                                            {{ $i->keterangan }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @switch($i->status)
                                                @case('disetujui')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Disetujui</span>
                                                    @break
                                                @case('menunggu')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">Menunggu</span>
                                                    @break
                                                @default
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-300">Ditolak</span>
                                            @endswitch
                                        </td>
                                        <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                            @if ($i->status === 'menunggu' && (auth()->user()->isAdmin() || auth()->user()->isKades()))
                                                <button wire:click="approve({{ $i->id }})" class="px-3 py-1.5 rounded-lg bg-[#064E3B] text-white font-bold text-[11px] hover:bg-[#04392B] border border-[#064E3B] transition shadow-xs cursor-pointer inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Setujui</span>
                                                </button>
                                                <button wire:click="konfirmasiTolak({{ $i->id }})" class="px-3 py-1.5 rounded-lg bg-rose-600 text-white font-bold text-[11px] hover:bg-rose-700 border border-rose-700 transition shadow-xs cursor-pointer inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Tolak</span>
                                                </button>
                                            @endif
                                            @if ($i->file_lampiran)
                                                <a href="{{ Storage::url($i->file_lampiran) }}" target="_blank" class="p-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition inline-block align-middle cursor-pointer" title="Lihat Surat Dokter / Lampiran">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                            Belum ada riwayat pengajuan izin/sakit.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-t border-slate-100 bg-white">
                        {{ $izins->links() }}
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: INPUT & RIWAYAT ABSEN MANUAL (OVERRIDE)                         -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if ($activeTab === 'absen_manual')
        @if ($showManualForm)
            <!-- FORM INLINE: INPUT PRESENSI MANUAL (SERAGAM & CLEAN) -->
            <div class="sadi-card p-6 bg-white border border-[#C9A84C]/40 rounded-3xl shadow-sm space-y-5 animate-in fade-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="closeManualForm"
                                class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer flex items-center gap-1.5 text-xs font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            <span>Kembali ke Daftar</span>
                        </button>
                        <div>
                            <h3 class="font-outfit text-base font-extrabold text-[#064E3B]">Form Input Presensi Manual Langsung</h3>
                            <p class="text-[11px] text-slate-500">Mencatat kehadiran manual pegawai (Data langsung tersimpan di database kehadiran)</p>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="saveManualAttendance" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Perangkat / Pegawai <span class="text-red-500">*</span></label>
                        <select wire:model="manual_pegawai_id" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white">
                            <option value="">-- Pilih Perangkat Desa --</option>
                            @foreach ($pegawais as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? 'Perangkat' }})</option>
                            @endforeach
                        </select>
                        @error('manual_pegawai_id') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                        <select wire:model.live="manual_status" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-bold text-slate-800">
                            <option value="Hadir">Hadir (Kerja di Kantor / Tugas)</option>
                            <option value="Sakit">Sakit (Langsung Sinkron Multi-Hari)</option>
                            <option value="Izin">Izin (Langsung Sinkron Multi-Hari)</option>
                            <option value="Alpa">Alpa (Tanpa Keterangan)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 md:col-span-2">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="manual_tanggal_mulai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-mono">
                            @error('manual_tanggal_mulai') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                            @error('manual_tanggal') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Selesai (1 s/d Banyak Hari)</label>
                            <input type="date" wire:model="manual_tanggal_selesai" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-mono">
                            @error('manual_tanggal_selesai') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if($manual_status === 'Hadir')
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Masuk (Opsional)</label>
                        <input type="time" wire:model="manual_jam_masuk" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Pulang (Opsional)</label>
                        <input type="time" wire:model="manual_jam_pulang" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white font-mono">
                    </div>
                    @endif

                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan / Justifikasi Admin <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="manual_keterangan" placeholder="Contoh: Sakit rawat jalan, penugasan luar desa, kendala teknis" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C] bg-white">
                        @error('manual_keterangan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeManualForm" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition cursor-pointer">Batal</button>
                        <button type="submit" class="btn-sadi-primary px-7 py-2.5 rounded-xl text-white font-bold text-xs shadow-md transition cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Presensi Manual</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- TABEL RIWAYAT PRESENSI MANUAL ADMIN -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-[#064E3B]">Riwayat Presensi Manual (Input Admin)</h3>
                        <p class="text-xs text-slate-500">Pencatatan presensi manual kedinasan langsung ke database kehadiran</p>
                    </div>
                    <button wire:click="openManualForm" class="btn-sadi-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-bold text-xs tracking-wide shadow-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Input Presensi Manual</span>
                    </button>
                </div>

                <div class="sadi-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#064E3B] text-white font-bold">
                                <tr>
                                    <th class="py-3 px-4 text-white">Tanggal</th>
                                    <th class="py-3 px-4 text-white">Pegawai</th>
                                    <th class="py-3 px-4 text-center text-[#E2C268]">Status</th>
                                    <th class="py-3 px-4 text-center text-white">Jam Kerja</th>
                                    <th class="py-3 px-4 text-white">Alasan / Keterangan</th>
                                    <th class="py-3 px-4 text-white">Diverifikasi Oleh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium bg-white">
                                @forelse ($overrides as $o)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                            {{ $o->tanggal->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-800">
                                            {{ $o->pegawai->nama_lengkap ?? '-' }}
                                            <p class="text-[10px] text-slate-400 font-normal">{{ $o->pegawai->jabatan->nama_jabatan ?? '' }}</p>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @switch($o->status)
                                                @case('Hadir')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Hadir</span>
                                                    @break
                                                @case('Izin')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-300">Izin</span>
                                                    @break
                                                @case('Sakit')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">Sakit</span>
                                                    @break
                                                @default
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-300">Alpa</span>
                                            @endswitch
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono text-[11px] text-slate-600">
                                            @if ($o->jam_masuk || $o->jam_pulang)
                                                {{ $o->jam_masuk ? substr($o->jam_masuk, 0, 5) : '-' }} — {{ $o->jam_pulang ? substr($o->jam_pulang, 0, 5) : '-' }}
                                            @else
                                                <span class="text-slate-400 italic text-[10px]">Tidak diisi</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-slate-600 max-w-xs">
                                            {{ $o->keterangan ?? '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-slate-500 text-[11px]">
                                            {{ $o->verifikator->name ?? 'Admin' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                            Belum ada catatan presensi manual oleh admin.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-t border-slate-100 bg-white">
                        {{ $overrides->links() }}
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL KONFIRMASI REJECT IZIN                                           -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if ($showRejectModal && $selectedIzin)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl overflow-hidden border-2 border-rose-300 p-6 space-y-4">
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center mx-auto text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-outfit font-extrabold text-slate-900 text-base">Tolak Pengajuan Izin/Sakit</h3>
                    <p class="text-xs text-slate-600">Pengajuan dari <strong>{{ $selectedIzin->pegawai->nama_lengkap ?? 'Perangkat' }}</strong> akan ditolak.</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea wire:model="catatanPenolakan" rows="3" placeholder="Tuliskan alasan penolakan izin ini..."
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 outline-none resize-none"></textarea>
                    @error('catatanPenolakan')
                    <p class="text-[11px] text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-2.5">
                    <button wire:click="tutupRejectModal" class="flex-1 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-xs cursor-pointer">Batal</button>
                    <button wire:click="reject" class="flex-[2] py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold transition text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL WARNING / PEMBERITAHUAN SUDAH PRESENSI (GAYA FOTO 2 - CLEAN)     -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @if ($showConflictModal && $conflictInfo)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 animate-in fade-in duration-200">
            <div class="bg-white rounded-3xl max-w-sm w-full shadow-2xl overflow-hidden border border-amber-200 p-6 space-y-4 animate-in zoom-in-95 duration-200">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="font-outfit text-base font-extrabold text-slate-900 leading-snug">{{ $conflictInfo['title'] ?? 'Presensi Sudah Tercatat' }}</h3>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pemberitahuan Sistem Presensi</p>
                    </div>
                </div>

                <!-- Box Detail Penjelasan Simpel -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Perangkat Desa:</span>
                        <span class="font-bold text-slate-800">{{ $conflictInfo['pegawai_nama'] ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tanggal:</span>
                        <span class="font-bold text-slate-800">{{ $conflictInfo['tanggal'] ?? '-' }}</span>
                    </div>
                    <div class="pt-1.5 border-t border-slate-200 text-[11px] text-slate-600 leading-relaxed">
                        {{ $conflictInfo['pesan'] ?? '' }}
                    </div>
                </div>

                <!-- Tombol Action Simple (Orange Button - Text Tutup) -->
                <div class="pt-1">
                    <button type="button" wire:click="closeConflictModal"
                            class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition cursor-pointer flex items-center justify-center gap-1.5">
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
