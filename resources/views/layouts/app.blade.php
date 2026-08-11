<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Perpustakaan - Platform manajemen koleksi, sirkulasi, dan anggota perpustakaan yang modern.')">
    <title>@yield('title', 'Dashboard') &mdash; SIPerpus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="h-full bg-slate-50 font-sans antialiased">

    <div id="sidebar-overlay" class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm hidden lg:hidden"
        onclick="toggleSidebar()"></div>

    <div class="flex h-full">

        {{-- SIDEBAR --}}
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 w-64 flex-shrink-0 flex flex-col bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

            {{-- Brand Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-700/60">
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-book-open text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-base font-bold tracking-tight text-white">SIPerpus</p>
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">Sistem Informasi</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
                <p class="px-3 mb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Navigasi Utama
                </p>

                <a href="{{ route('dashboard') }}"
                    class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                    <i
                        class="fa-solid fa-gauge-high w-4 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                    Dashboard
                </a>

                @hasanyrole('admin')
                    <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">
                        Administrasi</p>
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Manajemen Pengguna
                    </a>
                    <a href="{{ route('admin.fines.settings') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.fines.settings') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-sliders w-4 text-center {{ request()->routeIs('admin.fines.settings') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Konfigurasi Denda
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.audit-logs.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-clipboard-list w-4 text-center {{ request()->routeIs('admin.audit-logs.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Audit Log
                    </a>
                @endhasanyrole

                @hasanyrole('admin|petugas_admin')
                    <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Sirkulasi
                    </p>
                    <a href="{{ route('circulation.borrow.create') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('circulation.borrow.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-hand-holding-heart w-4 text-center {{ request()->routeIs('circulation.borrow.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Peminjaman
                    </a>
                    <a href="{{ route('circulation.return.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('circulation.return.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-rotate-left w-4 text-center {{ request()->routeIs('circulation.return.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Pengembalian
                    </a>
                    <a href="{{ route('circulation.fines.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('circulation.fines.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-money-bill-wave w-4 text-center {{ request()->routeIs('circulation.fines.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Pembayaran Denda
                    </a>
                    <a href="{{ route('circulation.visitors.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('circulation.visitors.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-door-open w-4 text-center {{ request()->routeIs('circulation.visitors.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Buku Tamu
                    </a>
                @endhasanyrole

                @hasanyrole('admin|petugas_buku')
                    <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Koleksi
                        Buku</p>
                    <a href="{{ route('books.books.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('books.books.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-book w-4 text-center {{ request()->routeIs('books.books.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Katalog Buku
                    </a>
                    <a href="{{ route('books.stocks.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('books.stocks.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-layer-group w-4 text-center {{ request()->routeIs('books.stocks.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Eksemplar &amp; Stok
                    </a>
                    <a href="{{ route('books.barcode.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('books.barcode.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-barcode w-4 text-center {{ request()->routeIs('books.barcode.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Cetak Barcode
                    </a>
                    <a href="{{ route('books.stock-opname.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('books.stock-opname.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-clipboard-check w-4 text-center {{ request()->routeIs('books.stock-opname.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Stock Opname
                    </a>
                @endhasanyrole

                @hasanyrole('siswa')
                    <p class="px-3 pt-4 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Layanan
                        Anggota</p>
                    <a href="{{ route('opac.index') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('opac.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-magnifying-glass w-4 text-center {{ request()->routeIs('opac.*') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        OPAC (Katalog)
                    </a>
                    <a href="{{ route('member.card') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('member.card') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-id-card w-4 text-center {{ request()->routeIs('member.card') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Kartu Anggota Digital
                    </a>
                    <a href="{{ route('member.history') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('member.history') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('member.history') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Riwayat Pinjam
                    </a>
                    <a href="{{ route('member.bookings') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('member.bookings') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-bookmark w-4 text-center {{ request()->routeIs('member.bookings') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Booking Buku
                    </a>
                    <a href="{{ route('member.fines') }}"
                        class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium {{ request()->routeIs('member.fines') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-700/60 hover:text-white' }} transition-all duration-200">
                        <i
                            class="fa-solid fa-triangle-exclamation w-4 text-center {{ request()->routeIs('member.fines') ? 'text-white' : 'text-slate-500 group-hover:text-indigo-400' }}"></i>
                        Info Denda
                    </a>
                @endhasanyrole
            </nav>

            {{-- Sidebar Footer --}}
            <div class="border-t border-slate-700/60 p-3">
                <div class="flex items-center gap-3 rounded-xl bg-slate-800/60 px-3 py-2.5">
                    <div
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 text-xs font-bold text-white shadow">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-semibold text-white">{{ auth()->user()?->name ?? 'Pengguna' }}
                        </p>
                        <p class="truncate text-[10px] text-slate-400">
                            @php
                                $displayRole = 'Tanpa Peran';
                                if (auth()->check()) {
                                    try {
                                        $roles = auth()->user()->getRoleNames();
                                        $displayRole = is_object($roles)
                                            ? $roles->first() ?? 'Tanpa Peran'
                                            : $roles[0] ?? 'Tanpa Peran';
                                    } catch (\Throwable $e) {
                                        $displayRole = 'Tanpa Peran';
                                    }
                                }
                            @endphp
                            {{ $displayRole }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar"
                            class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-slate-700 hover:text-red-400">
                            <i class="fa-solid fa-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex flex-1 flex-col lg:pl-64 min-w-0">

            {{-- TOP NAVBAR --}}
            <header
                class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-slate-200/80 bg-white/90 backdrop-blur-md px-4 sm:px-6 shadow-sm">
                <div class="flex items-center gap-4 min-w-0">
                    <button onclick="toggleSidebar()"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors lg:hidden">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-base font-semibold text-slate-800">@yield('page_title', 'Dashboard')</h1>
                        @hasSection('breadcrumb')
                            <nav aria-label="Breadcrumb"
                                class="hidden sm:flex items-center gap-1 text-xs text-slate-400">@yield('breadcrumb')
                            </nav>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Spotlight Search Button --}}
                    <button onclick="openSpotlight()" title="Cari Cepat (Ctrl + K)"
                        class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-700 text-xs font-medium transition-all shadow-sm">
                        <i class="fa-solid fa-magnifying-glass text-indigo-500"></i>
                        <span>Pencarian Cepat...</span>
                        <kbd
                            class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white rounded border border-slate-200 shadow-2xs">Ctrl
                            K</kbd>
                    </button>
                    <button onclick="openSpotlight()" title="Cari Cepat"
                        class="md:hidden flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-base"></i>
                    </button>

                    <div class="relative">
                        <button id="notif-btn" onclick="toggleNotifications()"
                            class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                            <i class="fa-regular fa-bell text-base"></i>
                            <span
                                class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        </button>
                        <div id="notif-dropdown"
                            class="absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 hidden">
                            <div class="p-4 border-b border-slate-100">
                                <p class="font-semibold text-sm text-slate-800">Notifikasi</p>
                            </div>
                            <div class="p-2 max-h-64 overflow-y-auto">
                                <div
                                    class="flex items-start gap-3 rounded-xl p-3 hover:bg-slate-50 transition-colors cursor-pointer">
                                    <div
                                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600 text-xs">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-slate-800">3 Buku Melewati Batas Waktu</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Harap segera diproses
                                            pengembaliannya.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 border-t border-slate-100">
                                <a href="#"
                                    class="block text-center text-xs font-medium text-indigo-600 hover:text-indigo-700 py-2">Lihat
                                    semua notifikasi</a>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <button id="user-menu-btn" onclick="toggleUserMenu()"
                            class="flex items-center gap-2 rounded-xl py-1.5 pl-1.5 pr-3 hover:bg-slate-100 transition-colors">
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 text-xs font-bold text-white shadow">
                                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                            </div>
                            <span
                                class="hidden sm:block text-sm font-medium text-slate-700 max-w-28 truncate">{{ auth()->user()?->name ?? 'Pengguna' }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                        </button>
                        <div id="user-menu-dropdown"
                            class="absolute right-0 mt-2 w-52 rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 hidden">
                            <div class="p-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-800 truncate">
                                    {{ auth()->user()?->name ?? 'Pengguna' }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ auth()->user()?->email ?? '' }}</p>
                            </div>
                            <div class="p-1.5">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                    <i class="fa-regular fa-user w-4 text-center text-slate-400"></i> Profil Saya
                                </a>
                            </div>
                            <div class="p-1.5 border-t border-slate-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div id="flash-success"
                        class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm">
                        <i class="fa-solid fa-circle-check text-green-500 flex-shrink-0"></i>
                        <span>{{ session('success') }}</span>
                        <button onclick="document.getElementById('flash-success').remove()"
                            class="ml-auto text-green-400 hover:text-green-600"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif
                @if (session('error'))
                    <div id="flash-error"
                        class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0"></i>
                        <span>{{ session('error') }}</span>
                        <button onclick="document.getElementById('flash-error').remove()"
                            class="ml-auto text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                @endif
                @yield('content')
            </main>

            {{-- SPOTLIGHT SEARCH MODAL --}}
            <div id="spotlight-modal"
                class="fixed inset-0 z-50 flex items-start justify-center pt-16 px-4 bg-slate-900/60 backdrop-blur-sm hidden"
                onclick="if(event.target === this) closeSpotlight()">
                <div
                    class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden ring-1 ring-slate-200 transform transition-all">
                    <div class="flex items-center px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 mr-3 text-sm"></i>
                        <input type="text" id="spotlight-input" oninput="debounceSearch()"
                            placeholder="Cari judul buku, pengarang, ISBN, nama anggota, atau kode transaksi..."
                            class="w-full bg-transparent text-sm text-slate-800 focus:outline-none placeholder-slate-400 font-medium">
                        <kbd
                            class="hidden sm:inline-block px-2 py-0.5 text-[10px] font-semibold text-slate-400 bg-slate-200/60 rounded">ESC</kbd>
                    </div>

                    <div id="spotlight-results" class="p-4 max-h-[60vh] overflow-y-auto space-y-4">
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Ketik minimal 2 karakter untuk mencari seluruh data perpustakaan...
                        </div>
                    </div>

                    <div
                        class="px-4 py-2 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Gunakan <kbd class="px-1 py-0.5 bg-slate-200 rounded text-slate-600 font-mono">Ctrl</kbd>
                            + <kbd class="px-1 py-0.5 bg-slate-200 rounded text-slate-600 font-mono">K</kbd> untuk
                            membuka pencarian dari mana saja</span>
                        <span>SIPerpus QuickSearch</span>
                    </div>
                </div>
            </div>

            <script>
                function toggleSidebar() {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebar-overlay');
                    const isHidden = sidebar.classList.contains('-translate-x-full');
                    if (isHidden) {
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    }
                }

                function toggleNotifications() {
                    document.getElementById('notif-dropdown').classList.toggle('hidden');
                    document.getElementById('user-menu-dropdown').classList.add('hidden');
                }

                function toggleUserMenu() {
                    document.getElementById('user-menu-dropdown').classList.toggle('hidden');
                    document.getElementById('notif-dropdown').classList.add('hidden');
                }

                document.addEventListener('click', function(e) {
                    const notifBtn = document.getElementById('notif-btn');
                    const userBtn = document.getElementById('user-menu-btn');
                    if (notifBtn && !notifBtn.contains(e.target) && !document.getElementById('notif-dropdown').contains(e
                            .target)) {
                        document.getElementById('notif-dropdown').classList.add('hidden');
                    }
                    if (userBtn && !userBtn.contains(e.target) && !document.getElementById('user-menu-dropdown').contains(e
                            .target)) {
                        document.getElementById('user-menu-dropdown').classList.add('hidden');
                    }
                });

                // Spotlight Search Functions
                function openSpotlight() {
                    const modal = document.getElementById('spotlight-modal');
                    const input = document.getElementById('spotlight-input');
                    modal.classList.remove('hidden');
                    setTimeout(() => input.focus(), 50);
                }

                function closeSpotlight() {
                    document.getElementById('spotlight-modal').classList.add('hidden');
                }

                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        const modal = document.getElementById('spotlight-modal');
                        if (modal.classList.contains('hidden')) {
                            openSpotlight();
                        } else {
                            closeSpotlight();
                        }
                    }
                    if (e.key === 'Escape') {
                        closeSpotlight();
                    }
                });

                let searchTimeout = null;

                function debounceSearch() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(performSpotlightSearch, 300);
                }

                async function performSpotlightSearch() {
                    const query = document.getElementById('spotlight-input').value.trim();
                    const resultsContainer = document.getElementById('spotlight-results');

                    if (query.length < 2) {
                        resultsContainer.innerHTML =
                            '<div class="text-center py-8 text-slate-400 text-xs">Ketik minimal 2 karakter untuk mencari...</div>';
                        return;
                    }

                    resultsContainer.innerHTML =
                        '<div class="text-center py-8 text-indigo-500 text-xs"><i class="fa-solid fa-circle-notch fa-spin text-base mr-2"></i>Mencari data...</div>';

                    try {
                        const response = await fetch(`{{ route('quick-search') }}?q=${encodeURIComponent(query)}`, {
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            resultsContainer.innerHTML =
                                '<div class="text-center py-8 text-red-500 text-xs">Terjadi kesalahan saat mencari. Silakan coba lagi.</div>';
                            return;
                        }

                        const data = await response.json();

                        let html = '';

                        if (data.books && data.books.length > 0) {
                            html +=
                                `<div class="space-y-1">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 px-2">Katalog Buku (${data.books.length})</p>`;
                            data.books.forEach(book => {
                                html += `<a href="{{ url('/opac') }}/${book.id}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-indigo-50/80 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 text-xs font-bold">
                                    <i class="fa-solid fa-book"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 group-hover:text-indigo-600">${escapeHtml(book.title)}</p>
                                    <p class="text-[11px] text-slate-400">Pengarang: ${escapeHtml(book.author ?? '-')} | ISBN: ${escapeHtml(book.isbn ?? '-')}</p>
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-indigo-500"></i>
                        </a>`;
                            });
                            html += `</div>`;
                        }

                        if (data.users && data.users.length > 0) {
                            html +=
                                `<div class="space-y-1 pt-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 px-2">Pengguna / Anggota (${data.users.length})</p>`;
                            data.users.forEach(u => {
                                html += `<div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 text-xs font-bold">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">${escapeHtml(u.name)}</p>
                                    <p class="text-[11px] text-slate-400">Email: ${escapeHtml(u.email ?? '-')} | ID Anggota: ${escapeHtml(u.member_id ?? '-')}</p>
                                </div>
                            </div>
                        </div>`;
                            });
                            html += `</div>`;
                        }

                        if (data.transactions && data.transactions.length > 0) {
                            html +=
                                `<div class="space-y-1 pt-2">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 px-2">Transaksi Peminjaman (${data.transactions.length})</p>`;
                            data.transactions.forEach(t => {
                                const bookTitle = t.book_stock?.book?.title ?? 'Buku';
                                const userName = t.user?.name ?? 'Anggota';
                                html += `<div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-amber-50/80 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600 text-xs font-bold">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">Kode: ${escapeHtml(t.transaction_code)} &bull; Status: <span class="capitalize">${escapeHtml(t.status)}</span></p>
                                    <p class="text-[11px] text-slate-400">Peminjam: ${escapeHtml(userName)} | Buku: ${escapeHtml(bookTitle)}</p>
                                </div>
                            </div>
                        </div>`;
                            });
                            html += `</div>`;
                        }

                        if (html === '') {
                            html =
                                '<div class="text-center py-8 text-slate-400 text-xs">Tidak ada hasil yang cocok dengan kata kunci tersebut.</div>';
                        }

                        resultsContainer.innerHTML = html;
                    } catch (err) {
                        resultsContainer.innerHTML =
                            '<div class="text-center py-8 text-rose-500 text-xs">Gagal memuat hasil pencarian.</div>';
                    }
                }

                function escapeHtml(str) {
                    if (!str) return '';
                    return String(str)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }
            </script>
            @stack('scripts')
</body>

</html>
