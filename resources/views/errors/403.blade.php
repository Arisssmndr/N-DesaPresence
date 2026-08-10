<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | Presence Desa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-[#F5F0E8] min-h-screen flex items-center justify-center p-6 text-slate-800">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-2xl border border-[#C9A84C]/30 text-center space-y-6">
        <div class="w-20 h-20 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto text-3xl font-bold border border-red-200 shadow-inner">
            🛑
        </div>
        <div>
            <h1 class="font-outfit text-3xl font-extrabold text-[#064E3B]">403 — Akses Ditolak</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Maaf, akun Anda tidak memiliki hak akses yang sesuai untuk melihat modul atau halaman ini.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-block w-full py-3 rounded-xl bg-[#064E3B] text-white font-bold text-xs hover:bg-[#04392B] shadow-lg transition">
            KEMBALI KE DASHBOARD
        </a>
    </div>
</body>
</html>
