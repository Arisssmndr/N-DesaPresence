<div class="space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Override Presensi Manual Admin</h1>
        <p class="text-xs text-slate-500 mt-1">Digunakan untuk kondisi darurat jika pegawai tidak bisa tap sidik jari (dengan audit trail tertulis)</p>
    </div>

    <!-- Override Form Card -->
    <div class="sadi-card p-6">
        <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-4">Form Input Override Presensi</h3>

        <form wire:submit.prevent="saveOverride" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Pegawai <span class="text-red-500">*</span></label>
                <select wire:model="pegawai_id" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach ($pegawais as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_lengkap }} ({{ $p->jabatan->nama_jabatan ?? '' }})</option>
                    @endforeach
                </select>
                @error('pegawai_id') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" wire:model="tanggal" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                @error('tanggal') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Masuk</label>
                <input type="time" wire:model="jam_masuk" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Jam Pulang</label>
                <input type="time" wire:model="jam_pulang" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                <select wire:model="status" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Dinas Luar">Dinas Luar</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Alasan / Justifikasi Admin <span class="text-red-500">*</span></label>
                <input type="text" wire:model="keterangan" placeholder="Contoh: Mesin mati daya saat jam masuk" class="w-full px-3 py-2 text-xs rounded-xl border border-[#C9A84C]/40 focus:ring-2 focus:ring-[#C9A84C]">
                @error('keterangan') <span class="text-[11px] text-red-600 font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 pt-2 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#064E3B] text-white font-bold text-xs hover:bg-[#04392B] shadow-md transition">
                    SIMPAN OVERRIDE PRESENSI
                </button>
            </div>
        </form>
    </div>

    <!-- Override History Table -->
    <div class="sadi-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h4 class="font-outfit text-base font-bold text-[#064E3B]">Riwayat Override Manual Admin</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-4">Status Override</th>
                        <th class="py-3.5 px-4">Alasan / Keterangan</th>
                        <th class="py-3.5 px-4">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($overrides as $o)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                {{ $o->tanggal->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $o->pegawai->nama_lengkap ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    {{ $o->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $o->keterangan }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-semibold">
                                {{ $o->verifikator->name ?? 'Admin' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 italic">
                                Belum ada riwayat override manual.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $overrides->links() }}
        </div>
    </div>

</div>
