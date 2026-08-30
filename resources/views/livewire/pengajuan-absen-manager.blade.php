<div class="p-6 space-y-6">

    {{-- Flash Message --}}
    @if (session('success'))
    <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-300 rounded-xl shadow-xs">
        <div class="w-7 h-7 rounded-lg bg-[#064E3B] text-[#E2C268] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="text-slate-900 text-xs font-bold">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-outfit font-extrabold text-[#064E3B]">Pengajuan Absen Luar</h1>
            <p class="text-slate-600 text-xs mt-1">Verifikasi pengajuan kehadiran di luar kantor dari perangkat desa</p>
        </div>
        @if($totalMenunggu > 0)
        <div class="flex items-center gap-2 px-3.5 py-2 bg-white border border-[#C9A84C]/60 rounded-xl shadow-xs">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            <span class="text-xs font-extrabold text-[#064E3B]">{{ $totalMenunggu }} pengajuan menunggu persetujuan</span>
        </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
        <div>
            <label class="block text-[11px] font-extrabold text-slate-700 mb-1 uppercase tracking-wider">Status</label>
            <select wire:model.live="filterStatus"
                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none bg-white font-semibold cursor-pointer">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-extrabold text-slate-700 mb-1 uppercase tracking-wider">Kategori / Jenis</label>
            <select wire:model.live="filterJenis"
                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none bg-white font-semibold cursor-pointer">
                <option value="">Semua Kategori</option>
                <option value="dinas_luar_undangan">Dinas Luar (Undangan)</option>
                <option value="dinas_luar_pengajuan">Dinas Luar (Pengajuan Mandiri)</option>
                <option value="dinas_luar_surat_tugas">Dinas Luar (Surat Tugas / SPT)</option>
                <option value="kegiatan_sosial">Kegiatan Sosial</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-extrabold text-slate-700 mb-1 uppercase tracking-wider">Tanggal</label>
            <input type="date" wire:model.live="filterTanggal"
                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none cursor-pointer">
        </div>
        <div>
            <label class="block text-[11px] font-extrabold text-slate-700 mb-1 uppercase tracking-wider">Cari Nama</label>
            <input type="text" wire:model.live.debounce.300ms="filterCari" placeholder="Cari nama pegawai..."
                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
        </div>
    </div>

    {{-- Table --}}
    <div class="sadi-card overflow-hidden bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead class="bg-[#064E3B] text-white">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-xs font-extrabold text-white border-r border-emerald-800 w-[22%]">PEGAWAI</th>
                        <th class="px-3 py-2.5 text-left text-xs font-extrabold text-white border-r border-emerald-800 w-[12%]">TANGGAL</th>
                        <th class="px-3 py-2.5 text-left text-xs font-extrabold text-white border-r border-emerald-800 w-[18%]">KATEGORI</th>
                        <th class="px-3 py-2.5 text-left text-xs font-extrabold text-white border-r border-emerald-800 w-[24%]">JUDUL & DETAIL</th>
                        <th class="px-3 py-2.5 text-center text-xs font-extrabold text-[#E2C268] border-r border-emerald-800 w-[10%]">STATUS</th>
                        <th class="px-3 py-2.5 text-center text-xs font-extrabold text-[#E2C268] w-[14%]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($pengajuans as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $p->status === 'menunggu' ? 'bg-amber-50/20' : '' }}">
                        <td class="px-3 py-2.5 border-r border-slate-100">
                            <div class="flex items-center gap-2">
                                @php $foto = $p->pegawai->foto_profil ?? $p->user->foto_profil ?? null; @endphp
                                @if($foto)
                                    <img src="{{ asset('storage/' . $foto) }}" class="w-7 h-7 rounded-lg object-cover border border-slate-300 shrink-0">
                                @else
                                    <div class="w-7 h-7 rounded-lg bg-[#064E3B] text-[#E2C268] flex items-center justify-center text-[10px] font-extrabold shrink-0">
                                        {{ strtoupper(substr($p->pegawai->nama_lengkap ?? 'X', 0, 2)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 text-xs truncate">{{ $p->pegawai->nama_lengkap }}</p>
                                    <p class="text-[10px] text-slate-500 truncate">{{ $p->pegawai->jabatan->nama_jabatan ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2.5 text-xs font-bold text-slate-800 whitespace-nowrap border-r border-slate-100">
                            {{ $p->tanggal->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-3 py-2.5 border-r border-slate-100">
                            <span class="text-[10.5px] font-semibold px-2 py-0.5 rounded-md border inline-block leading-tight {{ $p->jenis_badge_class }}">
                                {{ $p->label_jenis }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 text-xs text-slate-700 border-r border-slate-100">
                            <p class="font-bold text-slate-900 truncate">{{ $p->judul }}</p>
                            @if($p->instansi_pengundang)
                            <p class="text-[10px] text-slate-600 font-medium truncate">Instansi: {{ $p->instansi_pengundang }}</p>
                            @endif
                            @if($p->nomor_surat_tugas)
                            <p class="text-[10px] text-slate-600 font-mono truncate">SPT: {{ $p->nomor_surat_tugas }}</p>
                            @endif
                            @if($p->latitude && $p->longitude)
                            <p class="text-[9.5px] text-emerald-800 font-semibold flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="truncate">{{ $p->label_sumber_koordinat }}</span>
                            </p>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 whitespace-nowrap text-center border-r border-slate-100">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md border inline-block {{ $p->badge_class }}">
                                {{ $p->label_status }}
                            </span>
                        </td>
                        <td class="px-3 py-2.5 whitespace-nowrap text-center">
                            <div class="inline-flex items-center justify-center gap-1">
                                <button wire:click="lihatDetail({{ $p->id }})"
                                    class="h-7 px-2 rounded-md bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 transition shadow-2xs inline-flex items-center gap-1 text-[11px] font-bold cursor-pointer" title="Lihat Detail">
                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                @if($p->status === 'menunggu')
                                <button wire:click="konfirmasiSetujui({{ $p->id }})"
                                    class="h-7 px-2 rounded-md bg-[#064E3B] text-white hover:bg-[#04392B] border border-[#064E3B] transition shadow-2xs inline-flex items-center gap-1 text-[11px] font-bold cursor-pointer" title="Setujui">
                                    <svg class="w-3 h-3 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Setujui</span>
                                </button>
                                <button wire:click="konfirmasiTolak({{ $p->id }})"
                                    class="h-7 px-2 rounded-md bg-white text-red-700 hover:bg-red-50 border border-red-200 transition shadow-2xs inline-flex items-center gap-1 text-[11px] font-bold cursor-pointer" title="Tolak">
                                    <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Tolak</span>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            Tidak ada pengajuan absen luar yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengajuans->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 bg-white">
            {{ $pengajuans->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════ MODAL: DETAIL ═══════════ --}}
    @if($showModal && $selected)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" wire:click.self="tutupModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border-2 border-[#C9A84C]">
            {{-- Modal Header --}}
            <div class="p-4 bg-[#064E3B] text-white flex items-start justify-between sticky top-0 rounded-t-2xl z-10 border-b border-[#C9A84C]">
                <div>
                    <h3 class="font-outfit font-extrabold text-[#E2C268] text-base">Detail Pengajuan Absen Luar</h3>
                    <p class="text-xs text-slate-300 mt-0.5">{{ $selected->pegawai->nama_lengkap ?? '—' }} · {{ $selected->tanggal->translatedFormat('d F Y') }}</p>
                </div>
                <button wire:click="tutupModal()" class="text-slate-300 hover:text-white text-lg font-bold p-1 cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-5 space-y-4 text-xs bg-white">
                {{-- Status & Kategori Badge --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold px-3 py-1 rounded-md border {{ $selected->badge_class }} text-xs">
                        {{ $selected->label_status }}
                    </span>
                    <span class="text-xs font-semibold px-3 py-1 rounded-md border {{ $selected->jenis_badge_class }}">
                        {{ $selected->label_jenis }}
                    </span>
                </div>

                {{-- Info Pegawai --}}
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider">Data Pengaju</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div><span class="text-slate-500">Nama</span><br><span class="font-bold text-slate-900">{{ $selected->pegawai->nama_lengkap }}</span></div>
                        <div><span class="text-slate-500">Jabatan</span><br><span class="font-bold text-slate-900">{{ $selected->pegawai->jabatan->nama_jabatan ?? '—' }}</span></div>
                        <div><span class="text-slate-500">Tanggal Absen</span><br><span class="font-bold text-slate-900">{{ $selected->tanggal->translatedFormat('d F Y') }}</span></div>
                        <div><span class="text-slate-500">Diajukan</span><br><span class="font-bold text-slate-900">{{ $selected->created_at->translatedFormat('d M Y, H:i') }}</span></div>
                    </div>
                </div>

                {{-- Detail Tambahan: Instansi Pengundang / Nomor SPT --}}
                @if($selected->instansi_pengundang)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Instansi / Pihak Pengundang</p>
                    <p class="text-xs font-extrabold text-slate-900 mt-0.5">{{ $selected->instansi_pengundang }}</p>
                </div>
                @endif

                @if($selected->nomor_surat_tugas)
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-[10px] font-bold text-slate-700 uppercase tracking-wider">Nomor Surat Perintah Tugas (SPT)</p>
                    <p class="text-xs font-extrabold text-slate-900 font-mono mt-0.5">{{ $selected->nomor_surat_tugas }}</p>
                </div>
                @endif

                {{-- Judul & Deskripsi --}}
                <div class="space-y-1">
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider">Judul Kegiatan</p>
                    <p class="font-bold text-slate-900 text-sm">{{ $selected->judul }}</p>
                </div>

                <div class="space-y-1">
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider">Uraian / Keterangan</p>
                    <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-200 leading-relaxed">{{ $selected->deskripsi }}</p>
                </div>

                {{-- Koordinat GPS & Lokasi --}}
                @if($selected->latitude && $selected->longitude)
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#064E3B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Validasi Lokasi Penugasan</span>
                        </p>
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded border {{ $selected->badge_sumber_koordinat }}">
                            {{ $selected->label_sumber_koordinat }}
                        </span>
                    </div>

                    @if($selected->alamat_gps)
                    <p class="text-xs font-semibold text-slate-700 bg-white p-2.5 rounded-lg border border-slate-200 leading-snug">
                        📍 {{ $selected->alamat_gps }}
                    </p>
                    @endif

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <span class="font-mono text-xs font-bold text-slate-900">{{ $selected->latitude }}, {{ $selected->longitude }}</span>
                        <a href="https://maps.google.com/?q={{ $selected->latitude }},{{ $selected->longitude }}" target="_blank"
                           class="btn-sadi-primary px-3 py-1.5 rounded-lg font-bold text-[11px] flex items-center gap-1 shadow-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>
                </div>
                @endif

                {{-- Foto Lokasi --}}
                @if($selected->foto_lokasi)
                <div>
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider mb-2">Foto Bukti Lokasi</p>
                    <img src="{{ asset('storage/' . $selected->foto_lokasi) }}" class="w-full rounded-xl border border-slate-300 object-cover max-h-64" alt="Foto Bukti">
                </div>
                @endif

                {{-- File Dokumen --}}
                @if($selected->file_dokumen)
                <div>
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider mb-2">Dokumen Pendukung</p>
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
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider mb-2">Tanda Tangan Digital Pengaju</p>
                    <div class="bg-slate-50 border border-slate-300 rounded-xl p-3 flex items-center justify-center">
                        <img src="{{ $selected->tanda_tangan_src }}" class="max-h-32 w-auto" alt="Tanda Tangan">
                    </div>
                </div>
                @endif

                {{-- Catatan Admin (jika sudah diproses) --}}
                @if($selected->catatan_admin)
                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <p class="font-extrabold text-slate-800 uppercase tracking-wider mb-1">Catatan Admin</p>
                    <p class="text-slate-700">{{ $selected->catatan_admin }}</p>
                </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            @if($selected->status === 'menunggu')
            <div class="p-4 border-t border-slate-200 flex gap-3 sticky bottom-0 bg-white rounded-b-2xl">
                <button wire:click="konfirmasiTolak({{ $selected->id }})"
                    class="flex-1 py-2.5 rounded-lg bg-white hover:bg-red-50 text-red-700 font-bold border border-red-200 transition cursor-pointer text-xs">
                    Tolak Pengajuan
                </button>
                <button wire:click="konfirmasiSetujui({{ $selected->id }})"
                    class="flex-[2] btn-sadi-primary py-2.5 rounded-lg text-white font-bold flex items-center justify-center gap-2 text-xs cursor-pointer">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Setujui & Catat Kehadiran</span>
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ═══════════ MODAL: KONFIRMASI SETUJUI ═══════════ --}}
    @if($showApproveModal && $selected)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4 border-2 border-[#064E3B]">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center mx-auto text-[#064E3B]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-outfit font-extrabold text-slate-900 text-base">Setujui Pengajuan</h3>
                <p class="text-xs text-slate-600">Pengajuan <strong>{{ $selected->judul }}</strong> oleh <strong>{{ $selected->pegawai->nama_lengkap ?? '—' }}</strong> akan disetujui dan kehadiran otomatis tercatat sebagai <strong>Dinas Luar</strong>.</p>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Admin (Opsional)</label>
                <textarea wire:model="catatanAdmin" rows="2" placeholder="Catatan tambahan untuk staf (opsional)..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none resize-none"></textarea>
            </div>

            <div class="flex gap-2.5">
                <button wire:click="tutupModal()" class="flex-1 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-xs cursor-pointer">Batal</button>
                <button wire:click="setujui()" class="flex-[2] btn-sadi-primary py-2.5 rounded-lg text-white font-bold transition text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E2C268]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Setujui Sekarang</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════ MODAL: KONFIRMASI TOLAK ═══════════ --}}
    @if($showRejectModal && $selected)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4 border-2 border-red-300">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center mx-auto text-red-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 class="font-outfit font-extrabold text-slate-900 text-base">Tolak Pengajuan</h3>
                <p class="text-xs text-slate-600">Pengajuan <strong>{{ $selected->judul }}</strong> oleh <strong>{{ $selected->pegawai->nama_lengkap ?? '—' }}</strong> akan ditolak.</p>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea wire:model="catatanAdmin" rows="3" placeholder="Jelaskan alasan penolakan pengajuan ini (wajib diisi)..."
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-900 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none resize-none"></textarea>
                @error('catatanAdmin')
                <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2.5">
                <button wire:click="tutupModal()" class="flex-1 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition text-xs cursor-pointer">Batal</button>
                <button wire:click="tolak()" class="flex-[2] py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold transition text-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Tolak Pengajuan</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
