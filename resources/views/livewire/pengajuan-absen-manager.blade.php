<div class="p-6 space-y-6">

    {{-- Flash --}}
    @if (session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border-2 border-emerald-300 rounded-2xl shadow-sm">
        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-emerald-900 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-outfit font-extrabold text-[#064E3B]">Pengajuan Absen Luar</h1>
            <p class="text-slate-500 text-sm mt-1">Verifikasi pengajuan kehadiran di luar kantor dari perangkat desa</p>
        </div>
        @if($totalMenunggu > 0)
        <div class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border-2 border-amber-300 rounded-xl">
            <div class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></div>
            <span class="text-sm font-extrabold text-amber-900">{{ $totalMenunggu }} pengajuan menunggu persetujuan</span>
        </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wider">Status</label>
            <select wire:model.live="filterStatus"
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none bg-white">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wider">Kategori / Jenis</label>
            <select wire:model.live="filterJenis"
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none bg-white font-medium">
                <option value="">Semua Kategori</option>
                <option value="dinas_luar_undangan">Dinas Luar (Undangan)</option>
                <option value="dinas_luar_pengajuan">Dinas Luar (Pengajuan Mandiri)</option>
                <option value="dinas_luar_surat_tugas">Dinas Luar (Surat Tugas / SPT)</option>
                <option value="kegiatan_sosial">Kegiatan Sosial</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wider">Tanggal</label>
            <input type="date" wire:model.live="filterTanggal"
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1 uppercase tracking-wider">Cari Nama</label>
            <input type="text" wire:model.live.debounce.300ms="filterCari" placeholder="Nama pegawai..."
                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
        </div>
    </div>

    {{-- Table --}}
    <div class="sadi-card overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider whitespace-nowrap">Pegawai</th>
                        <th class="px-4 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider">Judul & Detail</th>
                        <th class="px-4 py-3.5 text-left text-xs font-extrabold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-extrabold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengajuans as $p)
                    <tr class="hover:bg-slate-50 transition-colors {{ $p->status === 'menunggu' ? 'bg-amber-50/40' : '' }}">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2.5">
                                @php $foto = $p->pegawai->foto_profil ?? $p->user->foto_profil ?? null; @endphp
                                @if($foto)
                                    <img src="{{ asset('storage/' . $foto) }}" class="w-8 h-8 rounded-xl object-cover border border-[#C9A84C]/50">
                                @else
                                    <div class="w-8 h-8 rounded-xl bg-[#064E3B] text-[#C9A84C] flex items-center justify-center text-xs font-extrabold">
                                        {{ strtoupper(substr($p->pegawai->nama_lengkap ?? 'X', 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-800 text-xs">{{ $p->pegawai->nama_lengkap }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $p->pegawai->jabatan->nama_jabatan ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-xs font-bold text-slate-700 whitespace-nowrap">
                            {{ $p->tanggal->isoFormat('D MMM Y') }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $p->jenis_badge_class }}">
                                {{ $p->label_jenis }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-xs text-slate-700 max-w-xs">
                            <p class="font-bold text-slate-800 truncate">{{ $p->judul }}</p>
                            @if($p->instansi_pengundang)
                            <p class="text-[10.5px] text-indigo-700 font-semibold truncate">🏛️ {{ $p->instansi_pengundang }}</p>
                            @endif
                            @if($p->nomor_surat_tugas)
                            <p class="text-[10.5px] text-blue-700 font-semibold truncate">📜 SPT: {{ $p->nomor_surat_tugas }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full border {{ $p->badge_class }}">
                                @if($p->status === 'menunggu')
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Menunggu
                                    </span>
                                @else
                                    {{ $p->label_status }}
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="lihatDetail({{ $p->id }})"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @if($p->status === 'menunggu')
                                <button wire:click="konfirmasiSetujui({{ $p->id }})"
                                    class="p-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-700 transition" title="Setujui">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button wire:click="konfirmasiTolak({{ $p->id }})"
                                    class="p-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 transition" title="Tolak">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-slate-400 text-sm font-semibold">Tidak ada pengajuan yang ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengajuans->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $pengajuans->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════ MODAL: DETAIL ═══════════ --}}
    @if($showModal && $selected)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" wire:click.self="tutupModal()">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="p-5 border-b border-slate-100 flex items-start justify-between sticky top-0 bg-white rounded-t-3xl z-10">
                <div>
                    <h3 class="font-outfit font-extrabold text-[#064E3B] text-base">Detail Pengajuan Kehadiran Luar</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $selected->pegawai->nama_lengkap ?? '—' }} · {{ $selected->tanggal->isoFormat('D MMMM Y') }}</p>
                </div>
                <button wire:click="tutupModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 space-y-4 text-xs">
                {{-- Status & Kategori Badge --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-extrabold px-3 py-1.5 rounded-full border {{ $selected->badge_class }} text-[11px]">
                        {{ $selected->label_status }}
                    </span>
                    <span class="text-[11px] font-bold px-3 py-1.5 rounded-full border {{ $selected->jenis_badge_class }}">
                        {{ $selected->label_jenis }}
                    </span>
                </div>

                {{-- Info Pegawai --}}
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider">Data Pengaju</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div><span class="text-slate-400">Nama</span><br><span class="font-bold text-slate-800">{{ $selected->pegawai->nama_lengkap }}</span></div>
                        <div><span class="text-slate-400">Jabatan</span><br><span class="font-bold text-slate-800">{{ $selected->pegawai->jabatan->nama_jabatan ?? '—' }}</span></div>
                        <div><span class="text-slate-400">Tanggal Absen</span><br><span class="font-bold text-slate-800">{{ $selected->tanggal->isoFormat('D MMMM Y') }}</span></div>
                        <div><span class="text-slate-400">Diajukan</span><br><span class="font-bold text-slate-800">{{ $selected->created_at->isoFormat('D MMM Y HH:mm') }}</span></div>
                    </div>
                </div>

                {{-- Detail Tambahan: Instansi Pengundang / Nomor SPT --}}
                @if($selected->instansi_pengundang)
                <div class="p-3 bg-indigo-50/70 rounded-2xl border border-indigo-200">
                    <p class="text-[10px] font-bold text-indigo-900 uppercase tracking-wider">Instansi / Pihak Pengundang</p>
                    <p class="text-xs font-extrabold text-indigo-950 mt-0.5">{{ $selected->instansi_pengundang }}</p>
                </div>
                @endif

                @if($selected->nomor_surat_tugas)
                <div class="p-3 bg-blue-50/70 rounded-2xl border border-blue-200">
                    <p class="text-[10px] font-bold text-blue-900 uppercase tracking-wider">Nomor Surat Perintah Tugas (SPT)</p>
                    <p class="text-xs font-extrabold text-blue-950 font-mono mt-0.5">{{ $selected->nomor_surat_tugas }}</p>
                </div>
                @endif

                {{-- Judul & Deskripsi --}}
                <div class="space-y-1">
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider">Judul Kegiatan</p>
                    <p class="font-bold text-slate-800 text-sm">{{ $selected->judul }}</p>
                </div>

                <div class="space-y-1">
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider">Uraian / Keterangan</p>
                    <p class="text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200 leading-relaxed">{{ $selected->deskripsi }}</p>
                </div>
                {{-- Koordinat GPS --}}
                @if($selected->latitude && $selected->longitude)
                <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-1.5">
                    <p class="font-extrabold text-emerald-900 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Validasi Lokasi GPS Staf</span>
                    </p>
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono text-xs font-bold text-emerald-950">{{ $selected->latitude }}, {{ $selected->longitude }}</span>
                        <a href="https://maps.google.com/?q={{ $selected->latitude }},{{ $selected->longitude }}" target="_blank"
                           class="px-3 py-1.5 rounded-xl bg-emerald-700 text-white font-extrabold text-[11px] hover:bg-emerald-800 transition flex items-center gap-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Foto Lokasi --}}
                @if($selected->foto_lokasi)
                <div>
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider mb-2">Foto Bukti Lokasi</p>
                    <img src="{{ asset('storage/' . $selected->foto_lokasi) }}" class="w-full rounded-2xl border-2 border-[#C9A84C] object-cover max-h-64" alt="Foto Bukti">
                </div>
                @endif

                {{-- File Dokumen --}}
                @if($selected->file_dokumen)
                <div>
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider mb-2">Dokumen Pendukung</p>
                    <a href="{{ asset('storage/' . $selected->file_dokumen) }}" target="_blank"
                       class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl hover:border-[#064E3B] transition">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="font-bold text-[#064E3B]">Buka Dokumen ({{ strtoupper(pathinfo($selected->file_dokumen, PATHINFO_EXTENSION)) }})</span>
                    </a>
                </div>
                @endif

                {{-- Tanda Tangan --}}
                @if($selected->tanda_tangan_src)
                <div>
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider mb-2">Tanda Tangan Digital Pengaju</p>
                    <div class="bg-slate-50 border-2 border-[#C9A84C] rounded-2xl p-3 flex items-center justify-center">
                        <img src="{{ $selected->tanda_tangan_src }}" class="max-h-32 w-auto" alt="Tanda Tangan">
                    </div>
                </div>
                @endif

                {{-- Catatan Admin (jika sudah diproses) --}}
                @if($selected->catatan_admin)
                <div class="p-3 rounded-xl border
                    {{ $selected->status === 'ditolak' ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }}">
                    <p class="font-extrabold {{ $selected->status === 'ditolak' ? 'text-red-800' : 'text-emerald-800' }} uppercase tracking-wider mb-1">Catatan Admin</p>
                    <p class="{{ $selected->status === 'ditolak' ? 'text-red-700' : 'text-emerald-700' }}">{{ $selected->catatan_admin }}</p>
                </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            @if($selected->status === 'menunggu')
            <div class="p-5 border-t border-slate-100 flex gap-3 sticky bottom-0 bg-white rounded-b-3xl">
                <button wire:click="konfirmasiTolak({{ $selected->id }})"
                    class="flex-1 py-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 font-bold border border-red-200 transition">
                    Tolak Pengajuan
                </button>
                <button wire:click="konfirmasiSetujui({{ $selected->id }})"
                    class="flex-[2] btn-sadi-primary py-3 rounded-xl text-white font-bold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Setujui & Catat Kehadiran
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════ MODAL: KONFIRMASI SETUJUI ═══════════ --}}
    @if($showApproveModal && $selected)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 space-y-4">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 border-2 border-emerald-300 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-outfit font-extrabold text-slate-800 text-base">Setujui Pengajuan</h3>
                <p class="text-xs text-slate-500">Pengajuan <strong>{{ $selected->judul }}</strong> oleh <strong>{{ $selected->pegawai->nama_lengkap ?? '—' }}</strong> akan disetujui dan kehadiran otomatis tercatat sebagai <strong>Dinas Luar</strong>.</p>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Admin (Opsional)</label>
                <textarea wire:model="catatanAdmin" rows="2" placeholder="Catatan tambahan untuk staf (opsional)..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none resize-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button wire:click="tutupModal()" class="flex-1 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition text-sm">Batal</button>
                <button wire:click="setujui()" class="flex-[2] py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Setujui Sekarang
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════ MODAL: KONFIRMASI TOLAK ═══════════ --}}
    @if($showRejectModal && $selected)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6 space-y-4">
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-red-100 border-2 border-red-300 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="font-outfit font-extrabold text-slate-800 text-base">Tolak Pengajuan</h3>
                <p class="text-xs text-slate-500">Pengajuan <strong>{{ $selected->judul }}</strong> oleh <strong>{{ $selected->pegawai->nama_lengkap ?? '—' }}</strong> akan ditolak. Staf dapat melihat alasan penolakan.</p>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea wire:model="catatanAdmin" rows="3" placeholder="Jelaskan alasan penolakan pengajuan ini (wajib diisi)..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none resize-none"></textarea>
                @error('catatanAdmin')
                <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button wire:click="tutupModal()" class="flex-1 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition text-sm">Batal</button>
                <button wire:click="tolak()" class="flex-[2] py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak Pengajuan
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
