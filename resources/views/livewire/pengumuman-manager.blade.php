<div class="space-y-6">

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">Pusat Informasi Desa</span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs font-medium text-slate-500">Multi-Channel Broadcast (In-App & WhatsApp)</span>
            </div>
            <h1 class="font-outfit text-2xl font-extrabold text-[#064E3B] tracking-tight mt-1">Pengumuman & Siaran WhatsApp</h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Kelola informasi resmi pemerintahan desa yang tampil di portal staf dan disiarkan langsung ke WhatsApp pegawai.</p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('konfigurasi-wa.index') }}" 
               class="px-3.5 py-2.5 rounded-xl bg-white border border-[#C9A84C]/40 text-[#064E3B] font-bold text-xs hover:bg-[#FAF6F0] transition shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Kelola WhatsApp Gateway</span>
            </a>

            <button wire:click="openCreateModal" class="btn-sadi-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md transition cursor-pointer"
                    style="background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%); border: 1px solid #C9A84C;">
                <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Pengumuman Baru</span>
            </button>
        </div>
    </div>

    @if (!$isWaConfigured)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300/80 flex items-center justify-between gap-3 text-xs text-amber-900 shadow-xs">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Pemberitahuan Gateway WhatsApp:</strong> Token Fonnte belum dikonfigurasi atau dinonaktifkan. Pengumuman tetap tampil di portal web staf, namun siaran otomatis WA tidak akan terkirim.
                </div>
            </div>
            <a href="{{ route('konfigurasi-wa.index') }}" class="font-bold underline text-[#064E3B] shrink-0">Setup Sekarang →</a>
        </div>
    @endif

    <!-- Data Cards Compact List -->
    <div class="space-y-3.5">
        @forelse ($pengumumans as $p)
            @php
                $kategoriStyle = match($p->kategori) {
                    'penting' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'rapat' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'kegiatan' => 'bg-amber-50 text-amber-800 border-amber-200',
                    default => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                };
            @endphp
            <div class="sadi-card p-5 bg-white border {{ $p->is_pinned ? 'border-[#C9A84C] ring-1 ring-[#C9A84C]/30 shadow-md' : 'border-slate-200/80 shadow-xs' }} rounded-2xl transition hover:border-[#064E3B]">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                    
                    {{-- Content --}}
                    <div class="space-y-2 flex-1 min-w-0">
                        {{-- Meta Baris Atas --}}
                        <div class="flex flex-wrap items-center gap-2 text-[11px]">
                            @if ($p->is_pinned)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md font-extrabold bg-[#FAF6F0] text-[#064E3B] border border-[#C9A84C]">
                                    <svg class="w-3 h-3 text-[#C9A84C]" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                    <span>PINNED</span>
                                </span>
                            @endif

                            <span class="px-2.5 py-0.5 rounded-md font-bold uppercase tracking-wider border {{ $kategoriStyle }}">
                                {{ ucfirst($p->kategori) }}
                            </span>

                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                                Target: {{ $p->target_penerima_label }}
                            </span>

                            <span class="text-slate-400 font-medium">
                                {{ $p->created_at ? $p->created_at->isoFormat('D MMMM Y, HH:mm') : '-' }} WIB
                            </span>

                            @if ($p->berlaku_hingga)
                                <span class="text-amber-800 font-semibold bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                    s/d {{ \Carbon\Carbon::parse($p->berlaku_hingga)->isoFormat('D MMM Y') }}
                                </span>
                            @endif

                            {{-- WA Status Pill --}}
                            @if ($p->kirim_wa)
                                <button type="button" wire:click="openWaLogs({{ $p->id }})"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md font-bold text-[10px] bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 transition cursor-pointer">
                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.554z"/></svg>
                                    <span>WA: {{ $p->total_wa_terkirim }} Terkirim</span>
                                    @if ($p->total_wa_gagal > 0)
                                        <span class="text-rose-600 font-extrabold">({{ $p->total_wa_gagal }} Gagal)</span>
                                    @endif
                                </button>
                            @else
                                <span class="text-slate-400 text-[10px] italic">In-App Only</span>
                            @endif
                        </div>

                        {{-- Judul & Pesan --}}
                        <h3 class="font-outfit text-base font-bold text-[#064E3B]">
                            {{ $p->judul }}
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                            {{ $p->isi }}
                        </p>
                    </div>

                    {{-- Actions Ringkas --}}
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 w-full md:w-auto justify-end">
                        
                        @if ($isWaConfigured)
                            <button wire:click="broadcastWaManual({{ $p->id }})" title="Kirim / Ulangi Siaran WhatsApp" wire:confirm="Kirimkan siaran pengumuman ini ke seluruh WhatsApp pegawai terkait?"
                                    class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 transition text-xs font-bold flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span class="hidden sm:inline">Kirim WA</span>
                            </button>
                        @endif

                        <button wire:click="togglePin({{ $p->id }})" title="{{ $p->is_pinned ? 'Lepas Pin' : 'Sematkan di Atas' }}"
                                class="p-2 rounded-xl {{ $p->is_pinned ? 'bg-[#FAF6F0] text-[#064E3B] border border-[#C9A84C]' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} transition text-xs font-bold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 {{ $p->is_pinned ? 'text-[#C9A84C]' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                            <span class="hidden sm:inline">{{ $p->is_pinned ? 'Unpin' : 'Pin' }}</span>
                        </button>

                        <button wire:click="edit({{ $p->id }})" class="p-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition text-xs font-bold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span class="hidden sm:inline">Edit</span>
                        </button>

                        <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus pengumuman ini secara permanen?" class="p-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition text-xs font-bold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </div>

                </div>
            </div>
        @empty
            <div class="sadi-card p-12 text-center bg-white shadow-sm rounded-2xl space-y-2 border border-slate-200">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto text-emerald-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h4 class="font-outfit font-bold text-[#064E3B] text-base">Belum Ada Pengumuman</h4>
                <p class="text-xs text-slate-500">Klik tombol "Buat Pengumuman Baru" di atas untuk menerbitkan informasi atau mengirim siaran WhatsApp.</p>
            </div>
        @endforelse

        <div class="pt-2">
            {{ $pengumumans->links() }}
        </div>
    </div>

    <!-- Modal Form Create / Edit -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-[#C9A84C]/40 my-8">
                <div class="px-6 py-4 bg-[#064E3B] text-white flex items-center justify-between border-b border-[#C9A84C]/50">
                    <div>
                        <h3 class="font-outfit text-base font-bold text-white">{{ $pengumumanId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h3>
                        <p class="text-[11px] text-emerald-200/80">Informasi Kedinasan & Notifikasi Perangkat</p>
                    </div>
                    <button wire:click="closeModal" class="p-1.5 rounded-lg hover:bg-emerald-800 text-emerald-200 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Pengumuman <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model.defer="judul" placeholder="Contoh: Undangan Rapat Evaluasi APBDes 2026" 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#C9A84C]/50 outline-none">
                        @error('judul') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori</label>
                            <select wire:model.defer="kategori" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] outline-none">
                                <option value="informasi">ℹ️ Informasi Kedinasan</option>
                                <option value="rapat">🏛️ Undangan Rapat</option>
                                <option value="kegiatan">📅 Kegiatan Desa</option>
                                <option value="penting">🚨 Penting / Mendesak</option>
                            </select>
                            @error('kategori') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Penerima</label>
                            <select wire:model.defer="target_penerima" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] outline-none">
                                <option value="semua">Semua Perangkat & Staf</option>
                                <option value="perangkat_tetap">Perangkat Desa Tetap</option>
                                <option value="staf">Staf / Honorer Desa</option>
                                <option value="bpd">Badan Permusyawaratan Desa (BPD)</option>
                                <option value="kemasyarakatan">Lembaga Kemasyarakatan</option>
                            </select>
                            @error('target_penerima') <span class="text-[11px] text-rose-600 font-semibold mt-0.5 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1.5">Isi Pesan Pengumuman <span class="text-rose-500">*</span></label>
                        <textarea wire:model.defer="isi" rows="4" placeholder="Tuliskan detail arahan, jadwal pelaksanaan, tempat, atau instruksi kerja..." 
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:border-[#064E3B] focus:ring-2 focus:ring-[#C9A84C]/50 outline-none"></textarea>
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
                                    Kirim Siaran ke WhatsApp Masing-masing Pegawai
                                </span>
                                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                                    Pesan pengumuman ini akan otomatis dikirimkan ke nomor WhatsApp pegawai target via Fonnte Gateway. Kosongkan jika hanya ingin tampil di akun/portal web.
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
