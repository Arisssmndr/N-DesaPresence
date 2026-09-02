<div class="space-y-5" x-data="pengumumanManagerComponent()">

    <!-- Page Header & Action (Horizontal, Single-Line, No Wrap) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-lg bg-[#064E3B]/10 text-[#064E3B] text-[10.5px] font-extrabold uppercase tracking-wider border border-[#064E3B]/20">Pusat Informasi Desa</span>
                <span class="text-xs text-slate-300">•</span>
                <span class="text-xs font-semibold text-slate-500">Siaran Portal Web & WhatsApp Gateway</span>
            </div>
            <h1 class="font-outfit text-2xl sm:text-3xl font-extrabold text-[#064E3B] tracking-tight mt-1">Pengumuman & Siaran WhatsApp</h1>
            <p class="text-xs text-slate-600 mt-0.5">Kelola informasi kedinasan desa yang tampil di portal staf dan disiarkan langsung ke WhatsApp pegawai.</p>
        </div>

        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
            <a href="{{ route('konfigurasi-wa.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-[#C9A84C]/40 text-[#064E3B] font-bold text-xs hover:bg-[#FAF6F0] transition shadow-xs whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Konfigurasi WhatsApp</span>
            </a>

            <button wire:click="openCreateModal" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white font-extrabold text-xs shadow-md transition whitespace-nowrap cursor-pointer"
                    style="background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%); border: 1px solid #C9A84C;">
                <svg class="w-3.5 h-3.5 text-[#F3E5AB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Pengumuman</span>
            </button>
        </div>
    </div>

    @if (!$isWaConfigured)
        <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-300/80 flex items-center justify-between gap-3 text-xs text-amber-900 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Pemberitahuan Gateway WhatsApp:</strong> Gateway belum aktif. Pengumuman tetap tampil di portal web staf.
                </div>
            </div>
            <a href="{{ route('konfigurasi-wa.index') }}" class="font-bold underline text-[#064E3B] shrink-0 text-xs">Buka Konfigurasi WA →</a>
        </div>
    @endif

    <!-- Data Cards List Pengumuman (Compact, Slim & Unified Tone) -->
    <div class="space-y-3">
        @forelse ($pengumumans as $p)
            <div class="sadi-card p-4 sm:p-4.5 bg-white border {{ $p->is_pinned ? 'border-[#C9A84C] ring-1 ring-[#C9A84C]/30 shadow-sm' : 'border-slate-200/90 shadow-2xs' }} rounded-2xl transition hover:border-[#064E3B]/60 space-y-2.5 {{ $p->kategori_border_bar }}">
                
                <!-- Card Header: Badges & Selaras Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    
                    <!-- Left Badges (Compact) -->
                    <div class="flex flex-wrap items-center gap-1.5 text-[10.5px]">
                        @if ($p->is_pinned)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-extrabold bg-[#FAF6F0] text-[#064E3B] border border-[#C9A84C]">
                                <svg class="w-3 h-3 text-[#C9A84C]" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                <span>DISEMATKAN</span>
                            </span>
                        @endif

                        <span class="px-2.5 py-0.5 rounded-md font-bold uppercase tracking-wider border {{ $p->kategori_badge }}">
                            {{ $p->kategori_label }}
                        </span>

                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold border border-slate-200 flex items-center gap-1">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>{{ $p->target_penerima_label }}</span>
                        </span>

                        @if ($p->berlaku_hingga)
                            <span class="text-amber-900 font-medium bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 flex items-center gap-1">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>s/d {{ \Carbon\Carbon::parse($p->berlaku_hingga)->isoFormat('D MMM Y') }}</span>
                            </span>
                        @endif
                    </div>

                    <!-- Right Action Toolbar (Selaras & Neutral Tone) -->
                    <div class="flex items-center gap-1.5 self-end sm:self-center shrink-0">
                        @if ($isWaConfigured)
                            <button type="button" @click="confirmBroadcastWa({{ $p->id }}, '{{ addslashes($p->judul) }}')" title="Kirim Siaran WhatsApp"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 hover:text-emerald-800 hover:border-emerald-300 text-slate-700 border border-slate-200 transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Kirim WA</span>
                            </button>
                        @endif

                        <button wire:click="togglePin({{ $p->id }})" title="{{ $p->is_pinned ? 'Lepas Pin' : 'Sematkan' }}"
                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                            <span>{{ $p->is_pinned ? 'Unpin' : 'Pin' }}</span>
                        </button>

                        <button wire:click="edit({{ $p->id }})" title="Edit Pengumuman"
                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit</span>
                        </button>

                        <button type="button" @click="confirmDeletePengumuman({{ $p->id }}, '{{ addslashes($p->judul) }}')" title="Hapus Pengumuman"
                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-300 text-slate-700 border border-slate-200 transition text-xs font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500 hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>

                </div>

                <!-- Main Body: Headline & Clean Text Snippet (No Huge Empty Box) -->
                <div>
                    <h3 class="font-outfit text-base sm:text-lg font-bold text-slate-900 leading-snug">
                        {{ $p->judul }}
                    </h3>
                    @if(!empty(trim($p->isi)) && trim($p->isi) !== '-')
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed whitespace-pre-line">
                            {{ $p->isi }}
                        </p>
                    @endif
                </div>

                <!-- Card Footer: WhatsApp Status Pill & Publisher Meta -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100 text-[10.5px] text-slate-400">
                    
                    <div class="flex items-center gap-2">
                        @if ($p->kirim_wa)
                            <button type="button" wire:click="openWaLogs({{ $p->id }})"
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold text-[10.5px] bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 transition cursor-pointer">
                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.554z"/></svg>
                                <span>WhatsApp: {{ $p->total_wa_terkirim }} Terkirim</span>
                                @if ($p->total_wa_gagal > 0)
                                    <span class="text-rose-600 font-extrabold">({{ $p->total_wa_gagal }} Gagal)</span>
                                @endif
                                <span class="text-slate-400 font-normal">→ Log</span>
                            </button>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-medium">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Portal Web Staf</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-1 text-slate-400">
                        <span>{{ $p->created_at ? $p->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }} WIB</span>
                        <span>•</span>
                        <span class="font-medium text-slate-600">Oleh: {{ $p->pembuat->name ?? 'Admin Desa' }}</span>
                    </div>

                </div>

            </div>
        @empty
            <div class="sadi-card p-10 text-center bg-white shadow-xs rounded-2xl space-y-2.5 border border-slate-200">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto text-emerald-700 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h4 class="font-outfit font-bold text-[#064E3B] text-sm">Belum Ada Pengumuman</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Klik tombol "+ Buat Pengumuman" di atas untuk menerbitkan berita atau menyiarkan notifikasi WhatsApp ke seluruh pegawai.</p>
            </div>
        @endforelse

        <div class="pt-2">
            {{ $pengumumans->links() }}
        </div>
    </div>

    <!-- Modal Form Create / Edit (3 Distinct Target Modes & SVG Icons) -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/50">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-white">{{ $pengumumanId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h3>
                        <p class="text-[11px] text-emerald-200/80">Informasi Kedinasan & Siaran Notifikasi WhatsApp</p>
                    </div>
                    <button wire:click="closeModal" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs max-h-[80vh] overflow-y-auto">
                    
                    <!-- Judul Pengumuman -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Pengumuman <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model.defer="judul" placeholder="Contoh: Undangan Rapat Evaluasi APBDes 2026" 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#C9A84C]/50 outline-none">
                        @error('judul') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori (10 Pilihan Lengkap) -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Pengumuman <span class="text-rose-500">*</span></label>
                        <select wire:model.defer="kategori" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] outline-none font-medium">
                            @foreach ($kategoriList as $key => $item)
                                <option value="{{ $key }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Mode Target Penerima (3 Opsi Terpisah) -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <label class="block font-bold text-slate-800 uppercase tracking-wider text-[11px]">
                            Target Penerima
                        </label>

                        <!-- 3 Radio Buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <!-- Opsi 1: Semua -->
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition {{ $mode_target === 'semua' ? 'bg-emerald-50 border-[#064E3B] text-[#064E3B] font-bold shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="mode_target" value="semua" class="text-[#064E3B] focus:ring-0">
                                <span class="text-xs">Semua Staf</span>
                            </label>

                            <!-- Opsi 2: Berdasarkan Bagian -->
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition {{ $mode_target === 'bagian' ? 'bg-emerald-50 border-[#064E3B] text-[#064E3B] font-bold shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="mode_target" value="bagian" class="text-[#064E3B] focus:ring-0">
                                <span class="text-xs">Per Bagian</span>
                            </label>

                            <!-- Opsi 3: Pilih Orang Tertentu -->
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition {{ $mode_target === 'individual' ? 'bg-emerald-50 border-[#064E3B] text-[#064E3B] font-bold shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                <input type="radio" wire:model.live="mode_target" value="individual" class="text-[#064E3B] focus:ring-0">
                                <span class="text-xs">Orang Tertentu</span>
                            </label>
                        </div>

                        <!-- Panel Opsi 2: Dropdown Bagian -->
                        @if ($mode_target === 'bagian')
                            <div class="pt-1">
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Bagian / Divisi:</label>
                                <select wire:model.defer="target_penerima" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:border-[#064E3B] outline-none">
                                    <option value="perangkat_tetap">Perangkat Desa Tetap (Kaur, Kasi, Kadus)</option>
                                    <option value="staf">Staf / Honorer Desa</option>
                                    <option value="bpd">Badan Permusyawaratan Desa (BPD)</option>
                                    <option value="kemasyarakatan">Lembaga Kemasyarakatan (RT, RW, PKK, Karang Taruna)</option>
                                </select>
                            </div>
                        @endif

                        <!-- Panel Opsi 3: Multi-Select Pegawai Individual -->
                        @if ($mode_target === 'individual')
                            <div class="space-y-2 pt-1">
                                <div class="flex items-center justify-between gap-2">
                                    <input type="text" wire:model.live.debounce.200ms="search_pegawai" placeholder="Cari nama pegawai..."
                                        class="px-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white focus:border-[#064E3B] outline-none flex-1">
                                    
                                    <div class="flex items-center gap-1 shrink-0 text-[11px]">
                                        <button type="button" wire:click="selectAllPegawai" class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded font-bold hover:bg-emerald-200">Semua</button>
                                        <button type="button" wire:click="deselectAllPegawai" class="px-2 py-1 bg-slate-200 text-slate-700 rounded font-bold hover:bg-slate-300">Batal</button>
                                    </div>
                                </div>

                                <div class="max-h-44 overflow-y-auto divide-y divide-slate-100 bg-white rounded-xl border border-slate-200 p-1">
                                    @forelse ($pegawaiList as $peg)
                                        <label class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 cursor-pointer text-xs">
                                            <div class="flex items-center gap-2.5">
                                                <input type="checkbox" wire:model.live="selected_pegawai_ids" value="{{ $peg->id }}"
                                                    class="w-4 h-4 text-[#064E3B] rounded border-slate-300 focus:ring-0">
                                                <div>
                                                    <p class="font-bold text-slate-800">{{ $peg->nama_lengkap }}</p>
                                                    <p class="text-[10px] text-slate-500">{{ $peg->jabatan->nama_jabatan ?? 'Perangkat' }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                @if (!empty($peg->no_hp))
                                                    <span class="text-[10px] text-emerald-700 font-mono bg-emerald-50 px-1.5 py-0.5 rounded">WA: {{ substr($peg->no_hp, 0, 4) }}...</span>
                                                @else
                                                    <span class="text-[10px] text-slate-400 italic">No HP (-)</span>
                                                @endif
                                            </div>
                                        </label>
                                    @empty
                                        <div class="p-3 text-center text-slate-400 text-xs">
                                            Tidak ada pegawai yang sesuai pencarian.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-slate-600 px-1">
                                    <span>Penerima Terpilih:</span>
                                    <strong class="text-[#064E3B] font-bold">{{ count($selected_pegawai_ids) }} Orang</strong>
                                </div>
                                @error('selected_pegawai_ids') <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    <!-- Isi Pesan Pengumuman -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Isi Pesan Pengumuman <span class="text-rose-500">*</span></label>
                        <textarea wire:model.defer="isi" rows="4" placeholder="Tuliskan detail pengumuman, agenda, instruksi, atau arahan kerja di sini..." 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#C9A84C]/50 outline-none leading-relaxed"></textarea>
                        @error('isi') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 items-center">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Berlaku Hingga (Opsional)</label>
                            <input type="date" wire:model.defer="berlaku_hingga" class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] outline-none">
                        </div>

                        <div class="pt-2 sm:pt-5">
                            <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                <input type="checkbox" wire:model.defer="is_pinned" class="w-4 h-4 text-[#064E3B] rounded border-[#C9A84C]">
                                <span class="font-bold text-slate-700 text-xs">Sematkan di Atas (Pin)</span>
                            </label>
                        </div>
                    </div>

                    <!-- WhatsApp Notification Broadcast Selector -->
                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-300/80 space-y-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.defer="kirim_wa" class="w-5 h-5 text-emerald-700 rounded border-[#C9A84C] mt-0.5">
                            <div>
                                <span class="font-outfit font-extrabold text-[#064E3B] text-xs block">
                                    Kirim Siaran ke WhatsApp Penerima
                                </span>
                                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                                    Pesan pengumuman ini akan langsung dikirimkan ke nomor WhatsApp target saat tombol Simpan ditekan.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-extrabold text-white shadow-md transition cursor-pointer"
                            style="background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%); border: 1px solid #C9A84C;">
                            Simpan & Terbitkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal WhatsApp Delivery Logs -->
    @if ($showWaLogModal && $selectedPengumuman)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/50">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-white">Log Pengiriman WhatsApp</h3>
                        <p class="text-[11px] text-emerald-200/80 truncate max-w-md">{{ $selectedPengumuman->judul }}</p>
                    </div>
                    <button wire:click="closeWaLogs" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between bg-[#FAF6F0] p-3.5 rounded-2xl border border-[#C9A84C]/20 text-xs">
                        <div>
                            <span class="font-bold text-slate-700">Total Terkirim:</span> 
                            <strong class="text-emerald-700">{{ $selectedPengumuman->total_wa_terkirim }}</strong>
                        </div>
                        <div>
                            <span class="font-bold text-slate-700">Gagal / Dilewati:</span> 
                            <strong class="text-rose-700">{{ $selectedPengumuman->total_wa_gagal }}</strong>
                        </div>
                        <div>
                            <span class="font-bold text-slate-700">Waktu Terakhir:</span> 
                            <span class="font-mono text-slate-600">{{ $selectedPengumuman->wa_terkirim_at ? $selectedPengumuman->wa_terkirim_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>

                    <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 border border-slate-100 rounded-xl">
                        @forelse ($selectedPengumuman->waLogs as $log)
                            <div class="p-3 text-xs flex items-center justify-between gap-3 hover:bg-slate-50">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $log->nama_penerima ?: ($log->pegawai->nama_lengkap ?? 'Perangkat') }}</p>
                                    <p class="text-[11px] font-mono text-slate-500">{{ $log->no_hp }} • Percobaan: {{ $log->percobaan }}x</p>
                                </div>
                                <div class="text-right">
                                    @if ($log->status === 'terkirim')
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                            ✓ TERKIRIM
                                        </span>
                                    @elseif ($log->status === 'pending')
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">
                                            ⏳ PENDING
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold" title="{{ $log->error_message }}">
                                            ✕ GAGAL
                                        </span>
                                    @endif
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $log->updated_at ? $log->updated_at->format('H:i:s') : '-' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs">
                                Belum ada rincian log antrian untuk pengumuman ini.
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="button" wire:click="closeWaLogs" class="px-5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Alpine + SweetAlert2 Helper Component -->
<script>
    function pengumumanManagerComponent() {
        return {
            confirmBroadcastWa(id, judul) {
                if (window.Swal) {
                    Swal.fire({
                        title: 'Kirim Siaran WhatsApp?',
                        text: `Kirimkan pengumuman "${judul}" langsung ke WhatsApp seluruh perangkat/staf terkait?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#064E3B',
                        cancelButtonColor: '#64748B',
                        confirmButtonText: 'Ya, Kirim Sekarang',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl border border-slate-200'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.call('broadcastWaManual', id);
                        }
                    });
                } else {
                    @this.call('broadcastWaManual', id);
                }
            },

            confirmDeletePengumuman(id, judul) {
                if (window.Swal) {
                    Swal.fire({
                        title: 'Hapus Pengumuman?',
                        text: `Pengumuman "${judul}" akan dihapus permanen dari portal staf dan sistem.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#E11D48',
                        cancelButtonColor: '#64748B',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-3xl shadow-2xl border border-slate-200'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.call('delete', id);
                        }
                    });
                } else {
                    @this.call('delete', id);
                }
            }
        };
    }
</script>
