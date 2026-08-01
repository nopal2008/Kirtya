@extends('layouts.app')

@section('title', 'Dashboard')
@section('meta_description', 'Ringkasan statistik dan aktivitas terkini Sistem Informasi Perpustakaan.')

@section('page_title', 'Dashboard')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-slate-600 transition-colors">Beranda</a>
    <i class="fa-solid fa-chevron-right text-[8px]"></i>
    <span class="text-slate-600 font-medium">Dashboard</span>
@endsection

@section('content')

    {{-- ============================================================== --}}
    {{-- GREETING HEADER --}}
    {{-- ============================================================== --}}
    <div class="mb-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm text-slate-500 font-medium">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
                <h2 class="mt-0.5 text-2xl font-bold text-slate-900 tracking-tight">
                    Selamat datang, <span
                        class="text-indigo-600">{{ Str::words(auth()->user()?->name ?? 'Pengunjung', 1, '') }}</span>
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Berikut adalah ringkasan aktivitas perpustakaan hari ini.
                </p>
            </div>

            @hasanyrole('admin|petugas_admin')
                <div class="flex items-center gap-2">
                    <a href="{{ route('circulation.borrow.create') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all duration-150">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Tambah Peminjaman
                    </a>
                    <a href="{{ route('circulation.return.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-200 shadow-sm hover:bg-slate-50 active:scale-95 transition-all duration-150">
                        <i class="fa-solid fa-rotate-left text-xs text-slate-400"></i>
                        Proses Pengembalian
                    </a>
                </div>
            @endhasanyrole
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- STAT CARDS (Grid 4 kolom) --}}
    {{-- ============================================================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-7">

        {{-- Card 1: Total Koleksi Buku --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Koleksi</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">
                        {{ number_format($stats['total_books']) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ number_format($stats['total_stocks']) }} eksemplar tersedia
                    </p>
                </div>
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-50 group-hover:bg-indigo-100 transition-colors">
                    <i class="fa-solid fa-book-open text-indigo-600 text-lg"></i>
                </div>
            </div>
            {{-- Trend indicator --}}
            <div class="mt-4 flex items-center gap-1.5">
                <span
                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-700">
                    <i class="fa-solid fa-arrow-up text-[9px]"></i> 12%
                </span>
                <span class="text-[11px] text-slate-400">dari bulan lalu</span>
            </div>
            {{-- Decorative bar --}}
            <div
                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

        {{-- Card 2: Anggota Aktif --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Anggota Aktif</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">
                        {{ number_format($stats['total_active_members']) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Siswa terdaftar aktif</p>
                </div>
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-sky-50 group-hover:bg-sky-100 transition-colors">
                    <i class="fa-solid fa-users text-sky-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                <span
                    class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-700">
                    <i class="fa-solid fa-arrow-up text-[9px]"></i> 5%
                </span>
                <span class="text-[11px] text-slate-400">dari bulan lalu</span>
            </div>
            <div
                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-sky-500 to-cyan-400 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

        {{-- Card 3: Buku Dipinjam --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sedang Dipinjam</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">
                        {{ number_format($stats['total_borrowed']) }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        <span class="font-semibold text-red-500">{{ number_format($stats['total_overdue']) }}</span>
                        melewati batas waktu
                    </p>
                </div>
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-amber-50 group-hover:bg-amber-100 transition-colors">
                    <i class="fa-solid fa-hand-holding-heart text-amber-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                @if ($stats['total_overdue'] > 0)
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">
                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> {{ $stats['total_overdue'] }} Terlambat
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-700">
                        <i class="fa-solid fa-check text-[9px]"></i> Semua tepat waktu
                    </span>
                @endif
            </div>
            <div
                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-orange-400 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

        {{-- Card 4: Total Denda Belum Dibayar --}}
        <div
            class="group relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/80 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Denda Aktif</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">
                        Rp {{ number_format($stats['total_unpaid_fines'], 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Lunas hari ini:
                        <span class="font-semibold text-green-600">Rp
                            {{ number_format($stats['total_fines_today'], 0, ',', '.') }}</span>
                    </p>
                </div>
                <div
                    class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-rose-50 group-hover:bg-rose-100 transition-colors">
                    <i class="fa-solid fa-money-bill-wave text-rose-600 text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5">
                @if ($stats['total_unpaid_fines'] > 0)
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-semibold text-rose-700">
                        <i class="fa-solid fa-circle-dot text-[9px]"></i> Perlu ditindaklanjuti
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-700">
                        <i class="fa-solid fa-check text-[9px]"></i> Semua denda lunas
                    </span>
                @endif
            </div>
            <div
                class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

    </div>
    {{-- END STAT CARDS --}}

    {{-- ============================================================== --}}
    {{-- MAIN CONTENT GRID --}}
    {{-- ============================================================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- ---------------------------------------------------------- --}}
        {{-- TABEL TRANSAKSI TERBARU (2/3 lebar) --}}
        {{-- ---------------------------------------------------------- --}}
        <div class="xl:col-span-2">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Aktivitas Transaksi Terbaru</h3>
                        <p class="text-xs text-slate-400 mt-0.5">10 sirkulasi peminjaman terakhir</p>
                    </div>
                    <a href="{{ route('circulation.transactions.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                        Lihat Semua
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70">
                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    Kode Transaksi
                                </th>
                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    Anggota
                                </th>
                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    Buku
                                </th>
                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    Jatuh Tempo
                                </th>
                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentTransactions as $trx)
                                <tr class="hover:bg-slate-50/60 transition-colors duration-100">

                                    {{-- Kode Transaksi --}}
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span
                                            class="font-mono text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-lg px-2 py-1">
                                            {{ $trx->transaction_code }}
                                        </span>
                                    </td>

                                    {{-- Anggota --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div
                                                class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 text-[10px] font-bold text-white">
                                                {{ strtoupper(substr($trx->user->name ?? 'U', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate max-w-[120px] text-xs font-semibold text-slate-800">
                                                    {{ $trx->user->name ?? '-' }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 font-mono">
                                                    {{ $trx->user->member_id ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Judul Buku --}}
                                    <td class="px-5 py-3.5">
                                        <p class="truncate max-w-[180px] text-xs font-medium text-slate-700">
                                            {{ $trx->bookStock->book->title ?? '-' }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-mono">
                                            {{ $trx->bookStock->barcode ?? '-' }}
                                        </p>
                                    </td>

                                    {{-- Jatuh Tempo --}}
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        @if ($trx->return_date)
                                            <span class="text-xs text-slate-500">
                                                {{ $trx->return_date->format('d M Y') }}
                                            </span>
                                        @else
                                            <span
                                                class="text-xs {{ $trx->due_date->isPast() ? 'font-semibold text-red-600' : 'text-slate-600' }}">
                                                {{ $trx->due_date->format('d M Y') }}
                                            </span>
                                            @if ($trx->due_date->isPast() && $trx->status !== 'returned')
                                                <p class="text-[10px] font-semibold text-red-500">
                                                    +{{ $trx->overdue_days }} hari
                                                </p>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- Badge Status --}}
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $trx->status_badge_class }}">
                                            @if ($trx->status === 'borrowed')
                                                <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                                            @elseif ($trx->status === 'returned')
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                            @elseif ($trx->status === 'overdue')
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-600 animate-pulse"></span>
                                            @else
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                            @endif
                                            {{ $trx->status_label }}
                                        </span>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                                <i class="fa-solid fa-inbox text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-600">Belum Ada Transaksi</p>
                                                <p class="text-xs text-slate-400 mt-1">Aktivitas sirkulasi akan muncul di
                                                    sini.</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        {{-- END TABEL TRANSAKSI --}}

        {{-- ---------------------------------------------------------- --}}
        {{-- PANEL KANAN (1/3 lebar) --}}
        {{-- ---------------------------------------------------------- --}}
        <div class="flex flex-col gap-6">

            {{-- DENDA BELUM DIBAYAR --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">Denda Belum Dibayar</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Nominal terbesar</p>
                    </div>
                    @hasanyrole('admin|petugas_admin')
                        <a href="{{ route('circulation.fines.index') }}"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                            Kelola
                        </a>
                    @endhasanyrole
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($pendingFines as $fine)
                        <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50/60 transition-colors">
                            <div
                                class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-rose-50 text-[10px] font-bold text-rose-600">
                                {{ strtoupper(substr($fine->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-slate-800">{{ $fine->user->name ?? '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400">{{ $fine->type_label }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-bold text-rose-600">
                                    Rp {{ number_format($fine->amount, 0, ',', '.') }}
                                </p>
                                <span
                                    class="inline-block rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $fine->payment_status_badge_class }}">
                                    {{ $fine->payment_status_label }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center gap-2 px-5 py-8 text-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-50 text-green-500">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                            </div>
                            <p class="text-xs font-medium text-slate-600">Tidak ada denda aktif</p>
                            <p class="text-[11px] text-slate-400">Semua denda telah dibayar.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- RINGKASAN STATUS STOK --}}
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-800">Status Stok Buku</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Distribusi eksemplar saat ini</p>
                </div>
                <div class="p-5 space-y-4">

                    {{-- Tersedia --}}
                    @php
                        $totalStocks = $stats['total_stocks'] ?: 1;
                        $availablePct = round(($stats['available_stocks'] / $totalStocks) * 100);
                        $borrowedStocks = $stats['total_borrowed'];
                        $borrowedPct = round(($borrowedStocks / $totalStocks) * 100);
                        $otherStocks = $totalStocks - $stats['available_stocks'] - $borrowedStocks;
                        $otherPct = 100 - $availablePct - $borrowedPct;
                    @endphp

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-700">Tersedia</span>
                            <span
                                class="text-xs font-bold text-emerald-700">{{ number_format($stats['available_stocks']) }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-green-500 transition-all duration-700"
                                style="width: {{ $availablePct }}%"></div>
                        </div>
                        <p class="mt-1 text-right text-[10px] text-slate-400">{{ $availablePct }}% dari total</p>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-700">Dipinjam</span>
                            <span class="text-xs font-bold text-blue-700">{{ number_format($borrowedStocks) }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 transition-all duration-700"
                                style="width: {{ $borrowedPct }}%"></div>
                        </div>
                        <p class="mt-1 text-right text-[10px] text-slate-400">{{ $borrowedPct }}% dari total</p>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-700">Lainnya (Rusak/Hilang)</span>
                            <span class="text-xs font-bold text-slate-600">{{ number_format($otherStocks) }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-slate-300 to-slate-400 transition-all duration-700"
                                style="width: {{ max(0, $otherPct) }}%"></div>
                        </div>
                        <p class="mt-1 text-right text-[10px] text-slate-400">{{ max(0, $otherPct) }}% dari total</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-600">Total Eksemplar</span>
                            <span
                                class="text-sm font-extrabold text-slate-900">{{ number_format($stats['total_stocks']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- END PANEL KANAN --}}

    </div>
    {{-- END MAIN CONTENT GRID --}}

@endsection

@push('scripts')
    <script>
        // Animasikan progress bar saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const bars = document.querySelectorAll('[style*="width:"]');
            bars.forEach(function(bar) {
                const targetWidth = bar.style.width;
                bar.style.width = '0%';
                requestAnimationFrame(function() {
                    setTimeout(function() {
                        bar.style.width = targetWidth;
                    }, 100);
                });
            });
        });
    </script>
@endpush
