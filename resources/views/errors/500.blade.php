<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kesalahan Server (500) &mdash; SIPerpus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 font-sans antialiased text-white flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="relative inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-amber-500/10 border border-amber-500/30 text-amber-400 shadow-2xl shadow-amber-500/20">
            <i class="fa-solid fa-server text-4xl"></i>
        </div>
        
        <div class="space-y-2">
            <h1 class="text-6xl font-black tracking-tight text-white">500</h1>
            <h2 class="text-xl font-bold text-slate-200">Terjadi Kesalahan Sistem</h2>
            <p class="text-sm text-slate-400 max-w-xs mx-auto leading-relaxed">
                Sistem kami sedang mengalami kendala teknis sementara. Tim kami telah mencatat kendala ini.
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
