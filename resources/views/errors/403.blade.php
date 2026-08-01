<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak (403) &mdash; SIPerpus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 font-sans antialiased text-white flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="relative inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-rose-500/10 border border-rose-500/30 text-rose-400 shadow-2xl shadow-rose-500/20">
            <i class="fa-solid fa-user-shield text-4xl"></i>
        </div>
        
        <div class="space-y-2">
            <h1 class="text-6xl font-black tracking-tight text-white">403</h1>
            <h2 class="text-xl font-bold text-slate-200">Akses Dibatasi</h2>
            <p class="text-sm text-slate-400 max-w-xs mx-auto leading-relaxed">
                Anda tidak memiliki izin untuk mengakses area atau sumber daya ini. Silakan hubungi Administrator jika ini adalah kekeliruan.
            </p>
        </div>

        <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-medium text-sm transition-all duration-200 border border-slate-700">
                <i class="fa-solid fa-house text-xs"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
