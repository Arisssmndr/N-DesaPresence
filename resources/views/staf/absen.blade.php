@extends('staf.layout', ['title' => 'Tanda Tangan Presensi ' . ucfirst($jenis)])

@section('content')
<div class="space-y-4 pb-6">

    <!-- Header Form -->
    <div class="flex items-center justify-between">
        <a href="{{ route('staf.beranda') }}" class="text-xs font-bold text-[#064E3B] flex items-center gap-1.5 hover:underline">
            ← Kembali ke Beranda
        </a>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }} uppercase tracking-wider">
            Absen {{ $jenis }}
        </span>
    </div>

    <!-- Pegawai & Network Info Card -->
    <div class="sadi-card p-4 bg-white space-y-3 shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-[#064E3B]/10 border border-[#C9A84C]/40 flex items-center justify-center text-[#064E3B] font-bold text-sm shrink-0">
                {{ strtoupper(substr($pegawai->nama_lengkap, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-slate-800 text-sm truncate">{{ $pegawai->nama_lengkap }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</p>
            </div>
            <span class="text-xs font-mono font-bold text-[#064E3B] bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200 shrink-0">{{ date('H:i') }} WIB</span>
        </div>

        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <div class="flex items-center gap-1.5 text-emerald-800 font-bold">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>WiFi Kantor Desa Terverifikasi</span>
            </div>
            <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">{{ $clientIp }}</span>
        </div>
    </div>

    <!-- Canvas Signature Card -->
    <div id="signature-card" class="sadi-card p-5 bg-white space-y-4 shadow-xl">
        <div>
            <h3 class="font-outfit font-bold text-[#064E3B] text-base">Tanda Tangan Digital Basah</h3>
            <p class="text-xs text-slate-500">Bubuhkan tanda tangan Anda menggunakan ujung jari di dalam kotak berikut:</p>
        </div>

        <div id="canvas-wrapper" class="border-2 border-dashed border-[#C9A84C]/60 rounded-2xl bg-[#FAFAF8] overflow-hidden relative" style="touch-action: none;">
            <canvas id="canvas-ttd" height="230" class="w-full block cursor-crosshair"></canvas>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20">
                <span class="text-xs font-bold tracking-widest text-[#064E3B] -rotate-6">GORES TANDA TANGAN DI SINI</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="clearCanvas()"
                class="flex-1 py-3 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-50 transition flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Bersihkan</span>
            </button>
            <button type="button" onclick="kirimTandaTangan()" id="btn-submit"
                class="flex-[2] py-3 rounded-xl btn-gold text-xs font-bold shadow-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4 text-[#064E3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Kirim Presensi {{ ucfirst($jenis) }}</span>
            </button>
        </div>
    </div>

    <!-- Loading Screen -->
    <div id="loading-card" class="hidden sadi-card p-8 bg-white text-center space-y-3">
        <div class="w-12 h-12 border-4 border-[#C9A84C]/30 border-t-[#064E3B] rounded-full animate-spin mx-auto"></div>
        <p class="font-bold text-slate-700 text-sm">Menyimpan Tanda Tangan & Presensi...</p>
    </div>

    <!-- Sukses Screen -->
    <div id="sukses-card" class="hidden sadi-card p-6 bg-white text-center space-y-4 shadow-xl">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <h3 class="font-outfit font-extrabold text-xl text-[#064E3B]" id="sukses-title">Presensi Berhasil!</h3>
            <p class="text-xs text-slate-500 mt-1" id="sukses-msg">Data tanda tangan dan jam kehadiran Anda telah tercatat di server.</p>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 max-w-xs mx-auto">
            <img id="preview-img" src="" alt="Pratinjau Tanda Tangan" class="max-h-20 mx-auto">
        </div>

        <a href="{{ route('staf.beranda') }}" class="block w-full py-3.5 rounded-xl btn-gold text-sm font-bold shadow text-center">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    let signaturePad = null;

    function initSignature() {
        const canvas = document.getElementById('canvas-ttd');
        const wrapper = document.getElementById('canvas-wrapper');
        canvas.width = wrapper.offsetWidth;
        canvas.height = 230;

        signaturePad = new SignaturePad(canvas, {
            minWidth: 1.5,
            maxWidth: 3.5,
            penColor: '#064E3B',
            backgroundColor: 'rgba(0,0,0,0)'
        });
    }

    function clearCanvas() {
        if (signaturePad) signaturePad.clear();
    }

    async function kirimTandaTangan() {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('⚠️ Harap bubuhkan tanda tangan Anda terlebih dahulu sebelum mengirim.');
            return;
        }

        const btn = document.getElementById('btn-submit');
        const ttdBase64 = signaturePad.toDataURL('image/png');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        document.getElementById('signature-card').classList.add('hidden');
        document.getElementById('loading-card').classList.remove('hidden');

        try {
            const res = await fetch("{{ route('staf.absen.submit') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    jenis: "{{ $jenis }}",
                    tanda_tangan: ttdBase64,
                })
            });

            const data = await res.json();

            document.getElementById('loading-card').classList.add('hidden');

            if (res.ok && data.status === 'berhasil') {
                document.getElementById('sukses-title').textContent = "Absen {{ ucfirst($jenis) }} Berhasil!";
                document.getElementById('sukses-msg').textContent = data.message;
                document.getElementById('preview-img').src = ttdBase64;
                document.getElementById('sukses-card').classList.remove('hidden');
            } else {
                document.getElementById('signature-card').classList.remove('hidden');
                if (window.Swal) {
                    Swal.fire({
                        title: 'Presensi Gagal',
                        text: data.message || 'Terjadi kesalahan saat memproses absensi.',
                        icon: 'error',
                        confirmButtonColor: '#064E3B',
                        confirmButtonText: 'Tutup',
                        customClass: {
                            popup: 'rounded-3xl border border-[#C9A84C]/40 shadow-2xl',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                        }
                    });
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat memproses absensi.'));
                }
            }
        } catch (err) {
            document.getElementById('loading-card').classList.add('hidden');
            document.getElementById('signature-card').classList.remove('hidden');
            if (window.Swal) {
                Swal.fire({
                    title: 'Gangguan Jaringan',
                    text: 'Terjadi gangguan jaringan atau Anda terputus dari WiFi Kantor Desa.',
                    icon: 'warning',
                    confirmButtonColor: '#064E3B',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-3xl border border-[#C9A84C]/40 shadow-2xl',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                    }
                });
            } else {
                alert('Terjadi kesalahan jaringan. Pastikan Anda tetap terhubung ke WiFi desa.');
            }
        }
    }

    window.addEventListener('load', initSignature);
    window.addEventListener('resize', () => {
        if (signaturePad) initSignature();
    });
</script>
@endsection
