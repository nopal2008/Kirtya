@extends('layouts.app')

@section('title', 'Pengembalian Buku')
@section('page_title', 'Proses Pengembalian Buku')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-800">Sirkulasi Pengembalian Buku</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pilih peminjaman aktif untuk memproses pengembalian &amp; penghitungan denda otomatis.</p>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/80">
        <form method="GET" action="{{ route('circulation.return.index') }}">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Kode Transaksi, Barcode, atau ID Anggota..."
                   class="w-full max-w-md rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
        </form>
    </div>

    {{-- Table of Active Borrowings --}}
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3.5">Kode Transaksi</th>
                        <th class="px-5 py-3.5">Peminjam</th>
                        <th class="px-5 py-3.5">Buku &amp; Barcode</th>
                        <th class="px-5 py-3.5">Jatuh Tempo</th>
                        <th class="px-5 py-3.5 text-right">Proses Pengembalian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($activeLoans as $trx)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-5 py-3.5 font-mono font-bold text-indigo-700">
                            {{ $trx->transaction_code }}
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-bold text-slate-800">{{ $trx->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $trx->user->member_id ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-800 line-clamp-1">{{ $trx->bookStock->book->title ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $trx->bookStock->barcode ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-semibold {{ $trx->due_date->isPast() ? 'text-red-600' : 'text-slate-700' }}">
                                {{ $trx->due_date->format('d M Y') }}
                            </p>
                            @if ($trx->due_date->isPast())
                            <span class="text-[10px] font-bold text-red-500">+{{ $trx->overdue_days }} hari terlambat</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <form method="POST" action="{{ route('circulation.return.process', $trx) }}" class="inline-flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="return_date" value="{{ date('Y-m-d') }}">
                                <select name="book_condition" class="rounded-xl border border-slate-200 text-xs px-2.5 py-1.5 bg-slate-50">
                                    <option value="good">Kondisi Baik</option>
                                    <option value="damaged">Kondisi Rusak</option>
                                    <option value="lost">Kondisi Hilang</option>
                                </select>
                                <button type="submit" class="rounded-xl bg-green-600 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-green-700">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Kembalikan
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-slate-400">Tidak ada transaksi peminjaman aktif saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($activeLoans->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $activeLoans->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
