<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Page Header -->
    <div>
        <h1 class="font-outfit text-2xl font-bold text-[#064E3B] tracking-tight">Import Log Presensi (USB Backup)</h1>
        <p class="text-xs text-slate-500 mt-1">Gunakan mode ini jika kabel serial terlepas atau untuk memulihkan log historis dari Flashdisk (.dat / .txt)</p>
    </div>

    <!-- Upload Card -->
    <div class="sadi-card p-8 text-center border-2 border-dashed border-[#C9A84C]/40 bg-white">
        <div class="w-16 h-16 rounded-full bg-emerald-50 text-[#064E3B] flex items-center justify-center mx-auto mb-4 border border-[#C9A84C]/20 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        </div>

        <h3 class="font-outfit text-lg font-bold text-[#064E3B] mb-1">Unggah Berkas Log Fingerprint</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto mb-6">Pilih file log bertipe <code class="bg-slate-100 px-1.5 py-0.5 rounded text-amber-800 font-bold">.dat</code> atau <code class="bg-slate-100 px-1.5 py-0.5 rounded text-amber-800 font-bold">.txt</code> yang diunduh dari port USB samping mesin sidik jari.</p>

        <form wire:submit.prevent="import" class="space-y-6 max-w-md mx-auto">
            <div>
                <input type="file" wire:model="logFile" accept=".dat,.txt,.csv" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#064E3B] file:text-white hover:file:bg-[#04392B] cursor-pointer">
                @error('logFile') <span class="block text-xs text-red-600 font-bold mt-2">{{ $message }}</span> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full py-3 px-6 rounded-xl bg-gradient-to-r from-[#064E3B] to-[#1B4D3E] text-white font-bold text-xs shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2">
                <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove>MULAI IMPORT LOG PRESENSI</span>
                <span wire:loading>Memproses Data Log...</span>
            </button>
        </form>
    </div>

    <!-- Import Summary Report -->
    @if (!empty($importSummary))
        <div class="sadi-card p-6 border-l-4 border-[#064E3B]">
            <h4 class="font-outfit text-base font-bold text-[#064E3B] mb-3">Ringkasan Hasil Import Log</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Total Baris</p>
                    <p class="font-outfit text-2xl font-bold text-slate-800 mt-1">{{ $importSummary['total'] }}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-xl">
                    <p class="text-[10px] text-emerald-600 font-bold uppercase">Berhasil Ingest</p>
                    <p class="font-outfit text-2xl font-bold text-emerald-800 mt-1">{{ $importSummary['success'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl">
                    <p class="text-[10px] text-amber-600 font-bold uppercase">Duplikat Diabaikan</p>
                    <p class="font-outfit text-2xl font-bold text-amber-800 mt-1">{{ $importSummary['duplicate'] }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-xl">
                    <p class="text-[10px] text-red-600 font-bold uppercase">PIN Belum Dikenal</p>
                    <p class="font-outfit text-2xl font-bold text-red-800 mt-1">{{ $importSummary['unknown_pin'] }}</p>
                </div>
            </div>
        </div>
    @endif

</div>
