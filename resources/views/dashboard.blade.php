@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- Top Greeting Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @php
                $hour = (int) now()->format('H');
                $greeting = match(true) {
                    $hour >= 5 && $hour < 11  => 'Selamat Pagi',
                    $hour >= 11 && $hour < 15 => 'Selamat Siang',
                    $hour >= 15 && $hour < 18 => 'Selamat Sore',
                    default                   => 'Selamat Malam',
                };
            @endphp
            <h1 class="font-outfit text-3xl font-bold text-[#064E3B] tracking-tight">SISTEM ABSENSI DESA NANGTANG</h1>
            <p class="text-sm text-slate-600 mt-1 font-medium">{{ $greeting }}, <span class="text-[#064E3B] font-bold">{{ auth()->user()->name }}</span> ({{ auth()->user()->role }})</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                FASE 1 — TERVERIFIKASI
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards (60-30-10 palette matching user's image) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Hadir Hari Ini -->
        <div class="sadi-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir Hari Ini</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-800 mt-1">18</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="sadi-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Terlambat</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-800 mt-1">2</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Izin / Sakit -->
        <div class="sadi-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Izin / Sakit</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-800 mt-1">1</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <!-- Dinas Luar -->
        <div class="sadi-card p-6 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dinas Luar</p>
                <p class="font-outfit text-3xl font-extrabold text-slate-800 mt-1">3</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Section Grid (Kehadiran & Matrix & Audit Log) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Absensi Hari Ini Feed (8 Cols) -->
        <div class="lg:col-span-8 sadi-card p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-outfit text-lg font-bold text-[#064E3B]">Absensi Hari Ini</h3>
                    <p class="text-xs text-slate-500">Log kehadiran perangkat desa secara real-time</p>
                </div>
                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800">15 Data Terakhir</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3 px-2">Nama</th>
                            <th class="py-3 px-2">Jabatan</th>
                            <th class="py-3 px-2">Jam Masuk</th>
                            <th class="py-3 px-2">Jam Pulang</th>
                            <th class="py-3 px-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-2 font-bold text-slate-800">H. Ahmad Supriyadi, S.IP</td>
                            <td class="py-3 px-2 text-slate-600">Kepala Desa</td>
                            <td class="py-3 px-2 text-slate-700">07:55:12 WIB</td>
                            <td class="py-3 px-2 text-slate-400">—</td>
                            <td class="py-3 px-2 text-right">
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Tepat Waktu</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-2 font-bold text-slate-800">Hj. Nurlaila Rahmawati, S.AP</td>
                            <td class="py-3 px-2 text-slate-600">Sekretaris Desa</td>
                            <td class="py-3 px-2 text-slate-700">08:18:04 WIB</td>
                            <td class="py-3 px-2 text-slate-400">—</td>
                            <td class="py-3 px-2 text-right">
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Terlambat</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Matriks Presensi Mini & Audit Trail (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Matriks Presensi Widget -->
            <div class="sadi-card p-6">
                <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-1">Matriks Presensi</h3>
                <p class="text-xs text-slate-500 mb-4">Visual kehadiran bulanan</p>

                <!-- Grid Visual 1-31 representation -->
                <div class="grid grid-cols-7 gap-1.5 mb-4">
                    @for ($i = 1; $i <= 21; $i++)
                        <div class="h-6 rounded-md text-[10px] font-bold flex items-center justify-center text-white {{ $i % 5 == 0 ? 'bg-amber-500' : ($i % 7 == 0 ? 'bg-blue-500' : 'bg-emerald-600') }}">
                            {{ $i }}
                        </div>
                    @endfor
                </div>
                <div class="flex items-center justify-around text-[10px] font-semibold text-slate-600 border-t border-slate-100 pt-3">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Hadir</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Terlambat</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Dinas Luar</span>
                </div>
            </div>

            <!-- Audit Trail Widget -->
            <div class="sadi-card p-6">
                <h3 class="font-outfit text-base font-bold text-[#064E3B] mb-1">Audit Trail</h3>
                <p class="text-xs text-slate-500 mb-4">Aktivitas sistem terbaru</p>

                <div class="space-y-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="font-bold text-slate-800">Admin Desa (Sekdes)</p>
                        <p class="text-[11px] text-slate-600">Login ke sistem SADI</p>
                        <p class="text-[10px] text-slate-400 mt-1">{{ now()->format('H:i:s WIB') }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
