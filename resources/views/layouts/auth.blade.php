<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Absensi Desa Nangtang (SADI)</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN Fallback & SweetAlert2 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sadiCream: '#F5F0E8',
                        sadiGreenDark: '#064E3B',
                        sadiGreenPrimary: '#1B4D3E',
                        sadiGold: '#C9A84C',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #F5F0E8;
            color: #1C2826;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        
        /* High Contrast Buttons */
        .btn-sadi-primary, .btn-primary-dark {
            background: linear-gradient(135deg, #064E3B 0%, #1B4D3E 100%) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            border: 1px solid rgba(201, 168, 76, 0.4) !important;
            box-shadow: 0 4px 14px rgba(6, 78, 59, 0.35) !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-sadi-primary:hover, .btn-primary-dark:hover {
            background: linear-gradient(135deg, #04392B 0%, #064E3B 100%) !important;
            box-shadow: 0 6px 20px rgba(6, 78, 59, 0.45) !important;
            transform: translateY(-1px);
        }
        
        .btn-gold {
            background: linear-gradient(135deg, #E2C268 0%, #C9A84C 100%) !important;
            color: #064E3B !important;
            font-family: 'Outfit', sans-serif !important;
            font-weight: 800 !important;
            border: 1px solid #B59339 !important;
            box-shadow: 0 4px 14px rgba(201, 168, 76, 0.4) !important;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #C9A84C 0%, #B59339 100%) !important;
            box-shadow: 0 6px 20px rgba(201, 168, 76, 0.5) !important;
            transform: translateY(-1px);
        }

        /* High contrast cards & inputs */
        .auth-card {
            background: #FFFFFF;
            border: 1px solid rgba(201, 168, 76, 0.25);
            box-shadow: 0 20px 40px -15px rgba(6, 78, 59, 0.15);
        }
        .auth-panel-left {
            background: linear-gradient(165deg, #064E3B 0%, #04392B 100%) !important;
            color: #FFFFFF !important;
        }
        .auth-input {
            background-color: #FFFFFF !important;
            color: #1E293B !important;
            border: 1.5px solid #CBD5E1 !important;
        }
        .auth-input:focus {
            border-color: #064E3B !important;
            box-shadow: 0 0 0 3px rgba(6, 78, 59, 0.15) !important;
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased bg-[#F5F0E8] min-h-screen text-slate-800 flex items-center justify-center p-4 sm:p-6">
    @yield('content')
</body>
</html>
