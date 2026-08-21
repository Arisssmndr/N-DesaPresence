<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan | Presence Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-[#F5F0E8] min-h-screen flex items-center justify-center p-6 text-slate-800">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-2xl border border-[#C9A84C]/30 text-center space-y-6">
        <div class="w-20 h-20 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200 shadow-inner">
            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <div>
            <h1 class="font-outfit text-3xl font-extrabold text-[#064E3B]">404 — Halaman Tidak Ada</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Halaman yang Anda cari tidak ditemukan atau telah dipindahkan.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-block w-full py-3 rounded-xl bg-[#064E3B] text-white font-bold text-xs hover:bg-[#04392B] shadow-lg transition">
            KEMBALI KE DASHBOARD
        </a>
    </div>
</body>
</html>
