@extends('staf.layout', ['title' => 'Ajukan Absen Luar — Portal Staf Desa Nangtang'])

@section('content')
<div class="space-y-5 pb-6">

    {{-- Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('staf.beranda') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#064E3B] hover:text-emerald-700 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-sm transition">
            <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Beranda</span>
        </a>
        <a href="{{ route('staf.riwayat.pengajuan') }}" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-500 hover:text-[#064E3B] transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Riwayat Pengajuan
        </a>
    </div>

    {{-- Flash Error --}}
    @if(session('error'))
    <div class="p-3.5 bg-red-50 border-2 border-red-300 rounded-2xl shadow-sm flex items-start gap-2.5 text-xs text-red-800 font-semibold">
        <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="p-3.5 bg-red-50 border-2 border-red-300 rounded-2xl shadow-sm space-y-1 text-xs">
        <p class="font-bold text-red-800">Terdapat kesalahan:</p>
        <ul class="list-disc list-inside text-red-700 space-y-0.5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Sudah ada pengajuan hari ini --}}
    @if($pengajuanHariIni)
    <div class="sadi-card p-5 bg-amber-50 border-2 border-amber-300 text-center space-y-2 shadow-md">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 border-2 border-amber-300 flex items-center justify-center mx-auto">
            <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h4 class="font-outfit font-extrabold text-amber-900 text-sm">Pengajuan Hari Ini Sudah Ada</h4>
        <p class="text-xs text-amber-800">Anda sudah mengajukan: <strong>{{ $pengajuanHariIni->judul }}</strong></p>
        <span class="inline-block text-[11px] font-bold px-3 py-1 rounded-full border {{ $pengajuanHariIni->badge_class }}">
            {{ $pengajuanHariIni->label_status }}
        </span>
        <div class="pt-1">
            <a href="{{ route('staf.riwayat.pengajuan') }}" class="text-xs font-bold text-amber-900 underline">Lihat Detail Pengajuan</a>
        </div>
    </div>
    @else
    {{-- ─────── FORM PENGAJUAN ─────── --}}
    <form action="{{ route('staf.ajukan.store') }}" method="POST" enctype="multipart/form-data"
          id="form-ajukan-absen"
          x-data="{
              jenisAbsen: '',
              previewFoto: null,
              namaFoto: '',
              namaDokumen: '',
              signatureCanvas: null,
              ctx: null,
              isDrawing: false,
              hasSignature: false,
              
              // GPS State
              lat: '',
              lng: '',
              alamatGps: '',
              gpsStatus: 'idle', // 'idle' | 'loading' | 'success' | 'error'
              gpsErrorMessage: '',
              
              initCanvas() {
                  this.signatureCanvas = this.$refs.sigCanvas;
                  this.ctx = this.signatureCanvas.getContext('2d');
                  this.ctx.strokeStyle = '#064E3B';
                  this.ctx.lineWidth = 2.5;
                  this.ctx.lineCap = 'round';
                  this.ctx.lineJoin = 'round';
              },
              startDraw(e) {
                  this.isDrawing = true;
                  const pos = this.getPos(e);
                  this.ctx.beginPath();
                  this.ctx.moveTo(pos.x, pos.y);
              },
              draw(e) {
                  if (!this.isDrawing) return;
                  e.preventDefault();
                  const pos = this.getPos(e);
                  this.ctx.lineTo(pos.x, pos.y);
                  this.ctx.stroke();
                  this.hasSignature = true;
              },
              stopDraw() { this.isDrawing = false; },
              getPos(e) {
                  const rect = this.signatureCanvas.getBoundingClientRect();
                  const src = e.touches ? e.touches[0] : e;
                  return { x: src.clientX - rect.left, y: src.clientY - rect.top };
              },
              clearSig() {
                  this.ctx.clearRect(0, 0, this.signatureCanvas.width, this.signatureCanvas.height);
                  this.hasSignature = false;
                  this.$refs.ttdInput.value = '';
              },
              saveSig() {
                  if (!this.hasSignature) return false;
                  this.$refs.ttdInput.value = this.signatureCanvas.toDataURL('image/png');
                  return true;
              },
              
              // Wajib GPS Functionality
              requestLocation() {
                  this.gpsStatus = 'loading';
                  this.gpsErrorMessage = '';
                  
                  if (!navigator.geolocation) {
                      this.gpsStatus = 'error';
                      this.gpsErrorMessage = 'Perangkat Anda tidak mendukung fitur Geolocation / GPS.';
                      this.showGpsEnforceModal();
                      return;
                  }
                  
                  navigator.geolocation.getCurrentPosition(
                      (position) => {
                          this.lat = position.coords.latitude.toFixed(7);
                          this.lng = position.coords.longitude.toFixed(7);
                          this.gpsStatus = 'success';
                          
                          // Cari reverse geocoding sederhana atau catat koordinat
                          this.alamatGps = 'Koordinat: ' + this.lat + ', ' + this.lng;
                          
                          // SweetAlert notif sukses kunci GPS
                          if (window.Swal) {
                              Swal.fire({
                                  title: 'Lokasi GPS Terkunci',
                                  text: 'Titik koordinat Anda berhasil diverifikasi (' + this.lat + ', ' + this.lng + ')',
                                  icon: 'success',
                                  confirmButtonColor: '#064E3B',
                                  customClass: {
                                      popup: 'rounded-3xl border border-[#C9A84C]/30 shadow-2xl',
                                      confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                                  }
                              });
                          }
                      },
                      (error) => {
                          this.gpsStatus = 'error';
                          let msg = 'Izin lokasi ditolak atau GPS belum aktif.';
                          if (error.code === error.PERMISSION_DENIED) {
                              msg = 'Anda menolak akses lokasi. Wajib izinkan akses GPS di browser untuk dapat mengajukan absen luar.';
                          } else if (error.code === error.POSITION_UNAVAILABLE) {
                              msg = 'Informasi lokasi GPS tidak tersedia pada perangkat Anda.';
                          } else if (error.code === error.TIMEOUT) {
                              msg = 'Waktu permintaan lokasi GPS habis. Silakan coba lagi.';
                          }
                          this.gpsErrorMessage = msg;
                          this.showGpsEnforceModal();
                      },
                      {
                          enableHighAccuracy: true,
                          timeout: 10000,
                          maximumAge: 0
                      }
                  );
              },
              
              showGpsEnforceModal() {
                  if (window.Swal) {
                      Swal.fire({
                          title: 'Aktivasi GPS Wajib!',
                          text: 'Sesuai standar verifikasi presensi, Anda WAJIB mengaktifkan GPS dan mengizinkan akses lokasi saat mengajukan absen luar.',
                          icon: 'warning',
                          confirmButtonText: 'Aktifkan & Izinkan GPS',
                          confirmButtonColor: '#064E3B',
                          showCancelButton: false,
                          allowOutsideClick: false,
                          allowEscapeKey: false,
                          customClass: {
                              popup: 'rounded-3xl border border-[#C9A84C]/40 shadow-2xl',
                              confirmButton: 'rounded-xl px-6 py-3 font-bold text-sm'
                          }
                      }).then((result) => {
                          if (result.isConfirmed) {
                              this.requestLocation();
                          }
                      });
                  } else {
                      alert('Aktivasi GPS Wajib! Silakan izinkan akses lokasi pada browser Anda.');
                      this.requestLocation();
                  }
              },
              
              validateAndSubmit(e) {
                  // Cek apakah GPS sudah terkunci
                  if (!this.lat || !this.lng || this.gpsStatus !== 'success') {
                      e.preventDefault();
                      this.showGpsEnforceModal();
                      return false;
                  }
                  
                  // Cek tanda tangan
                  if (!this.hasSignature) {
                      e.preventDefault();
                      if (window.Swal) {
                          Swal.fire({
                              title: 'Tanda Tangan Kosong',
                              text: 'Silakan bubuhkan tanda tangan digital Anda terlebih dahulu pada kotak yang disediakan.',
                              icon: 'warning',
                              confirmButtonColor: '#064E3B',
                              customClass: {
                                  popup: 'rounded-3xl border border-[#C9A84C]/30 shadow-2xl',
                                  confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                              }
                          });
                      } else {
                          alert('Silakan tanda tangani formulir sebelum mengajukan.');
                      }
                      return false;
                  }
                  
                  this.saveSig();
                  return true;
              }
          }"
          x-init="initCanvas(); requestLocation();"
          @submit="validateAndSubmit($event)">
        @csrf

        {{-- Hidden GPS fields --}}
        <input type="hidden" name="latitude" :value="lat">
        <input type="hidden" name="longitude" :value="lng">
        <input type="hidden" name="alamat_gps" :value="alamatGps">

        {{-- 1. Header & Info Staf --}}
        <div class="sadi-card p-5 bg-white shadow-md space-y-1.5">
            <div class="flex items-center gap-3">
                @if($pegawai->foto_profil)
                    <img src="{{ asset('storage/' . $pegawai->foto_profil) }}" class="w-12 h-12 rounded-full object-cover border-2 border-[#C9A84C]">
                @else
                    <div class="w-12 h-12 rounded-full bg-slate-200 border-2 border-[#C9A84C] flex items-center justify-center overflow-hidden shrink-0">
                        <svg class="w-8 h-8 text-slate-400 translate-y-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <p class="font-outfit font-extrabold text-slate-800 text-sm">{{ $pegawai->nama_lengkap }}</p>
                    <p class="text-[11px] text-[#C9A84C] font-bold">{{ $pegawai->jabatan->nama_jabatan ?? 'Perangkat Desa' }}</p>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-[11px] text-amber-900 font-semibold flex items-start gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Formulir ini untuk pengajuan kehadiran di luar kantor desa (Dinas Luar / Kegiatan Lapangan). Pengajuan akan diverifikasi oleh Admin / Kades sebelum masuk laporan resmi.</span>
            </div>
        </div>

        {{-- GPS Live Status Banner --}}
        <div class="sadi-card p-4 border-2 transition-all duration-200"
             :class="{
                 'bg-emerald-50 border-emerald-300': gpsStatus === 'success',
                 'bg-amber-50 border-amber-300': gpsStatus === 'loading',
                 'bg-red-50 border-red-300': gpsStatus === 'error' || gpsStatus === 'idle'
             }">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0"
                         :class="{
                             'bg-emerald-600': gpsStatus === 'success',
                             'bg-amber-500 animate-pulse': gpsStatus === 'loading',
                             'bg-red-600': gpsStatus === 'error' || gpsStatus === 'idle'
                         }">
                        <template x-if="gpsStatus === 'success'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </template>
                        <template x-if="gpsStatus === 'loading'">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </template>
                        <template x-if="gpsStatus === 'error' || gpsStatus === 'idle'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </template>
                    </div>
                    <div>
                        <p class="text-xs font-extrabold"
                           :class="{
                               'text-emerald-900': gpsStatus === 'success',
                               'text-amber-900': gpsStatus === 'loading',
                               'text-red-900': gpsStatus === 'error' || gpsStatus === 'idle'
                           }">
                            <span x-show="gpsStatus === 'success'">📍 Lokasi GPS Terverifikasi</span>
                            <span x-show="gpsStatus === 'loading'">⏳ Sedang Mengunci Titik GPS...</span>
                            <span x-show="gpsStatus === 'error' || gpsStatus === 'idle'">⚠️ GPS Wajib Diaktifkan</span>
                        </p>
                        <p class="text-[11px] mt-0.5"
                           :class="{
                               'text-emerald-700 font-mono': gpsStatus === 'success',
                               'text-amber-700': gpsStatus === 'loading',
                               'text-red-700': gpsStatus === 'error' || gpsStatus === 'idle'
                           }">
                            <span x-show="gpsStatus === 'success'" x-text="'Lat: ' + lat + ', Lng: ' + lng"></span>
                            <span x-show="gpsStatus === 'loading'">Mohon izinkan saat browser meminta akses lokasi</span>
                            <span x-show="gpsStatus === 'error' || gpsStatus === 'idle'" x-text="gpsErrorMessage || 'Klik tombol di samping untuk mengaktifkan GPS'"></span>
                        </p>
                    </div>
                </div>

                <button type="button" @click="requestLocation()"
                        class="px-3 py-2 rounded-xl text-xs font-extrabold shrink-0 shadow-sm transition active:scale-95"
                        :class="gpsStatus === 'success' ? 'bg-white border border-emerald-300 text-emerald-800 hover:bg-emerald-100' : 'bg-red-600 text-white hover:bg-red-700'">
                    <span x-show="gpsStatus === 'success'">Segarkan</span>
                    <span x-show="gpsStatus !== 'success'">Nyalakan GPS</span>
                </button>
            </div>
        </div>

        {{-- 2. Pilih Jenis Pengajuan --}}
        <div class="sadi-card p-5 bg-white shadow-md space-y-3.5">
            <div>
                <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm">Pilih Kategori Kehadiran Luar <span class="text-red-500">*</span></h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Pilih salah satu dari 3 kategori Dinas Luar atau Kegiatan Sosial sesuai penugasan Anda:</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- 1. Dinas Luar Undangan --}}
                <label :class="jenisAbsen === 'dinas_luar_undangan' ? 'border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-600/20 shadow-sm' : 'border-slate-200 bg-white hover:border-indigo-300'"
                       class="cursor-pointer rounded-2xl border-2 p-3.5 flex items-start gap-3 transition-all">
                    <input type="radio" name="jenis" value="dinas_luar_undangan" x-model="jenisAbsen" class="sr-only">
                    <div :class="jenisAbsen === 'dinas_luar_undangan' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-100 text-indigo-700'"
                         class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs font-extrabold" :class="jenisAbsen === 'dinas_luar_undangan' ? 'text-indigo-900' : 'text-slate-800'">Dinas Luar (Undangan)</p>
                        <p class="text-[10.5px] text-slate-500 leading-tight">Menerima undangan resmi dari pihak luar/Kecamatan/Pemkab.</p>
                        <span class="inline-block text-[9.5px] font-bold text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-md mt-1">Lampirkan Surat Undangan</span>
                    </div>
                </label>

                {{-- 2. Dinas Luar Pengajuan (Mandiri / Inisiatif) --}}
                <label :class="jenisAbsen === 'dinas_luar_pengajuan' ? 'border-teal-600 bg-teal-50/70 ring-2 ring-teal-600/20 shadow-sm' : 'border-slate-200 bg-white hover:border-teal-300'"
                       class="cursor-pointer rounded-2xl border-2 p-3.5 flex items-start gap-3 transition-all">
                    <input type="radio" name="jenis" value="dinas_luar_pengajuan" x-model="jenisAbsen" class="sr-only">
                    <div :class="jenisAbsen === 'dinas_luar_pengajuan' ? 'bg-teal-600 text-white shadow-sm' : 'bg-teal-100 text-teal-700'"
                         class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs font-extrabold" :class="jenisAbsen === 'dinas_luar_pengajuan' ? 'text-teal-900' : 'text-slate-800'">Dinas Luar (Pengajuan Mandiri)</p>
                        <p class="text-[10.5px] text-slate-500 leading-tight">Inisiatif mandiri perangkat desa untuk urusan lapangan / wilayah.</p>
                        <span class="inline-block text-[9.5px] font-bold text-teal-700 bg-teal-100/80 px-2 py-0.5 rounded-md mt-1">Lampirkan Foto Lapangan</span>
                    </div>
                </label>

                {{-- 3. Dinas Luar Surat Tugas (SPT) --}}
                <label :class="jenisAbsen === 'dinas_luar_surat_tugas' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-600/20 shadow-sm' : 'border-slate-200 bg-white hover:border-blue-300'"
                       class="cursor-pointer rounded-2xl border-2 p-3.5 flex items-start gap-3 transition-all">
                    <input type="radio" name="jenis" value="dinas_luar_surat_tugas" x-model="jenisAbsen" class="sr-only">
                    <div :class="jenisAbsen === 'dinas_luar_surat_tugas' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-100 text-blue-700'"
                         class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs font-extrabold" :class="jenisAbsen === 'dinas_luar_surat_tugas' ? 'text-blue-900' : 'text-slate-800'">Dinas Luar (Surat Tugas / SPT)</p>
                        <p class="text-[10.5px] text-slate-500 leading-tight">Perintah/penugasan langsung dari Kepala Desa / Pemdes.</p>
                        <span class="inline-block text-[9.5px] font-bold text-blue-700 bg-blue-100/80 px-2 py-0.5 rounded-md mt-1">Lampirkan Dokumen SPT</span>
                    </div>
                </label>

                {{-- 4. Kegiatan Sosial --}}
                <label :class="jenisAbsen === 'kegiatan_sosial' ? 'border-pink-600 bg-pink-50/70 ring-2 ring-pink-600/20 shadow-sm' : 'border-slate-200 bg-white hover:border-pink-300'"
                       class="cursor-pointer rounded-2xl border-2 p-3.5 flex items-start gap-3 transition-all">
                    <input type="radio" name="jenis" value="kegiatan_sosial" x-model="jenisAbsen" class="sr-only">
                    <div :class="jenisAbsen === 'kegiatan_sosial' ? 'bg-pink-600 text-white shadow-sm' : 'bg-pink-100 text-pink-700'"
                         class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div class="space-y-0.5">
                        <p class="text-xs font-extrabold" :class="jenisAbsen === 'kegiatan_sosial' ? 'text-pink-900' : 'text-slate-800'">Kegiatan Sosial</p>
                        <p class="text-[10.5px] text-slate-500 leading-tight">Bakti sosial kemasyarakatan, gotong royong, pendampingan warga.</p>
                        <span class="inline-block text-[9.5px] font-bold text-pink-700 bg-pink-100/80 px-2 py-0.5 rounded-md mt-1">Lampirkan Foto Bukti</span>
                    </div>
                </label>
            </div>
            @error('jenis')
            <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p>
            @enderror
        </div>

        {{-- 3. Detail Kegiatan (muncul setelah pilih jenis) --}}
        <div x-show="jenisAbsen !== ''" x-transition class="space-y-4">

            {{-- Tanggal & Detail Form --}}
            <div class="sadi-card p-5 bg-white shadow-md space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Informasi Kegiatan & Penugasan</span>
                    </h4>
                    <span class="text-[10.5px] font-bold px-2.5 py-1 rounded-full"
                          :class="{
                              'bg-indigo-100 text-indigo-800': jenisAbsen === 'dinas_luar_undangan',
                              'bg-teal-100 text-teal-800': jenisAbsen === 'dinas_luar_pengajuan',
                              'bg-blue-100 text-blue-800': jenisAbsen === 'dinas_luar_surat_tugas',
                              'bg-pink-100 text-pink-800': jenisAbsen === 'kegiatan_sosial'
                          }">
                        <span x-show="jenisAbsen === 'dinas_luar_undangan'">Undangan Resmi</span>
                        <span x-show="jenisAbsen === 'dinas_luar_pengajuan'">Inisiatif Mandiri</span>
                        <span x-show="jenisAbsen === 'dinas_luar_surat_tugas'">Surat Tugas (SPT)</span>
                        <span x-show="jenisAbsen === 'kegiatan_sosial'">Sosial Kemasyarakatan</span>
                    </span>
                </div>

                {{-- Tanggal Absensi --}}
                <div class="text-xs space-y-1">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Tanggal Absensi <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $today) }}" max="{{ $today }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                    <p class="text-[10px] text-slate-400">Maksimal hari ini. Tidak dapat mengajukan tanggal di masa depan.</p>
                    @error('tanggal') <p class="text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Instansi Pengundang (Hanya untuk Dinas Luar Undangan) --}}
                <div x-show="jenisAbsen === 'dinas_luar_undangan'" x-transition class="text-xs space-y-1">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Instansi / Pihak Pengundang <span class="text-red-500">*</span></label>
                    <input type="text" name="instansi_pengundang" value="{{ old('instansi_pengundang') }}" placeholder="Contoh: Kantor Kecamatan Cigalontang / DPMD Kab. Tasikmalaya"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                    <p class="text-[10px] text-slate-400">Sebutkan nama lembaga, instansi, atau kepanitiaan yang mengundang.</p>
                    @error('instansi_pengundang') <p class="text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Nomor Surat Tugas / SPT (Hanya untuk Dinas Luar Surat Tugas) --}}
                <div x-show="jenisAbsen === 'dinas_luar_surat_tugas'" x-transition class="text-xs space-y-1">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Nomor Surat Perintah Tugas (SPT)</label>
                    <input type="text" name="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}" placeholder="Contoh: 090/045/SPT/Pemdes/2026 (Bila ada)"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                    <p class="text-[10px] text-slate-400">Tuliskan nomor SPT jika sudah diterbitkan oleh Pemerintah Desa.</p>
                    @error('nomor_surat_tugas') <p class="text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Judul Kegiatan --}}
                <div class="text-xs space-y-1">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Judul Ringkas Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Rapat Koordinasi Pembinaan Aparatur Desa" required maxlength="150"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none">
                    @error('judul') <p class="text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Keterangan Lengkap --}}
                <div class="text-xs space-y-1">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider">Keterangan / Uraian Kegiatan <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" rows="3" required placeholder="Jelaskan uraian kegiatan, agenda, hasil, atau keterangan yang perlu diketahui atasan..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:border-[#064E3B] focus:ring-2 focus:ring-[#064E3B]/20 outline-none resize-none">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p class="text-red-600 font-bold">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Bukti Dokumen (Untuk Dinas Luar Undangan & Surat Tugas) --}}
            <div x-show="jenisAbsen === 'dinas_luar_undangan' || jenisAbsen === 'dinas_luar_surat_tugas' || jenisAbsen === 'dinas_luar'" x-transition class="sadi-card p-5 bg-white shadow-md space-y-3">
                <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="jenisAbsen === 'dinas_luar_undangan'">Dokumen / Surat Undangan Resmi <span class="text-red-500">*</span></span>
                    <span x-show="jenisAbsen === 'dinas_luar_surat_tugas'">Surat Perintah Tugas (SPT) / Dokumen <span class="text-red-500">*</span></span>
                    <span x-show="jenisAbsen === 'dinas_luar'">Dokumen Pendukung <span class="text-red-500">*</span></span>
                </h4>
                <p class="text-[11px] text-slate-500">Unggah berkas scan surat / foto dokumen resmi. Format: PDF, JPG, PNG, WEBP. Maks 5MB.</p>

                <label class="block cursor-pointer border-2 border-dashed border-slate-300 hover:border-[#064E3B] rounded-xl p-5 text-center transition-all bg-slate-50/50 hover:bg-emerald-50/30">
                    <input type="file" name="file_dokumen" accept=".pdf,image/*" class="hidden"
                        @change="namaDokumen = $event.target.files[0] ? $event.target.files[0].name : ''">
                    <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <p x-show="!namaDokumen" class="text-xs text-slate-600 font-semibold">Klik untuk pilih berkas dokumen (PDF / Gambar)</p>
                    <p x-show="namaDokumen" class="text-xs text-[#064E3B] font-extrabold flex items-center justify-center gap-1.5" x-text="namaDokumen"></p>
                </label>
                @error('file_dokumen') <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p> @enderror
            </div>

            {{-- Bukti Foto Lokasi (Untuk Pengajuan Mandiri & Kegiatan Sosial) --}}
            <div x-show="jenisAbsen === 'dinas_luar_pengajuan' || jenisAbsen === 'kegiatan_sosial' || jenisAbsen === 'dinas_luar_undangan' || jenisAbsen === 'dinas_luar_surat_tugas'" x-transition class="sadi-card p-5 bg-white shadow-md space-y-3">
                <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Foto Bukti Keberadaan / Situasi Lapangan</span>
                    <span x-show="jenisAbsen === 'dinas_luar_pengajuan' || jenisAbsen === 'kegiatan_sosial'" class="text-red-500">*</span>
                    <span x-show="jenisAbsen === 'dinas_luar_undangan' || jenisAbsen === 'dinas_luar_surat_tugas'" class="text-slate-400 font-normal text-xs">(Opsional)</span>
                </h4>
                <p class="text-[11px] text-slate-500">Ambil foto dokumentasi situasi/kegiatan di lokasi sebagai bukti otentik. Format: JPG, PNG, WEBP. Maks 5MB.</p>

                {{-- Preview Foto --}}
                <div x-show="previewFoto" x-transition class="rounded-xl overflow-hidden border-2 border-[#C9A84C]">
                    <img :src="previewFoto" class="w-full max-h-48 object-cover">
                </div>

                <label class="block cursor-pointer border-2 border-dashed border-slate-300 hover:border-[#064E3B] rounded-xl p-5 text-center transition-all bg-slate-50/50 hover:bg-emerald-50/30">
                    <input type="file" name="foto_lokasi" accept="image/*" class="hidden"
                        @change="
                            const f = $event.target.files[0];
                            if (f) {
                                namaFoto = f.name;
                                const reader = new FileReader();
                                reader.onload = e => { previewFoto = e.target.result };
                                reader.readAsDataURL(f);
                            }
                        ">
                    <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p x-show="!namaFoto" class="text-xs text-slate-600 font-semibold">Klik untuk pilih foto atau jepret langsung dari kamera</p>
                    <p x-show="namaFoto" class="text-xs text-[#064E3B] font-extrabold" x-text="namaFoto"></p>
                </label>
                @error('foto_lokasi') <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p> @enderror
            </div>

            {{-- Tanda Tangan Digital --}}
            <div class="sadi-card p-5 bg-white shadow-md space-y-3">
                <h4 class="font-outfit font-extrabold text-[#064E3B] text-sm border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Tanda Tangan Digital <span class="text-red-500">*</span>
                </h4>
                <p class="text-[11px] text-slate-500">Tanda tangani pengajuan ini sebagai bukti bahwa informasi yang disampaikan adalah benar adanya.</p>

                <div class="relative rounded-2xl bg-slate-50 border-2 border-slate-200 overflow-hidden"
                     :class="hasSignature ? 'border-[#064E3B]' : 'border-slate-200'">
                    <canvas x-ref="sigCanvas" width="380" height="160" class="w-full touch-none cursor-crosshair"
                        @mousedown="startDraw($event)"
                        @mousemove="draw($event)"
                        @mouseup="stopDraw()"
                        @mouseleave="stopDraw()"
                        @touchstart.prevent="startDraw($event)"
                        @touchmove.prevent="draw($event)"
                        @touchend="stopDraw()">
                    </canvas>
                    <div x-show="!hasSignature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <p class="text-slate-300 text-xs font-semibold">Tanda tangan di sini...</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" @click="clearSig()"
                        class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1.5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Ulangi Tanda Tangan
                    </button>
                    <div x-show="hasSignature" class="text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Tanda tangan terekam
                    </div>
                </div>

                <input type="hidden" name="tanda_tangan" x-ref="ttdInput">
                @error('tanda_tangan') <p class="text-[11px] text-red-600 font-bold">{{ $message }}</p> @enderror
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full btn-sadi-primary py-4 rounded-2xl text-white font-extrabold text-sm flex items-center justify-center gap-2.5 shadow-lg cursor-pointer">
                <svg class="w-5 h-5 text-[#C9A84C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                <span>Ajukan Sekarang</span>
            </button>

            <p class="text-center text-[10px] text-slate-400 px-4">
                Dengan menekan tombol di atas, Anda menyatakan bahwa informasi yang diberikan adalah benar dan dapat dipertanggungjawabkan.
            </p>

        </div>{{-- end x-show jenis dipilih --}}

    </form>
    @endif

</div>
@endsection
