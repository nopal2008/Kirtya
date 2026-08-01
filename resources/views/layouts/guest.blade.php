<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Perpustakaan - Platform manajemen koleksi, sirkulasi, dan anggota perpustakaan yang modern.')">

    <title>@yield('title', 'SIPerpus') &mdash; SIPerpus</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full bg-slate-50 font-sans antialiased">

    <div class="flex min-h-full flex-col">

        {{-- ============================================================== --}}
        {{-- PAGE CONTENT --}}
        {{-- ============================================================== --}}
        <main class="flex-1">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="py-4 text-center">
            <p class="text-xs text-slate-400">
                &copy; {{ date('Y') }} SIPerpus &mdash; Sistem Informasi Perpustakaan. Hak Akses Terbatas.
            </p>
        </footer>

    </div>

    @stack('scripts')

</body>

</html>
