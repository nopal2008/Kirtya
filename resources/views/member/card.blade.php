@extends('layouts.app')

@section('title', 'Kartu Anggota Digital')
@section('page_title', 'Kartu Anggota Digital Perpustakaan')

@section('content')
<div class="max-w-md mx-auto space-y-6">

    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-800">Kartu Anggota Digital</h2>
        <p class="text-xs text-slate-500 mt-0.5">Tunjukkan kartu digital ini kepada petugas perpustakaan saat meminjam buku.</p>
    </div>

    {{-- Premium Digital Card Component --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-2xl ring-1 ring-slate-700/50">

        {{-- Background pattern --}}
        <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-indigo-500/10 blur-2xl"></div>
        <div class="absolute -left-12 -bottom-12 h-40 w-40 rounded-full bg-blue-500/10 blur-2xl"></div>

        {{-- Card Header --}}
        <div class="relative z-10 flex items-center justify-between border-b border-slate-700/60 pb-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-md">
                    <i class="fa-solid fa-book-open text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-extrabold tracking-tight">KARTU PERPUSTAKAAN</p>
                    <p class="text-[9px] uppercase tracking-widest text-indigo-300">SIPerpus Digital Identity</p>
                </div>
            </div>
            <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-[10px] font-bold text-emerald-400 border border-emerald-500/30">
                AKTIF
            </span>
        </div>

        {{-- Card Body --}}
        <div class="relative z-10 py-6 flex items-center gap-5">
            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-2xl font-extrabold text-white shadow-lg ring-2 ring-white/20">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="min-w-0 flex-1 space-y-1">
                <p class="text-xs text-indigo-300 uppercase tracking-widest font-semibold">Nama Anggota</p>
                <h3 class="text-lg font-bold text-white truncate">{{ $user->name }}</h3>
                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                <div class="pt-1">
                    <span class="font-mono text-xs font-bold text-indigo-300 bg-indigo-900/60 px-2.5 py-1 rounded-lg border border-indigo-700/50">
                        {{ $user->member_id ?? 'SWA-2025-001' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Card Barcode Footer --}}
        <div class="relative z-10 pt-4 border-t border-slate-700/60 flex flex-col items-center">
            <div class="h-10 w-full flex items-center justify-center gap-1 bg-white/10 backdrop-blur-md rounded-xl text-white px-4">
                <i class="fa-solid fa-barcode text-3xl tracking-widest text-indigo-300"></i>
            </div>
            <p class="text-[10px] font-mono text-slate-400 mt-1">ID BARCODE: {{ $user->member_id ?? 'SWA-2025-001' }}</p>
        </div>

    </div>

    <div class="text-center no-print">
        <button onclick="window.print()" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
            <i class="fa-solid fa-print mr-1"></i> Cetak Kartu
        </button>
    </div>

</div>
@endsection
